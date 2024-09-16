<?php

namespace App\ActionCommand;

use App\Character\Character;
use App\FightResultSet;

class AttackCommand {
	public function __construct(
		private readonly Character $player,
		private readonly Character $oponent,
		private readonly FightResultSet $fightResultSet) {
		
	}
}