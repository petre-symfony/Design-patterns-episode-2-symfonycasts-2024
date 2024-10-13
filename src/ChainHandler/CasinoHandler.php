<?php

namespace App\ChainHandler;

use App\Character\Character;
use App\Dice;
use App\FightResult;
use App\GameApplication;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;

#[Autoconfigure(
	calls: [['setNext' => ['@' . LevelHandler::class]]]
)]
class CasinoHandler implements XpBonusHandlerInterface {

	private XpBonusHandlerInterface $next;

	public function __construct() {
		$this->next = new NullHandler();
	}

	public function handle(Character $player, FightResult $fightResult): int {
		$dice1 = Dice::roll(6);
		$dice2 = Dice::roll(6);

		// exit imediately
		if ($dice1 + $dice2 === 7) {
			GameApplication::$printer->info('You earned NO XP thanks to the Casino handler!');
			return 0;
		}

		// Rolled a pair
		if ($dice1 === $dice2) {
			GameApplication::$printer->info('You earned extra XP thanks to the Casino handler!');
			return 25;
		}

		return $this->next->handle($player, $fightResult);
	}

	public function setNext(XpBonusHandlerInterface $next): void {
		$this->next = $next;
	}
}