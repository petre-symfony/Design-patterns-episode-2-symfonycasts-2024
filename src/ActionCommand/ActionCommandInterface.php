<?php

namespace App\ActionCommand;

interface ActionCommandInterface {
	public function execute();

	public function undo();
}