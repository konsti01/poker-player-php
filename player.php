<?php

class Player {

	const VERSION = "NoCo1406";

	private $_max_point = 28;
	private $_all_in = 25;

	public function betRequest($game_state) {
		$bet = 0;
		extract($game_state);
		$me = $players[$in_action];

		if (count($community_cards) > 0) {
			$cards['cards'] = json_encode(array_merge($me['hole_cards'], $community_cards));
			$response = $this->get_rankings($cards);
			
			if ($response['rank'] > 4){
				$bet = $me['stack'];
			} elseif(($response['rank'] > 3)){
				$bet = $small_blind * 10;
				$bet = ($bet > $current_buy_in) ? $bet : $current_buy_in;
			} elseif ($response['rank'] > 0){
				$bet = $small_blind * 5;
				$bet = ($bet > $current_buy_in) ? $bet : 0;
			}
		} else {

			$point = $this->rank_pre_flop($me['hole_cards']);

			//Akkor volt emelés
			if ($current_buy_in > $small_blind * 2) {
				//$bet = $current_buy_in;
			}

			$jolapomvan = false;
			// if ($point > $this->_max_point)
			if ($point > $this->_all_in) {
				$bet = $me['stack'] > 1000 ? 1000 : $me['stack'];
				$jolapomvan = true;
			} elseif ($point > $this->_max_point * 0.8) {
				$bet = $minimum_raise * 10;
				$jolapomvan = true;
			} elseif ($point > $this->_max_point * 0.6) {
				$bet = $minimum_raise * 5;
			}

			if ($jolapomvan) {
				return ($bet > $current_buy_in) ? $bet : $current_buy_in;
			}
		}
		return $bet;

		$response = null;
		if (count($community_cards) > 0) {
			$cards['cards'] = json_encode(array_merge($me['hole_cards'], $community_cards));
			$response = $this->get_rankings($cards);

			if ($response['rank'] > 0) {
				$bet = $response['rank'] * 60;
				if ($minimum_raise > $bet) {
					$bet = $minimum_raise;
				}
				 if ($response['rank'] > 2){
				  $bet += $minimum_raise;
				  }
				  if ($response['rank'] > 3){
				  $bet += $minimum_raise * 2;
				  }
			} else {
				if ($me['bet'] < 50) {
					$bet = 0;
				}
			}
		} else {
			if ($small_blind == $me['bet'] && $current_buy_in == $small_blind) {
				$bet = $current_buy_in;
			}

			if ($me['hole_cards'][0]['rank'] == $me['hole_cards'][1]['rank']) {
				$bet += $minimum_raise;
				if ($this->is_high($me['hole_cards'][0]['rank'])) {
					$bet += $minimum_raise * 10;
				}
			}
			if ($me['bet'] < 500 && $minimum_raise > 400) {

				$bet = 0;
			}

			$this->rank_pre_flop($me['hole_cards']);
		}

		//http://192.168.57.181:2048/
		//$bet = $current_buy_in - $me['bet'];

		$bet = rand($current_buy_in - $me['bet'], 600);
		return $bet;
	}

	private function rank_pre_flop($cards) {
		$point = 0;
		foreach ($cards as $card) {
			$point += $this->parse_card($card['rank']);
		}

		if ($cards[0]['suit'] == $cards[1]['suit']) {
			$point *= 1.125;
		}

		if (abs($cards[0]['rank'] - $cards[1]['rank']) == 1) {
			$point *= 1.100;
		}

		if ($cards[0]['rank'] - $cards[1]['rank'] == 0) {
			$point *= 1.6;
		}

		return $point;
	}

	private function parse_card($rank) {
		$result = 0;
		if ((int) $rank > 0) {
			$result = (int) $rank;
		}

		switch ($rank) {
			case 'j':
				$result = 11;
				break;
			case 'q':
				$result = 12;
				break;
			case 'k':
				$result = 13;
				break;
			case 'a':
				$result = 14;
				break;
		}
		return $result;
	}

	private function is_high($card) {
		switch ($card) {
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
			fwrite($stderr, $error . " - asd");
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
