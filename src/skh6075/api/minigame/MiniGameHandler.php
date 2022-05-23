<?php

declare(strict_types=1);

namespace skh6075\api\minigame;

use InvalidArgumentException;
use pocketmine\plugin\Plugin;

final class MiniGameHandler{

	/**
	 * Plugin that registered the library
	 * Only plugins loaded first are registered
	 *
	 * @var ?Plugin
	 */
	private static ?Plugin $registrant = null;

	/**
	 * Check if the library is used by the plugin
	 *
	 * @return bool
	 */
	public static function isRegistered(): bool{
		return self::$registrant !== null;
	}

	public static function register(Plugin $plugin): void{
		if(self::isRegistered()){
			throw new InvalidArgumentException("{$plugin->getName()} attempted to register " . self::class . " twice.");
		}

		self::$registrant = $plugin;
	}

	public static function getPlugin(): Plugin{
		return self::$registrant;
	}
}