<?php

class Player
{
    const VERSION = "NoCo1011";

    public function betRequest($game_state)
    {
			//http://192.168.57.138:4567
			extract($game_state);
			$me = $players[$in_action];
			
			$bet = $current_buy_in - $me['bet'];
      return $bet;
    }

    public function showdown($game_state)
    {
    }
}
