<?php

namespace App\ChainHandler;

use App\Character\Character;
use App\FightResult;

class NullHandler implements XpBonusHandlerInterface {
	public function handle(Character $player, FightResult $fightResult): int {
		return 0;
	}
	public function setNext(XpBonusHandlerInterface $next): void {
		// nothing to do
	}
}