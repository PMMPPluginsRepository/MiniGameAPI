<?php

declare(strict_types=1);

namespace skh6075\api\minigame\team;

use pocketmine\player\Player;

class Team{

	/**
	 * players on the team
	 *
	 * @phpstan-var array<string, Player>
	 * @var Player[]
	 */
	private array $member = [];

	/**
	 * Includes team schema options
	 *
	 * @phpstan-var array<string, mixed>
	 * @var array
	 */
	private array $option = [];

	public function __construct(private string $name){}

	public function getName(): string{
		return $this->name;
	}

	public function getMemberList(): array{
		return $this->member;
	}

	public function isInMember(Player|string $player): bool{
		if($player instanceof Player){
			$player = $player->getUniqueId()->getBytes();
		}
		return isset($this->member[$player]);
	}

	public function addMember(Player $player): bool{
		$rawUUID = $player->getUniqueId()->getBytes();
		if(isset($this->member[$rawUUID])){
			return false;
		}
		$this->member[$rawUUID] = $player;
		return true;
	}

	public function removeMember(Player|string $player): bool{
		if($player instanceof Player){
			$player = $player->getUniqueId()->getBytes();
		}
		if(!isset($this->member[$player])){
			return false;
		}
		unset($this->member[$player]);
		return true;
	}

	public function getOptions(): array{
		return $this->option;
	}

	public function getOption(string $option, mixed $default): mixed{
		return $this->option[$option] ?? $default;
	}

	public function setOption(string $option, mixed $value): void{
		$this->option[$option] = $value;
	}

	public function reset(): void{
		$this->member = [];
		$this->option = [];
	}
}