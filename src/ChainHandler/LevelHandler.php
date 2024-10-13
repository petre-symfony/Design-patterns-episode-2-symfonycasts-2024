<?php

namespace App\ChainHandler;

use App\Character\Character;
use App\FightResult;
use App\GameApplication;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

#[Autoconfigure(
	calls: [['setNext' => ['@' . OnFireHandler::class]]]
)]
class LevelHandler implements XpBonusHandlerInterface {
	private XpBonusHandlerInterface $next;

	public function __construct(
		#[Autowire(service: NullHandler::class)]
		XpBonusHandlerInterface $next
	) {
		$this->next = new NullHandler();
	}

	public function handle(Character $player, FightResult $fightResult): int {
		if ($player->getLevel() === 1) {
			GameApplication::$printer->info('You earned extra XP thanks to the Level handler!');
			return 25;
		}

		return $this->next->handle($player, $fightResult);
	}

	public function setNext(XpBonusHandlerInterface $next): void {
		$this->next = $next;
	}

}