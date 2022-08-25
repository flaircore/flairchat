<?php

/**
 * Plugin Name:       Flair Chat
 * Plugin URI:        flair-chat
 * Description:       Real time chat feature for wordpress.
 * Requires at least: 5.9
 * Requires PHP:      7.0
 * Version:           1.0.0
 * Author:            Nicholas Babu
 * Author URI:        https://profiles.wordpress.org/bahson/
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       flair-chat
 *
 * @package           flair-chat
 */

if (!defined('ABSPATH')) {
    exit();
}

require_once __DIR__ . '/vendor/autoload.php';

use Flair\Chat\AppVars;
use Flair\Chat\PusherClient;

class Flair_Chat
{

    const NONCE_ACTION = 'wp_rest';

    private string $plugin;

    protected \Pusher\Pusher $pusher;

    protected $pusher_details;

    public function __construct()
    {

        add_action('wp_footer', array($this, 'attach_chat_block'));

        // On install or update
        register_activation_hook(__FILE__, array($this, 'create_db_tables'));

        register_deactivation_hook(__FILE__, array($this, 'flair_chat_cleanup'));

        require_once plugin_dir_path(__FILE__) . 'templates/admin.php';


        // Add shortcode
        //add_shortcode('todo-list-form', array($this, 'load_short_code'));

        // @TODO add if user is admin only
        $this->plugin = plugin_basename(__FILE__);
        add_filter("plugin_action_links_$this->plugin", array($this, 'settings_links'));

        add_action('rest_api_init', array($this, 'register_routes_api'));

        $this->flair_chat_setup();
    }

    public function flair_chat_setup()
    {
        $pusher_details = new AppVars();
        $pusher_details = $pusher_details->getPusherDetails();

        $pusher = new PusherClient();
        $this->pusher = $pusher->pusherInstance($pusher_details);
        $this->pusher_details = $pusher_details;
    }

    public function settings_links($links)
    {
        $settings_link = '<a href="admin.php?page=flair-chat-settings-page">Configuration</a>';
        $links[]       = $settings_link;
        return $links;
    }

//    public function add_admin_pages()
//    {
//        add_menu_page(
//            'FlairChat plugin',
//            'FlairChat Config',
//            'manage_options',
//        'flair_chat_settings_page',
//        array($this, 'admin_index'),
//        'dashicons-admin-generic',
//        110);
//    }
//
//    public function admin_index()
//    {
//        require_once plugin_dir_path(__FILE__) . 'templates/admin.php';
//    }

    public function attach_chat_block(): void
    {

        $current_uid = get_current_user_id();

        // $TODO for guests
        // Nothing for guest users for now.
        if (!$current_uid) {
            return;
        }

        $js_src = plugin_dir_url(__FILE__) .'/dist/chat_nv.bundle.js';
        $new_msg_url = get_rest_url(null, 'api/v1/flair-chat/send-message');
        $messages_url = get_rest_url(null, 'api/v1/flair-chat/messages/');
        $nonce = wp_create_nonce(self::NONCE_ACTION);
        $headers = ['X-WP-Nonce' => $nonce];


        $styles_src = plugin_dir_url(__FILE__) .'/dist/chat_nv.css';

        wp_enqueue_script(
            'flair-chat',
            $js_src,
            array(),
            1,
            true
        );

        wp_enqueue_style('flair-chat', $styles_src);

        $flair_chat_items = array(
            'app_key' => $this->pusher_details['key'],
            'cluster' => $this->pusher_details['cluster'],
            'presence_url' => '@TODO',
            'messages_url' => $messages_url,
            'new_msg_url' => $new_msg_url,
            'current_id' => $current_uid,
            'headers' => $headers,
        );


        wp_add_inline_script('flair-chat', 'var flairChatData = ' . wp_json_encode($flair_chat_items), 'before');
        wp_add_inline_style('flair-chat', $styles_src);

        echo $this->chat_block_template($current_uid);
    }

