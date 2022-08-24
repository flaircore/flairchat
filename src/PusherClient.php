<?php

namespace Flair\Chat;

use Pusher\Pusher;
use Pusher\PusherException;

class PusherClient
{

	/**
	 * Returns a pusher instance we can reuse.
	 *
	 * @param $details
	 *
	 * @return Pusher
	 * @throws PusherException
	 */
	public function pusherInstance($details): Pusher {

		$cluster = $details['cluster'];
		$app_id = $details['app_id'];
		$secret =  $details['secret'];
		$key =  $details['key'];

		$options = [
			'cluster' => $cluster,
			//'useTLS' => false
		];
		return new Pusher(
			$key,
			$secret,
			$app_id,
			$options
		);
	}

}