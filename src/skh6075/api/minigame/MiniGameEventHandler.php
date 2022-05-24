<?php

declare(strict_types=1);

namespace skh6075\api\minigame;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerQuitEvent;
use skh6075\api\minigame\event\PlayerGameRoomJoinEvent;
use skh6075\api\minigame\event\PlayerGameRoomQuitEvent;
use skh6075\api\minigame\session\MiniGamePlayerSessionManager;

final class MiniGameEventHandler implements Listener{

	public function __construct(private MiniGamePlayerSessionManager $sessionManager){}

	/** @priority MONITOR */
	public function onPlayerQuitEvent(PlayerQuitEvent $event): void{
		$this->sessionManager->getSession($player = $event->getPlayer())?->getGameRoom()->removeParticipant($player);
	}

	/** @priority MONITOR */
	public function onPlayerGameRoomJoinEvent(PlayerGameRoomJoinEvent $event): void{
		if(!$event->isCancelled()){
			$this->sessionManager->createSession($event->getPlayer(), $event->getStorage(), $event->getGameRoom());
		}
	}

	/** @priority MONITOR */
	public function onPlayerGameRoomQuitEvent(PlayerGameRoomQuitEvent $event): void{
		$session = $this->sessionManager->getSession($player = $event->getPlayer());
		if($session === null || !$session->getStorage()->isQuitDestroySession()){
			return;
		}
		$this->sessionManager->removeSession($player);
	}
}