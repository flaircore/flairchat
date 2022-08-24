<?php

namespace Flair\Chat;

/**
 * Provides a single instance of app variables,
 * throughout the Chat app
 */
class AppVars {

	protected $pusher_details;

	public function __construct() {
		$this->setUp();
	}

	private function setUp()
	{
		$app_id = get_option( 'flair_chat_setting_app_id_input' );
		$cluster = get_option( 'flair_chat_setting_cluster_input' );
		$secret = get_option( 'flair_chat_setting_secret_input' );
		$key = get_option( 'flair_chat_setting_key_input' );
		$roles= get_option( 'flair_chat_setting_roles_select' );
		$this->pusher_details = [
			'app_id' => $app_id,
			'cluster' => $cluster,
			'secret' => $secret,
			'key' => $key,
			'roles' => $roles,
		];
	}

	public function get_pusher_details() {
		return $this->pusher_details;
	}
}