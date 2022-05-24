<?php

/**
 * API that makes setting/resetting the MiniGame world easy
 *
 * However, developers with optimized technology other than this function do not need to use it.
 * If you have a better way, please share the source code!
 */

declare(strict_types=1);

namespace skh6075\api\minigame\generator;

use InvalidArgumentException;
use pocketmine\scheduler\ClosureTask;
use pocketmine\Server;
use pocketmine\utils\AssumptionFailedError;
use pocketmine\utils\Filesystem;
use pocketmine\world\World;
use pocketmine\world\WorldManager;
use PrefixedLogger;
use skh6075\api\minigame\MiniGameHandler;
use ZipArchive;
use function file_exists;
use function pathinfo;
use function is_dir;
use const PATHINFO_EXTENSION;
use const PATHINFO_BASENAME;

final class MapGenerator{

	/**
	 * Save the compressed world
	 *
	 * Never force a file to be added by accessing this property with a closure bind or Reflection
	 *
	 * @phpstan-var array<string, ZipArchive>
	 * @var ZipArchive[]
	 */
	private array $zipArchives = [];

	private WorldManager $worldManager;

	private PrefixedLogger $logger;

	public function __construct(array $zipFiles){
		$this->logger = new PrefixedLogger(Server::getInstance()->getLogger(), "MapGenerator");
		foreach($zipFiles as $identifier => $filePath){
			$this->addZipFile($identifier, $filePath);
		}
		$this->worldManager = Server::getInstance()->getWorldManager();
	}

	private function addZipFile(string $identifier, string $filePath): void{
		if(isset($this->zipArchives[$identifier])){
			throw new AssumptionFailedError("Attempted to register file with $identifier twice.");
		}
		if(!file_exists($filePath)){
			throw new InvalidArgumentException("The compressed file could not be found in the $filePath");
		}
		if(pathinfo($filePath, PATHINFO_EXTENSION) !== "zip"){
			throw new InvalidArgumentException("File registration is possible only with extension zip.");
		}
		$zipArchive = new ZipArchive();
		if($zipArchive->open($filePath) !== true){
			throw new InvalidArgumentException("Unable to access file " . pathinfo($filePath, PATHINFO_BASENAME));
		}
		$this->zipArchives[$identifier] = $zipArchive;
		$this->logger->debug("Successfully added compressed file to \"$filePath\" path with $identifier");
	}

	public function isValid(string $identifier): bool{
		return isset($this->zipArchives[$identifier]);
	}

	public function extractTo(string $identifier, string $dest, bool $reset = true): bool{
		if(!$this->isValid($identifier) || !is_dir($dest)){
			return false;
		}
		if($reset){
			Filesystem::recursiveUnlink($dest);
			if(!is_dir($dest)){
				mkdir($dest, 0777, true);
			}
		}
		return $this->zipArchives[$identifier]->extractTo($dest);
	}

	public function restore(World $world, string $identifier, string $dest, bool $reset = true): bool{
		if(!$this->isValid($identifier) || !is_dir($dest)){
			return false;
		}
		foreach($world->getPlayers() as $player){
			$player->teleport($this->worldManager->getDefaultWorld()?->getSafeSpawn());
		}
		MiniGameHandler::getPlugin()->getScheduler()->scheduleTask(new ClosureTask(function() use ($world, $identifier, $dest, $reset): void{
			$this->worldManager->unloadWorld($world);
			$this->extractTo($identifier, $dest, $reset);
			$this->worldManager->loadWorld($world->getDisplayName());
		}));
		return true;
	}

	public function get(string $identifier): ?ZipArchive{
		return $this->zipArchives[$identifier] ?? null;
	}
}