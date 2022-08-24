<?php

// "Nick\\PhpSse\\": "src/"
namespace Flair\Chat\Faker;

use \Faker\Factory;
class MessageFactory
{
    /**
     * @var Factory;
     */
    private $faker;

    function __construct()
    {
        // use the factory to create a Faker\Generator instance
        $this->faker = Factory::create();

    }

    public function generateFakeMessages(){
        // create demos
        global $wpdb;

        $args = array( 'role__not_in' => 'Admin' );
        $user_query = new \WP_User_Query($args);
        $users = $user_query->get_results();
        for ($i = 0; $i < 55; ++$i) {
            do {
                $urs = array_rand($users, 2);
                $from = $urs[0];
                //$from = 18;
                $to = $urs[1];
                //$to = 1;
            } while ($from === $to);
            $message_data = array(
                'message'    => $this->faker->sentence(),
                'from_uid'  => $from,
                'to_uid'   => $to,
                'is_read'   => false,
                //'created_at' => current_time( 'timestamp')
            );



            $table = $wpdb->prefix . "flair_chat_messages";

            $new_message = $wpdb->insert($table, $message_data);

            // @TODO:: bulk prepare

        }

        // @TODO:: bulk save prepared

    }

}