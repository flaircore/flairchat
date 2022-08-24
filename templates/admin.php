<?php

if (!defined('ABSPATH')) {
    exit();
}


function add_flair_chat_settings_page()
{
	add_menu_page(
		'FlairChat plugin',
		'FlairChat Config',
		'manage_options',
		'flair-chat-settings-page',
		'admin_index',
		'dashicons-admin-generic',
		110);

}

function flair_chat_settings_init ()
{
	// Setup settings section
	add_settings_section(
		'flair_chat_settings_section',
		'FlairChat Settings Page',
		'',
		'flair-chat-settings-page'

	);

	// Register form fields

	// app_id
    register_setting(
		'flair-chat-settings-page',
		'flair_chat_setting_app_id_input',
		array(
			'type' => 'string',
			'sanitize_callback' => 'sanitize_text_field',
			'default' => ''
		)
	);

	// key
	register_setting(
		'flair-chat-settings-page',
		'flair_chat_setting_key_input',
		array(
			'type' => 'string',
			'sanitize_callback' => 'sanitize_text_field',
			'default' => ''
		)
	);

	// secret
    register_setting(
		'flair-chat-settings-page',
		'flair_chat_setting_secret_input',
		array(
			'type' => 'string',
			'sanitize_callback' => 'sanitize_text_field',
			'default' => ''
		)
	);

	// cluster
	register_setting(
		'flair-chat-settings-page',
		'flair_chat_setting_cluster_input',
		array(
			'type' => 'string',
			'sanitize_callback' => 'sanitize_text_field',
			'default' => ''
		)
	);

	// Exclude roles
	register_setting(
		'flair-chat-settings-page',
		'flair_chat_setting_roles_select',
		array(
			'type' => 'select',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);

	// Add settings fields
	add_settings_field(
		'flair_chat_setting_app_id_input',
		__('App id', 'flair-chat'),
		'flair_chat_settings_app_id_input_callback',
		'flair-chat-settings-page',
		'flair_chat_settings_section'
	);

	add_settings_field(
		'flair_chat_setting_key_input',
		__('Key', 'flair-chat'),
		'flair_chat_settings_key_input_callback',
		'flair-chat-settings-page',
		'flair_chat_settings_section'
	);


	add_settings_field(
		'flair_chat_setting_secret_input',
		__('Secret', 'flair-chat'),
		'flair_chat_settings_secret_input_callback',
		'flair-chat-settings-page',
		'flair_chat_settings_section'
	);

	add_settings_field(
		'flair_chat_setting_cluster_input',
		__('Cluster', 'flair-chat'),
		'flair_chat_settings_cluster_input_callback',
		'flair-chat-settings-page',
		'flair_chat_settings_section'
	);

	add_settings_field(
		'flair_chat_setting_roles_select',
		__('Roles to Exclude from Chat', 'flair-chat'),
		'flair_chat_settings_roles_select_callback',
		'flair-chat-settings-page',
		'flair_chat_settings_section'
	);




}

function flair_chat_settings_app_id_input_callback()
{
	$options = get_option( 'flair_chat_setting_app_id_input' );
	?>
    <input type="text" name="flair_chat_setting_app_id_input" class="regular-text" value="<?php echo $options ?? '' ?>">

	<?php
}
function flair_chat_settings_cluster_input_callback()
{
	$options = get_option( 'flair_chat_setting_cluster_input' );
	?>
    <input type="text" name="flair_chat_setting_cluster_input" class="regular-text" value="<?php echo $options ?? '' ?>">

	<?php
}
function flair_chat_settings_secret_input_callback()
{
	$options = get_option( 'flair_chat_setting_secret_input' );
	?>
    <input type="text" name="flair_chat_setting_secret_input" class="regular-text" value="<?php echo $options ?? '' ?>">

	<?php
}
function flair_chat_settings_key_input_callback()
{
	$options = get_option( 'flair_chat_setting_key_input' );
	?>
    <input type="text" name="flair_chat_setting_key_input" class="regular-text" value="<?php echo $options ?? '' ?>">

	<?php
}

function flair_chat_settings_roles_select_callback()
{
	$options = get_option( 'flair_chat_setting_roles_select' );
    return ''
	?>
    <select multiple='multiple' name="flair_chat_setting_roles_select" class="regular-text">
        <option value="">Select Roles to exclude</option>
        <option value="Admin" <?php selected('Admin', $options); ?>> Admin </option>
        <option value="Editor" <?php selected('Editor', $options); ?>> Editor </option>
    </select>

	<?php
}


function admin_index()
{
	?>
    <div class="wrap">
        <h1><?php echo esc_html( get_admin_page_title() )?></h1>
        <form action="options.php" method="post">
			<?php

			// Security field
			settings_fields( 'flair-chat-settings-page' );

			// output settings section here
			do_settings_sections( 'flair-chat-settings-page' );

			// Save settings btn

			submit_button('Save settings');

			?>
        </form>
        <div class="info">
            <span> Go to <a target="_blank" href="https://dashboard.pusher.com/apps/">https://dashboard.pusher.com/apps/</a>
                and  get your app (channel) keys/details.
            </span>
        </div>
    </div>
	<?php
}

add_action('admin_menu', 'add_flair_chat_settings_page');
add_action('admin_init', 'flair_chat_settings_init');
