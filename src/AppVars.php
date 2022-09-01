<?php

namespace Flair\Chat;

/**
 * Provides a single instance of app variables,
 * throughout the Chat app
 */
class AppVars
{

    protected $pusher_details;

    public function __construct()
    {
        $this->setUp();
    }

    private function setUp(): void
    {
        $app_id = get_option('flair_chat_setting_app_id_input');
        $cluster = get_option('flair_chat_setting_cluster_input');
        $secret = get_option('flair_chat_setting_secret_input');
        $key = get_option('flair_chat_setting_key_input');
        $roles = get_option('flair_chat_setting_roles_select');
        $notification_option = get_option('flair_chat_setting_notification_option');
        $this->pusher_details = array(
            'app_id' => $app_id,
            'cluster' => $cluster,
            'secret' => $secret,
            'key' => $key,
            'roles' => $roles,
            'notification_option' => $notification_option,
        );
    }

    public function getPusherDetails()
    {
        return $this->pusher_details;
    }
}
