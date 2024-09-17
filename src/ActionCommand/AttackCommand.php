<?php

namespace App\ActionCommand;

use App\Character\Character;
use App\FightResultSet;
use App\GameApplication;

class AttackCommand implements ActionCommandInterface {
	private int $damageDealt;
	private int $stamina;

	public function __construct(
		private readonly Character $player,
		private readonly Character $oponent,
		private readonly FightResultSet $fightResultSet) {
		
	}

	public function execute() {
		$this->stamina = $this->player->getStamina();
		$playerDamage = $this->player->attack();
		if ($playerDamage === 0) {
			GameApplication::$printer->printFor($this->player)->exhaustedMessage();
			$this->fightResultSet->of($this->player)->addExhaustedTurn();
		}

		$damageDealt = $this->oponent->receiveAttack($playerDamage);
		$this->damageDealt = $damageDealt;

		$this->fightResultSet->of($this->player)->addDamageDealt($damageDealt);

		GameApplication::$printer->printFor($this->player)->attackMessage($damageDealt);
		GameApplication::$printer->writeln('');
		usleep(300000);
	}

	public function undo() {
		$this->oponent->setHealth($this->oponent->getCurrentHealth() + $this->damageDealt);
		$this->player->setStamina($this->stamina);

		$this->fightResultSet->of($this->player)->removeDamageDealt($this->damageDealt);
		$this->fightResultSet->of($this->oponent)->removeDamageReceived($this->damageDealt);
	}
}