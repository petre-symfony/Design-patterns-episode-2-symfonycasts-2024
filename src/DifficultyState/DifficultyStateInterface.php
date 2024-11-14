<?php

namespace App\DifficultyState;

use App\Character\Character;
use App\FightResult;
use App\GameDifficultyContext;

interface DifficultyStateInterface {
	public function victory(
		GameDifficultyContext $difficultyContext,
		Character $player,
		FightResult $fightResult
	);
	public function defeat(
		GameDifficultyContext $difficultyContext,
		Character $player,
		FightResult $fightResult
	);
}