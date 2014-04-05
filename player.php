<?php

class Player
{
    const VERSION = "NoCo1024";

    public function betRequest($game_state)
    {
			extract($game_state);
			$me = $players[$in_action];

			
			$response = null;
			/*
			try {
				$cards = array_merge($players[$in_action]['hole_cards'], $community_cards);

				$ch = curl_init("localhost:2048");
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");

				curl_setopt($ch,CURLOPT_POST, count($cards));
				curl_setopt($ch,CURLOPT_POSTFIELDS, json_encode($cards));

				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1);
				curl_setopt($ch, CURLOPT_TIMEOUT, 1);
				$response = json_decode(curl_exec($ch), true);
				curl_close($ch);
				
			} catch (Exception $e){
				
			}*/
			//http://192.168.57.181:2048/
			
			$bet = $current_buy_in - $me['bet'];
			if ($response['rank'] != null){
				if ($response['rank'] > 3){
					$bet += $minimum_raise * 2;
				}
			}
			
      return $bet;
    }

    public function showdown($game_state)
    {
    }
}
