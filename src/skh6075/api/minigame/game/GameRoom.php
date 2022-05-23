<?php

declare(strict_types=1);

namespace skh6075\api\minigame\game;

use pocketmine\player\Player;
use skh6075\api\minigame\generator\MapGenerator;
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
		protected int $minPlayerCount = 0,
		protected int $maxPlayerCount = 1,
		private ?TeamManager $teamManager = null
	){}

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
		unset($this->participants[$player]);
		return true;
	}

	public function reset(): void{
		$this->participants = [];
		foreach(($this->teamManager?->getTeams() ?? []) as $team){
			$team->reset();
		}
		$this->gameMode = self::DEFAULT_MODE;
	}

	public function filterOfflineParticipant(): void{
		$this->participants = array_filter($this->participants, static fn($player) => $player !== null);
	}

	abstract public function start(): void;

	abstract public function end(Team|Player|null $winner = null): void;
}