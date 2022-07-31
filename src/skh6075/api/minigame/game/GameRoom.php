<?php

declare(strict_types=1);

namespace skh6075\api\minigame\game;

use pocketmine\network\mcpe\protocol\SetTitlePacket;
use pocketmine\player\Player;
use pocketmine\scheduler\TaskHandler;
use pocketmine\Server;
use skh6075\api\minigame\event\PlayerGameRoomJoinEvent;
use skh6075\api\minigame\event\PlayerGameRoomQuitEvent;
use skh6075\api\minigame\generator\MapGenerator;
use skh6075\api\minigame\session\MiniGamePlayerSessionManager;
use skh6075\api\minigame\session\MiniGamePlayerSessionStorage;
use skh6075\api\minigame\team\Team;
use skh6075\api\minigame\team\TeamManager;

abstract class GameRoom{
	public const DEFAULT_MODE = 0;

	/**
	 * players participating in the game
	 *
	 * @phpstan-var array<string, Player>
	 * @var Player[]
	 */
	private array $participants = [];

	protected int $gameMode = self::DEFAULT_MODE;

	public function __construct(
		private string $name,
		private MapGenerator $mapGenerator,
		private ?TaskHandler $taskHandler = null,
		protected int $minPlayerCount = 0,
		protected int $maxPlayerCount = 1,
		private ?TeamManager $teamManager = null,
	){}

	public function getPrefix(): string{
		return "§l§b[$this->name]§r§7 ";
	}

	final public function getName(): string{
		return $this->name;
	}

	final public function getMapGenerator(): MapGenerator{
		return $this->mapGenerator;
	}

	public function getMinPlayerCount(): int{
		return $this->minPlayerCount;
	}

	public function getMaxPlayerCount(): int{
		return $this->maxPlayerCount;
	}

	public function getTeamManager(): ?TeamManager{
		return $this->teamManager;
	}

	public function getGameMode(): int{
		return $this->gameMode;
	}

	public function setGameMode(int $mode = self::DEFAULT_MODE): void{
		$this->gameMode = $mode;
	}

	public function getParticipants(): array{
		return $this->participants;
	}

	public function isParticipant(Player|string $player): bool{
		if($player instanceof Player){
			$player = $player->getUniqueId()->getBytes();
		}
		return isset($this->participants[$player]);
	}

	public function addParticipant(Player $player): bool{
		$rawUUID = $player->getUniqueId()->getBytes();
		if(isset($this->participants[$rawUUID])){
			return false;
		}
		$this->filterOfflineParticipant();
		if($this->maxPlayerCount <= count($this->participants)){
			return false;
		}
		if($this->gameMode !== self::DEFAULT_MODE){
			return false;
		}
		$ev = new PlayerGameRoomJoinEvent($this, $player, $this->session());
		$ev->call();
		if($ev->isCancelled()){
			return false;
		}

		$this->participants[$rawUUID] = $player;
		return true;
	}

	public function removeParticipant(Player|string $player): bool{
		if($player instanceof Player){
			$player = $player->getUniqueId()->getBytes();
		}
		if(!isset($this->participants[$player])){
			return false;
		}

		(new PlayerGameRoomQuitEvent($this, $player))->call();

		unset($this->participants[$player]);
		$this->filterOfflineParticipant();
		return true;
	}

	public function reset(): void{
		$this->participants = [];
		foreach(($this->teamManager?->getTeams() ?? []) as $team){
			$team->reset();
		}
		$this->gameMode = self::DEFAULT_MODE;
		$this->taskHandler = null;
	}

	public function filterOfflineParticipant(): void{
		$this->participants = array_filter($this->participants, static fn($player) => $player !== null);
	}

	final public function getTaskHandler(): ?TaskHandler{
		return $this->taskHandler;
	}

	public function setTaskHandler(?TaskHandler $handler = null): void{
		$this->taskHandler?->cancel();
		$this->taskHandler = $handler;
	}

	abstract public function start(): void;

	public function end(Team|Player|null $winner = null): void{
		foreach($this->participants as $player){
			MiniGamePlayerSessionManager::getInstance()->removeSession($player);
		}
	}

	abstract public function session(): MiniGamePlayerSessionStorage;

	public function broadcastMessage(string $text, ?array $targets = null): void{
		if($targets === null){
			$targets = $this->participants;
		}
		Server::getInstance()->broadcastMessage($text, $targets);
	}

	public function broadcastActionBarMessage(string $text, ?array $targets = null): void{
		if($targets === null){
			$targets = $this->participants;
		}
		Server::getInstance()->broadcastPackets($targets, [SetTitlePacket::actionBarMessage($text)]);
	}
}