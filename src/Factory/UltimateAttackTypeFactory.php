<?php

namespace App\Factory;

use App\AttackType\AttackType;

class UltimateAttackTypeFactory implements AttackTypeFactoryInterface {

	public function create(string $type): AttackType {
		return match ($type) {
			'bow' => new TytaniumType()
		};
	}
}