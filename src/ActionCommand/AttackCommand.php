<?php

namespace App\ActionCommand;

use App\Character\Character;
use App\FightResultSet;
use App\GameApplication;

class AttackCommand implements ActionCommandInterface {
	public function __construct(
		private readonly Character $player,
		private readonly Character $oponent,
		private readonly FightResultSet $fightResultSet) {
		
	}

	public function execute() {
		$playerDamage = $this->player->attack();
		if ($playerDamage === 0) {
			GameApplication::$printer->printFor($this->player)->exhaustedMessage();
			$this->fightResultSet->of($this->player)->addExhaustedTurn();
		}

		$damageDealt = $this->oponent->receiveAttack($playerDamage);
		$this->fightResultSet->of($this->player)->addDamageDealt($damageDealt);

		GameApplication::$printer->printFor($this->player)->attackMessage($damageDealt);
		GameApplication::$printer->writeln('');
		usleep(300000);
	}
}