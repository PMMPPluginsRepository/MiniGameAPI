<?php

declare(strict_types=1);

namespace skh6075\api\minigame\task;

use skh6075\api\minigame\game\GameRoom;

abstract class GameRoomWaitingTask extends MiniGameTask{
	public const MODE_WAIT = 0;
	public const MODE_START_QUEUE = 1;
	public const MODE_START = 2; //Handle these constant directly by overriding the class

	protected int $mode = self::MODE_WAIT;

	protected int $start_queue = 0;

	public function __construct(GameRoom $room, private int $queue){
		parent::__construct($room);
	}

	final public function getMode(): int{
		return $this->mode;
	}

	public function setMode(int $mode = self::MODE_WAIT): void{
		$this->mode = $mode;
	}

	public function onRun() : void{
		if(($this->mode === self::MODE_START_QUEUE) && --$this->start_queue <= 0){
			/** Game start check override setMode method! */
			$this->setMode(self::MODE_START);
		}
		$this->broadcastActionBarMessage($this->getRoomInformationMessage());
	}

	/**
	 * Method for showing game room information to game participants in a popup
	 *
	 * @return string
	 */
	abstract public function getRoomInformationMessage(): string;

	/**
	 * Send one message when game start countdown starts
	 * handle the message
	 *
	 * @return string
	 */
	abstract public function getRoomGameStartQueueMessage(): string;

	/**
	 * Message to be sent when the game starts
	 *
	 * @return string
	 */
	abstract public function getRoomGameStartMessage(): string;
}