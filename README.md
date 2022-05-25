# MiniGameAPI
A library that makes it easy to create minigame plugins

## Usage
MiniGameAPI makes developing minigames a little easier.

**NOTE**: To use the MiniGameAPI library, you need to register a plugin with MiniGameHandler.
```php
use skh6075\api\minigame\MiniGameHandler;

protected function onEnable(): void{
    if(!MiniGameHandler::isRegistered()){
        MiniGameHandler::register($this);
    }
}
```

## Create a minigame room
```php
use skh6075\api\minigame\game\GameRoom;
use skh6075\api\minigame\session\MiniGamePlayerSessionStorage;

final class SkyWarsGameRoom extends GameRoom{

    /** @override */
    public function start() : void{
        //your code
    }
    
    /**
     * Manage sessions for minigame
     *
     * @return MiniGamePlayerSessionStorage
     */
    public function session() : MiniGamePlayerSessionStorage{
        return MiniGamePlayerSessionStorage::create();
    }
}
```
```MiniGameSessionStorage``` is a minigame player session.
This is a session where you can manage data deletion and player options when the player client connection is terminated.

### Create a minigame team
```php
use skh6075\api\minigame\team\Team;
use skh6075\api\minigame\team\TeamManager;

class RedTeam extends Team{
    public function __construct() {
        parent::__construct("red");
    }
}
class BlueTeam extends Team{
    public function __construct() {
        parent::__construct("blue");
    }
}

$teamManager = new TeamManager(
    new RedTeam(),
    new BlueTeam()
);
```
If the game requires team roles, override the ```Team``` class and put it in the ```TeamManager``` and put it in the minigame class.

### MiniGame World Auto Generate/Restore
```php
use skh6075\api\minigame\generator\MapGenerator;

$mapGenerator = new MapGenerator([
    "skywars" => "worlds/skywars.zip",
    "lobby" => "worlds/skywars_lobby.zip";
]);
```
It automatically sets up and cleans the game map.

## Register a GameRoom
```php
use skh6075\api\minigame\game\GameRoom;

/**
 * @phpstan-var array<int, GameRoom>
 * @var GameRoom[]
 */
private array $rooms = [];

public function createGameRoom(int $roomId): void{
    $room = new SkyWarsGameRoom(
        name: "SkyWars{$roomId}",
        mapGenerator: $mapGenerator,
        taskHandler: null,
        minPlayerCount: 4,
        maxPlayerCount: 8,
        teamManager: $teamManager
    );
    $this->rooms[$roomId] = $room;
}
```

## GameRoom TaskHandler
Implement the task in the game room.
```php
use skh6075\api\minigame\task\GameRoomWaitingTask;

class SkyWarsWaitingTask extends GameRoomWaitingTask{

    /**
     * Method for showing game room information to game participants in a popup
     *
     * @return string
     */
    public function getRoomInformationMessage(): string{
        return "Waiting Player: {$this->room->getParticipants()} / {$this->room->getMaxPlayerCount()}";
    }

    /**
     * Send one message when game start countdown starts
     * handle the message
     *
     * @return string
     */
    public function getRoomGameStartQueueMessage(): string{
         return "The game starts in %s seconds";
    }

    /**
     * Message to be sent when the game starts
     *
     * @return string
     */
    public function getRoomGameStartMessage(): string{
        return "starting Game";
    }
}
```