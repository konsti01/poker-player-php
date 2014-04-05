<?php

class Player
{
    const VERSION = "NoCo1007";

    public function betRequest($game_state)
    {
			//http://192.168.57.138:4567
			$bet = $game_state['current_buy_in'] - $game_state['players'][$game_state['in_action']]['bet'];
      return $bet;
    }

    public function showdown($game_state)
    {
    }
}
