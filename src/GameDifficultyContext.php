<?php

namespace App;

use App\Character\Character;
use App\DifficultyState\DifficultyStateInterface;
use App\DifficultyState\EasyState;

class GameDifficultyContext {
	public int $level = 1;
	public int $enemyLevelBonus = 0;
	public int $enemyHealthBonus = 0;
	public int $enemyAttackBonus = 0;
	public DifficultyStateInterface $difficultyState;

	public function __construct() {
		$this->difficultyState = new EasyState();
	}

	public function victory(Character $player, FightResult $fightResult): void {
		$this->difficultyState->victory($this, $player, $fightResult);
	}

	public function defeat(Character $player, FightResult $fightResult): void {
		$this->difficultyState->defeat($this, $player, $fightResult);
	}
}
