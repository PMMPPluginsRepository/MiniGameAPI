<?php

declare(strict_types=1);

namespace skh6075\api\minigame\task;

use pocketmine\network\mcpe\protocol\SetTitlePacket;
use pocketmine\scheduler\Task;
use pocketmine\Server;
use skh6075\api\minigame\game\GameRoom;

abstract class MiniGameTask extends Task{

	public function __construct(protected GameRoom $room){}

	public function getGameRoom(): GameRoom{
		return $this->room;
	}

	public function setGameRoom(GameRoom $room): void{
		$this->room = $room;
	}

	public function broadcastMessage(string $text, ?array $targets = null): void{
		if($targets === null){
			$targets = $this->room->getParticipants();
		}
		Server::getInstance()->broadcastMessage($text, $targets);
	}

	public function broadcastActionBarMessage(string $text, ?array $targets = null): void{
		if($targets === null){
			$targets = $this->room->getParticipants();
		}
		Server::getInstance()->broadcastPackets($targets, [SetTitlePacket::actionBarMessage($text)]);
	}
}