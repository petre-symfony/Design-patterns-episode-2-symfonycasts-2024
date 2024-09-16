<?php

namespace App;

use App\ActionCommand\AttackCommand;
use App\Builder\CharacterBuilder;
use App\Character\Character;
use App\Observer\GameObserverInterface;
use App\Printer\MessagePrinter;
use RuntimeException;

class GameApplication {
	public static MessagePrinter $printer;

	private GameDifficultyContext $difficultyContext;

	/** @var GameObserverInterface[] */
	private array $observers = [];

	public function __construct(private readonly CharacterBuilder $characterBuilder) {
		$this->difficultyContext = new GameDifficultyContext();
	}

	public function play(Character $player, Character $ai, FightResultSet $fightResultSet): void {
		while (true) {
			$fightResultSet->addRound();
			GameApplication::$printer->writeln([
				'------------------------------',
				sprintf('ROUND %d', $fightResultSet->getRounds()
				), '']);

			// Player's turn
			$playerAction = new AttackCommand($player, $ai, $fightResultSet);
			$playerAction->execute();

			if ($this->didPlayerDie($ai)) {
				$this->endBattle($fightResultSet, $player, $ai);
				return;
			}

			// AI's turn
			$aiDamage = $ai->attack();

			if ($aiDamage === 0) {
				GameApplication::$printer->printFor($ai)->exhaustedMessage();
				$fightResultSet->of($ai)->addExhaustedTurn();
			}

			$damageReceived = $player->receiveAttack($aiDamage);
			$fightResultSet->of($player)->addDamageReceived($damageReceived);

			GameApplication::$printer->printFor($ai)->attackMessage($damageReceived);
			GameApplication::$printer->writeln('');

			if ($this->didPlayerDie($player)) {
				$this->endBattle($fightResultSet, $ai, $player);
				return;
			}

			$this->printCurrentHealth($player, $ai);
			usleep(300000);
		}
	}

	public function victory(Character $player, FightResult $fightResult): void {
		$this->difficultyContext->victory($player, $fightResult);
	}

	public function defeat(Character $player, FightResult $fightResult): void {
		$this->difficultyContext->defeat($player, $fightResult);
	}

	private function endBattle(FightResultSet $fightResultSet, Character $winner, Character $loser): void {
		GameApplication::$printer->printFor($winner)->victoryMessage($loser);

		$fightResultSet->setWinner($winner);
		$fightResultSet->setLoser($loser);
		$fightResultSet->of($winner)->addVictory();
		$fightResultSet->of($loser)->addDefeat();

		$this->notify($fightResultSet);

		$winner->rest();
		$loser->rest();
	}

	private function didPlayerDie(Character $player): bool {
		return $player->getCurrentHealth() <= 0;
	}

	public function createAiCharacter(): Character {
		$characters = $this->getCharactersList();
		$aiCharacterString = $characters[array_rand($characters)];

		$aiCharacter = $this->createCharacter(
			$aiCharacterString,
			$this->difficultyContext->enemyAttackBonus,
			$this->difficultyContext->enemyHealthBonus,
			1 + $this->difficultyContext->enemyLevelBonus
		);
		$aiCharacter->setNickname($aiCharacterString);

		return $aiCharacter;
	}

	public function createCharacter(string $character, int $extraBaseDamage = 0, int $extraHealth = 0, int $level = 1): Character {
		return match (strtolower($character)) {
			'fighter' => $this->characterBuilder
				->setMaxHealth(60 + $extraHealth)
				->setBaseDamage(12 + $extraBaseDamage)
				->setAttackType('sword')
				->setArmorType('shield')
				->setLevel($level)
				->buildCharacter(),

			'archer' => $this->characterBuilder
				->setMaxHealth(50 + $extraHealth)
				->setBaseDamage(10 + $extraBaseDamage)
				->setAttackType('bow')
				->setArmorType('leather_armor')
				->setLevel($level)
				->buildCharacter(),

			'mage' => $this->characterBuilder
				->setMaxHealth(40 + $extraHealth)
				->setBaseDamage(8 + $extraBaseDamage)
				->setAttackType('fire_bolt')
				->setArmorType('ice_block')
				->setLevel($level)
				->buildCharacter(),

			'mage_archer' => $this->characterBuilder
				->setMaxHealth(50 + $extraHealth)
				->setBaseDamage(9 + $extraBaseDamage)
				->setAttackType('fire_bolt', 'bow')
				->setArmorType('shield')
				->setLevel($level)
				->buildCharacter(),

			default => throw new RuntimeException('Undefined Character')
		};
	}

	public function getCharactersList(): array {
		return [
			'fighter',
			'mage',
			'archer',
			'mage_archer',
		];
	}

	private function printCurrentHealth(Character $player, Character $ai): void {
		GameApplication::$printer->writeln([sprintf(
			'Current Health: <comment>%d/%d</comment> %sAI Health: <comment>%d/%d</comment>',
			$player->getCurrentHealth(),
			$player->getMaxHealth(),
			PHP_EOL,
			$ai->getCurrentHealth(),
			$ai->getMaxHealth(),
		), '']);
	}

	public function subscribe(GameObserverInterface $observer): void {
		if (!in_array($observer, $this->observers, true)) {
			$this->observers[] = $observer;
		}
	}

	public function unsubscribe(GameObserverInterface $observer): void {
		$key = array_search($observer, $this->observers, true);

		if ($key !== false) {
			unset($this->observers[$key]);
		}
	}

	private function notify(FightResultSet $fightResultSet): void {
		foreach ($this->observers as $observer) {
			$observer->onFightFinished($fightResultSet);
		}
	}
}
