<?php

namespace CortexPE\Commando\args;

use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;
use pocketmine\player\Player;
use pocketmine\Server;

class TargetArgument extends BaseArgument
{

    protected array $values;

    public function __construct(string $name, bool $optional = false)
    {
        parent::__construct($name, $optional);
        foreach (Server::getInstance()->getOnlinePlayers() as $player) {
            $this->values[] = $player->getName();
        }
    }

    public function getNetworkType(): int
    {
        return AvailableCommandsPacket::ARG_TYPE_TARGET;
    }

    public function canParse(string $testString, CommandSender $sender): bool
    {
        // PM player username validity regex
        return (bool)preg_match("/^(?!rcon|console)[a-zA-Z0-9_ ]{1,16}$/i", $testString);
    }

    public function parse(string $argument, CommandSender $sender): string
    {
        return $argument;
    }

    public function getTypeName(): string
    {
        return "exact_player";
    }

}