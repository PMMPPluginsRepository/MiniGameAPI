<?php

declare(strict_types=1);

namespace skh6075\api\minigame\session;

use JetBrains\PhpStorm\Pure;

final class MiniGamePlayerSessionStorage{

	public function __construct(
		private bool $quitDestroySession,
		private array $storages
	){}

	/**
	 * Whether to delete the session when the target player is fired
	 *
	 * @return bool
	 */
	public function isQuitDestroySession(): bool{
		return $this->quitDestroySession;
	}

	/**
	 * A space to store the mini-game data of the player in the session.
	 *
	 * @return array
	 */
	public function getStorages(): array{
		return $this->storages;
	}

	public function getStorage(string $storage, mixed $value = null): mixed{
		return $this->storages[$storage] ?? $value;
	}

	public function setStorage(string $storage, mixed $value): self{
		$this->storages[$storage] = $value;
		return $this;
	}

	public function exists(string $storage): bool{
		return isset($this->storages[$storage]);
	}

	public function reset(): void{
		$this->storages = [];
	}

	#[Pure]
	public static function create(bool $quitDestroy = true, array $storage = []): MiniGamePlayerSessionStorage{
		return new MiniGamePlayerSessionStorage($quitDestroy, $storage);
	}
}