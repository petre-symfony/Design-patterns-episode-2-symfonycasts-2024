<?php

namespace App\ChainHandler;

use App\Character\Character;
use App\Dice;
use App\FightResult;

class CasinoHandler implements XpBonusHandlerInterface {

	private XpBonusHandlerInterface $next;

	public function handle(Character $player, FightResult $fightResult): int {
		$dice1 = Dice::roll(6);
		$dice2 = Dice::roll(6);

		// exit imediately
		if ($dice1 + $dice2 === 7) {
			return 0;
		}

		// Rolled a pair
		if ($dice1 === $dice2) {
			return 25;
		}

		if (isset($this->next)) {
			$this->next->handle($player, $fightResult);
		}

		return 0;
	}

	public function setNext(XpBonusHandlerInterface $next): void {
		$this->next = $next;
	}
}