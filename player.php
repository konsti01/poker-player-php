<?php

class Player {

	const VERSION = "NoCo1300";

	public function betRequest($game_state) {
		$bet = 0;
		extract($game_state);
//		$me = $players[$in_action];
//
//		$response = null;
//		if (count($community_cards) > 0) {
//			$cards['cards'] = json_encode(array_merge($me['hole_cards'], $community_cards));
//			$response = $this->get_rankings($cards);
//
//			if ($response['rank'] > 0) {
//				$bet = $response['rank'] * 60;
//				if ($minimum_raise > $bet){
//					$bet = $minimum_raise;
//				}
//				/*if ($response['rank'] > 2){
//					$bet += $minimum_raise;
//				}
//				if ($response['rank'] > 3){
//					$bet += $minimum_raise * 2;
//				}*/
//			} else {
//				if ($me['bet'] < 50) {
//					$bet = 0;
//				}
//			}
//		} else {
//			if ($small_blind == $me['bet'] && $current_buy_in == $small_blind){
//				$bet = $current_buy_in;
//			}
//			
//			if ($me['hole_cards'][0]['rank'] == $me['hole_cards'][1]['rank']) {
//				$bet += $minimum_raise;
//				if ($this->is_high($me['hole_cards'][0]['rank'])){
//					$bet += $minimum_raise * 10;
//				}
//			}
//			if ($me['bet'] < 500 && $minimum_raise > 400) {
//				$bet = 0;
//			}
//		}
//
//		//http://192.168.57.181:2048/
//
//		//$bet = $current_buy_in - $me['bet'];
//
//
		$bet = rand(0, 1000);
		
		return $bet;
	}
	
	private function is_high($card){
		switch ($card){
			case 'k':
			case 'q':
			case 'j':
			case 'a':
				$result = true;
				break;
			default :
				$result = false;
				break;
		}
		return $result;
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
