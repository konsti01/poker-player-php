<?php

class Player {

	const VERSION = "NoCo1201";

	public function betRequest($game_state) {
		return 1000;
		extract($game_state);
		$me = $players[$in_action];

		$response = null;
		if (count($community_cards) > 0) {
			$cards['cards'] = json_encode(array_merge($me['hole_cards'], $community_cards));
			$response = $this->get_rankings($cards);
		}

		//http://192.168.57.181:2048/

		$bet = $current_buy_in - $me['bet'];
		
		if ($response['rank'] != null) {
			if ($response['rank'] > 3) {
				$bet += $minimum_raise * 2;
			} else {
				if ($me['bet'] < 400){
					$bet = 0;
				}
			}
		} else {
			if ($me['hole_cards'][0]['rank'] == $me['hole_cards'][1]['rank']) {
				$bet += $minimum_raise;
			}
			if ($bet > 400){
				$bet = 0;
			}
		}

		return $bet;
	}

	private function get_rankings($cards) {
		$response = null;

		$stderr = fopen("php://stderr", "w");

		try {
			$ch = curl_init("http://192.168.57.181:2048");
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");

			curl_setopt($ch, CURLOPT_POST, count($cards));
			curl_setopt($ch, CURLOPT_POSTFIELDS, $cards);

			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
			curl_setopt($ch, CURLOPT_TIMEOUT, 10);
			$response = curl_exec($ch);
			$error = curl_error($ch);
			curl_close($ch);
			fwrite($stderr, $error . " - " . $response);
			$response = json_decode($response, true);
		} catch (Exception $e) {
			fwrite($stderr, "hello" . $e->error());
		}
		fclose($stderr);
		return $response;
	}

	public function showdown($game_state) {
		
	}

}
