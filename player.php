<?php

class Player {

	const VERSION = "NoCo1619";

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
			} elseif ($response['rank'] > 1){
				if ($minimum_raise < $small_blind * 10){
					$bet = $small_blind * 10;
				} else {
					$bet = $small_blind * 5;
				}
				$bet = ($bet > $current_buy_in) ? $bet : $current_buy_in;
			} elseif ($response['rank'] > 0){
				$bet = $small_blind * 5;
				$bet = ($bet > $current_buy_in) ? $bet : 0;
			}
			
			if ($me['bet'] > ($current_buy_in)){
				$bet = ($bet > $current_buy_in) ? $bet : $current_buy_in;
			}
		} else {

			$point = $this->rank_pre_flop($me['hole_cards']);

			$minimum_raise = $small_blind * 2;
			$jolapomvan = false;
			$nagyonjolapomvan = false;
			// if ($point > $this->_max_point)
			if ($point > $this->_all_in) {
				$bet = $me['stack'] > 1000 ? 1000 : $me['stack'];
				$jolapomvan = true;
				$nagyonjolapomvan = true;
			} elseif ($point > ($this->_max_point * 0.8)) {
				$bet = $minimum_raise * 9;
				$jolapomvan = true;
			} elseif ($point > ($this->_max_point * 0.6)) {
				$bet = $minimum_raise * 4;
			}

			if ($jolapomvan) {
				$bet = ($bet > $current_buy_in) ? $bet : $current_buy_in;
				if ($nagyonjolapomvan){
					$bet *= 2;
					if($this->ketten_vagyunk($players)){
						$bet = $me['stack'];
					}
				}
			}
			
			if ($point > 20){
				if ($me['bet'] > ($current_buy_in)){
					$bet = ($bet > $current_buy_in) ? $bet : $current_buy_in;
				}
			}
		}
		
		
		
		return $bet;
	}
	
	private function ketten_vagyunk($players){
		$out = 0;
		foreach ($players as $p){
			if ($p['status'] == 'out'){
				$out++;
			}
		}
		return $out>0;
	}

	private function rank_pre_flop($cards) {
		$point = 0;
		foreach ($cards as $card) {
			$point += $this->parse_card($card['rank']);
		}

		if ($cards[0]['suit'] == $cards[1]['suit']) {
			$point *= 1.125;
		}

		if ($point < 22){
			if (abs($cards[0]['rank'] - $cards[1]['rank']) == 1) {
				$point *= 1.100;
			}
		}

		if ($cards[0]['rank'] - $cards[1]['rank'] == 0) {
			$point *= (1 + ($this->parse_card($cards[0]['rank']) / 10));
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
			case 'J':
				$result = 11;
				break;
			case 'q':
			case 'Q':
				$result = 12;
				break;
			case 'k':
			case 'K':
				$result = 13;
				break;
			case 'a':
			case 'A':
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
