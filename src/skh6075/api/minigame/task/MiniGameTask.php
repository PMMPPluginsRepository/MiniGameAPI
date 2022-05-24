<?php

declare(strict_types=1);

namespace skh6075\api\minigame\task;

use pocketmine\scheduler\Task;
use skh6075\api\minigame\game\GameRoom;

abstract class MiniGameTask extends Task{

	public function __construct(protected GameRoom $room){}

	final public function getGameRoom(): GameRoom{
		return $this->room;
	}
}