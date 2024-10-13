<?php

namespace App\ChainHandler;

use App\Character\Character;
use App\FightResult;
use App\GameApplication;

class OnFireHandler implements XpBonusHandlerInterface {

	private XpBonusHandlerInterface $next;

	public function __construct() {
		$this->next = new NullHandler();
	}

	public function handle(Character $player, FightResult $fightResult): int {
		if ($fightResult->getWinStreak() >=3) {
			GameApplication::$printer->info('You earned extra XP thanks to the OnFire handler!');
			return 25;
		}

		return $this->next->handle($player, $fightResult);
	}

	public function setNext(XpBonusHandlerInterface $next): void {
		$this->next = $next;
	}
}