<?php

namespace App\DifficultyState;

use App\Character\Character;
use App\FightResult;
use App\GameApplication;
use App\GameDifficultyContext;

class EasyState implements DifficultyStateInterface {

	public function victory(GameDifficultyContext $difficultyContext, Character $player, FightResult $fightResult) {
		if ($player->getLevel() >= 2 || $fightResult->getTotalVictories() >= 2) {
			$this->enemyAttackBonus = 5;
			$this->enemyHealthBonus = 5;
			$player->setXpBonus(25);
			$this->level++;

			GameApplication::$printer->info('Game difficulty level increased to Medium!');
		}
	}

	public function defeat(GameDifficultyContext $difficultyContext, Character $player, FightResult $fightResult) {
		// TODO: Implement defeat() method.
	}
}