<?php

declare(strict_types=1);

namespace skh6075\api\minigame\event;

use pocketmine\event\Event;
use pocketmine\player\Player;
use skh6075\api\minigame\game\GameRoom;

final class PlayerGameRoomQuitEvent extends Event{
	public const REASON_DISCONNECT = "Disconnect";

	public function __construct(
		private GameRoom $room,
		private Player $player,
		private string $reason = self::REASON_DISCONNECT
	){}

	public function getGameRoom(): GameRoom{
		return $this->room;
	}

	public function getPlayer(): Player{
		return $this->player;
	}

	public function getReason(): string{
		return $this->reason;
	}

	public function setReason(string $reason): void{
		$this->reason = $reason;
	}

	public function equal(string $reason): bool{
		return $this->reason === $reason;
	}
}