    public function create_db_tables(): void
    {
        global $wpdb;
        $messages_table = $wpdb->prefix . "flair_chat_messages";
        $charset = $wpdb->get_charset_collate();

        $msg_sql = "CREATE TABLE $messages_table(
 		id mediumint NOT NULL AUTO_INCREMENT,
 		message text NOT NULL,
 		from_uid bigint NOT NULL,
 		to_uid bigint NOT NULL,
 		is_read tinyint DEFAULT 0 NOT NULL,
 		created_at timestamp DEFAULT CURRENT_TIMESTAMP,
 		PRIMARY KEY (id)
 	    )$charset;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($msg_sql);
    }

    public function chat_block_template($current_uid): string
    {
        $args = array(
            // @TODO for roles from get_option() settings
            //'role__not_in' => 'administrator',
            'exclude'  => array('id' => $current_uid),
            );
        global $wpdb;
        // Unread messages
        $table = $wpdb->prefix . "flair_chat_messages";
        $user_query = new WP_User_Query($args);
        $div_weight = 4;
        $total_unread = [
            'total' => 0
        ];

        $missing_image_uri = plugin_dir_url(__FILE__) .'/images/missing_image.svg';
        ;
        $users_list = "";

        //$faker = new \Flair\Chat\Faker\MessageFactory();
        //dump($faker->generateFakeMessages());
        $users = $user_query->get_results();

        $user_ids = [];
        if (! empty($users)) {
            /** @var WP_User $user */
            foreach ($users as $user) {
                $user_ids[] = $user->ID;
            }
        }


        $user_ids = implode(',', $user_ids);
        $unread_messages = $wpdb->get_results("
                  SELECT
                      message, from_uid, to_uid, is_read,
                  COUNT(message)
                  FROM
                      $table
                   WHERE
                     to_uid = $current_uid
                   AND
                     is_read = false
                   AND from_uid IN ($user_ids)
                   
                GROUP BY from_uid");


        if ($unread_messages) {
            foreach ($unread_messages as $unread_message) {
                $unread_array = (array)$unread_message;
                //dump($unread_array);
                $total_unread[] = [
                    'from_uid' => (int)$unread_array['from_uid'],
                    'total' => $unread_array['COUNT(message)'],
                ];

                $total_unread['total'] = $total_unread['total'] + $unread_array['COUNT(message)'];
            }
        }


        // Template
        if (! empty($users)) {
            /** @var WP_User $user */
            foreach ($users as $user) {
                $user_div_data = "
                    <div class='user' id='$user->ID'>
                        <div class='user-content'>
                            <div class='user-image'>
                                <img 
                                    src='$missing_image_uri'
                                    alt='User image'
                                    loading='lazy'
                                    onerror='this.onerror=null;this.src='$missing_image_uri';'
                                />                            
                            </div> 
                             
                            <div class='details'>
                                <p class='display-name'>$user->display_name</p>                            
                            </div>   
                            <div class='unread'>
                                <span class='icon pending'>
                                    <span class='unread-count'>
                                        ".$this->unread_total_count($total_unread, $user->ID)."
                                    </span>    
                                </span>                        
                            </div>                                                    
                        </div>                                        
                    </div>";

                if ($users_list) {
                    $users_list .= $user_div_data;
                } else {
                    $users_list = $user_div_data;
                }
            }
        } else {
            $users_list .= 'No users found.';
        }

        // Chat controls.
        $chat_controls = "<div class='chat-controls'>
                            <section>
                               <div class='icon go-back no-show' title='Go back'>                           
                               </div>
                               
                               <div class='icon info' title='Total unread'>
                                    <span class='total'>".$total_unread['total']."</span>                           
                               </div> 
                               
                               <div class='icon maximize' title='Maximize chat view.'>                           
                               </div> 
                               
                               <div class='icon minimize no-show' 
                               		title='Minimize chat view.'>                           
                               </div> 
                            </section>
                     </div>";

        // User and message listings.
        $chat_block =
            "<div class='chat-block no-show'>
                <div class='users-wrapper'>
                    <div class='header'>
                        <div class='user-image'>
                            <img 
                                src='$missing_image_uri'
                                alt='User image'
                                loading='lazy'
                                onerror='this.onerror=null;this.src='$missing_image_uri';'
                            />                        
                        </div> 
                        <div class='user-nav'>
                            <span>Users </span>                     
                        </div>                    
                    </div>  
                    <div class='users-list'>
                        .$users_list.                     
                    </div>
                </div>
                 
                 <div class='messages-view no-show'>
                    <div class='header'>
                        <div class='user-image'>
                            <img 
                                src='$missing_image_uri'
                                alt='User image'
                                loading='lazy'
                                onerror='this.onerror=null;this.src='$missing_image_uri';'
                            />                        
                        </div>
                        <div class='chat-with'> Chat with: <span class='username'> ... </span></div>                  
                    </div>
                        
                    <div class='message-list' id='messages'>
                    
                    </div>                    
                    <div class='chat-input'>
                        <input type='text'
                                name='message'
                                class='submit'
                                placeholder='Type a message...'
                                title='Message input'
                        >
                    </div>                
                </div>
            </div>";

        $content = "<section id='chat-block-main' class='children-hidden' style='z-index: $div_weight'>
                        $chat_controls
                        $chat_block
                    ";


        $content .= "</section>";

        return $content;
    }

    private function unread_total_count(&$total_unread, $user_id)
    {

        $value = array_filter($total_unread, function ($item) use ($user_id) {
           //dump($item['total']);

            if (isset($item['from_uid']) && $item['from_uid'] === $user_id) {
                return $item;
            } else {
                return [];
            }
        });

        if (!array_values($value)) {
            return '0';
        }

        return array_values($value)[0]['total'];
    }

    public function register_routes_api(): void
    {
        register_rest_route('api/v1', 'flair-chat/messages/(?P<id>[a-zA-Z0-9-]+)', array(
                'methods' => 'GET',
                'callback' => array($this, 'get_messages')
        ));

        register_rest_route('api/v1', 'flair-chat/send-message', array(
            'methods' => 'POST',
            'callback' => array($this, 'send_message')
        ));
    }

    public function send_message(WP_REST_Request $req): WP_REST_Response
    {
        global $wpdb;
        $headers = $req->get_headers();
        $params = $req->get_params();
        $nonce = $headers['x_wp_nonce'][0];

        if (!wp_verify_nonce($nonce, self::NONCE_ACTION)) {
            return new WP_REST_Response('Message not sent', 422);
        }

        $from_uid = get_current_user_id();

        $message_data = array(
            'message'    =>  htmlentities($params['message']),
            'from_uid'  => $from_uid,
            'to_uid'   => $params['receiver_id'],
            'is_read'   => false,
            //'created_at' => current_time( 'timestamp')
        );

        $table = $wpdb->prefix . "flair_chat_messages";

        $new_message = $wpdb->insert($table, $message_data);

        $data = [
            'from' => $from_uid,
            'to' => $params['receiver_id']

        ];


        $this->pusher->trigger('my-channel', 'dru-chat-event', $data);

        return  new WP_REST_Response($data);
    }
    public function get_messages(WP_REST_Request $req): WP_REST_Response
    {

        $headers = $req->get_headers();
        $params = $req->get_params();
        $nonce = $headers['x_wp_nonce'][0];


        if (!wp_verify_nonce($nonce, self::NONCE_ACTION)) {
            return new WP_REST_Response('Whaaat!', 422);
        }

        global $wpdb;
        $from_uid = $params['id'];
        $page = isset($params['page']) ? absint($params['page']): 0;
        $limit = isset($params['limit']) ? absint($params['limit']): 10;
        $table = $wpdb->prefix . "flair_chat_messages";

        $to_uid = get_current_user_id();
        $user_ids = implode(',', [$to_uid, $from_uid]);

        $offset = $page * $limit;

        $total = $wpdb->get_var("
                SELECT COUNT(id) 
                FROM $table
                WHERE to_uid IN ($user_ids)
                AND from_uid IN ($user_ids) ");

        $num_of_pages = ceil($total / $limit);

        $message_data = $wpdb->get_results("
                  SELECT
                      id, message, from_uid as `from`, to_uid as `to`, is_read, created_at as `created`
                  FROM
                      $table
                   WHERE
                     to_uid IN ($user_ids)
                   AND from_uid IN ($user_ids)

                ORDER BY id DESC LIMIT $offset, $limit", OBJECT);

        $message_ids = ['5', '6', '7'];
        $message_ids = implode(',', $message_ids);

        $ids_to_update = [];
        if (!empty($message_data)) {
            foreach ($message_data as $message) {
                $ids_to_update[] = $message->id;
            }

            $ids_to_update = implode(',', $ids_to_update);

            $wpdb->query(
                "
				    UPDATE $table
				    SET is_read = true
				    WHERE ID IN ($ids_to_update)"
            );
        }


        $data = [
            'messages' => $message_data,
            'user_ids' => $user_ids,
            'pages' => [
                'current_page' => $page,
                'total_pages' => $num_of_pages,
                'total_items' => $total,
            ],
        ];

        return  new WP_REST_Response($data);
    }

    /**
     * Runs on plugin uninstall
     * @return void
     */
    public function flair_chat_cleanup(): void
    {
         global $wpdb;
         $tables = array(
             $wpdb->prefix . "flair_chat_messages",
         );
         foreach ($tables as $table) {
             $sql = "DROP TABLE IF EXISTS $table";
             $wpdb->query($sql);
         }
    }
}

new Flair_Chat();
