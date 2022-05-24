<?php

declare(strict_types=1);

namespace skh6075\api\minigame\session;

use pocketmine\player\Player;
use skh6075\api\minigame\game\GameRoom;

final class MiniGamePlayerSession{

	public function __construct(
		private Player $player,
		private MiniGamePlayerSessionStorage $storage,
		private GameRoom $room,
	){}

	public function getPlayer(): Player{
		return $this->player;
	}

	public function getGameRoom(): GameRoom{
		return $this->room;
	}

	public function setGameRoom(GameRoom $room): void{
		$this->room = $room;
	}

	public function getStorage(): MiniGamePlayerSessionStorage{
		return $this->storage;
	}
}