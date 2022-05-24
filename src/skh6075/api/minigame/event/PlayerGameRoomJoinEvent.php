<?php

declare(strict_types=1);

namespace skh6075\api\minigame\event;

use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;
use pocketmine\event\Event;
use pocketmine\player\Player;
use skh6075\api\minigame\game\GameRoom;
use skh6075\api\minigame\session\MiniGamePlayerSessionStorage;

final class PlayerGameRoomJoinEvent extends Event implements Cancellable{
	use CancellableTrait;

	public function __construct(
		private GameRoom $room,
		private Player $player,
		private MiniGamePlayerSessionStorage $storage
	){}

	public function getGameRoom(): GameRoom{
		return $this->room;
	}

	public function getPlayer(): Player{
		return $this->player;
	}

	public function getStorage(): MiniGamePlayerSessionStorage{
		return $this->storage;
	}
}