<?php

declare(strict_types=1);

namespace skh6075\api\minigame\team;

use JetBrains\PhpStorm\Pure;

final class TeamManager{

	/**
	 * @phpstan-var array<string, Team>
	 * @var Team[]
	 */
	private array $teams = [];

	#[Pure]
	public function __construct(Team ...$teams){
		foreach($teams as $team){
			$this->teams[$team->getName()] = $team;
		}
	}

	public function getTeams(): array{
		return $this->teams;
	}

	#[Pure]
	public function isExistsTeam(Team|string $team): bool{
		if($team instanceof Team){
			$team = $team->getName();
		}
		return isset($this->teams[$team]);
	}

	public function addTeam(Team $team): bool{
		if(isset($this->teams[$team->getName()])){
			return false;
		}
		$this->teams[$team->getName()] = $team;
		return true;
	}

	public function removeTeam(Team|string $team): bool{
		if($team instanceof Team){
			$team = $team->getName();
		}
		if(!isset($this->teams[$team])){
			return false;
		}
		unset($this->teams[$team]);
		return true;
	}
}