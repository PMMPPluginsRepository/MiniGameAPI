<?php

declare(strict_types=1);

namespace skh6075\api\minigame\session;

use pocketmine\player\Player;
use pocketmine\utils\SingletonTrait;
use skh6075\api\minigame\game\GameRoom;

final class MiniGamePlayerSessionManager{
	use SingletonTrait;

	public static function getInstance() : MiniGamePlayerSessionManager{
		return self::$instance ??= new self;
	}

	/**
	 * @phpstan-var array<string, MiniGamePlayerSession>
	 * @var MiniGamePlayerSession[]
	 */
	private array $session = [];

	public function getSession(Player $player): ?MiniGamePlayerSession{
		$rawUUID = $player->getUniqueId()->getBytes();
		return $this->session[$rawUUID] ?? null;
	}

	public function createSession(Player $player, MiniGamePlayerSessionStorage $storage, GameRoom $room): void{
		$rawUUID = $player->getUniqueId()->getBytes();
		if(isset($this->session[$rawUUID])){
			return;
		}
		$this->session[$rawUUID] = new MiniGamePlayerSession($player, $storage, $room);
	}

	public function removeSession(Player $player): void{
		$rawUUID = $player->getUniqueId()->getBytes();
		if(!isset($this->session[$rawUUID])){
			return;
		}
		unset($this->session[$rawUUID]);
	}

	public function reset(): void{
		$this->session = [];
	}
}