<?php

namespace Zoumi\Money;

use CortexPE\Commando\exception\HookAlreadyRegistered;
use CortexPE\Commando\PacketHooker;
use pocketmine\plugin\PluginBase;
use pocketmine\plugin\PluginLogger;
use pocketmine\utils\SingletonTrait;

class Money extends PluginBase
{
    use SingletonTrait;

    private \Logger $logger;

    protected function onLoad(): void
    {
        self::setInstance($this);
        $this->logger = new PluginLogger($this->getLogger(), "RestAPI");
    }

    /**
     * @return void
     * @throws HookAlreadyRegistered
     */
    protected function onEnable(): void
    {
        if (!PacketHooker::isRegistered()) {
            PacketHooker::register($this);
        }
    }

    public function getRestAPILogger(): \Logger
    {
        return $this->logger;
    }

}