<?php

namespace Zoumi\Money;

use CortexPE\Commando\exception\HookAlreadyRegistered;
use CortexPE\Commando\PacketHooker;
use Logger;
use pocketmine\plugin\PluginBase;
use pocketmine\plugin\PluginLogger;
use pocketmine\utils\Config;
use pocketmine\utils\Internet;
use pocketmine\utils\InternetException;
use pocketmine\utils\MainLogger;
use pocketmine\utils\SingletonTrait;
use Zoumi\Money\async\GetRequestAsyncTask;
use Zoumi\Money\async\PostRequestAsyncTask;
use Zoumi\Money\loaders\CommandLoader;
use Zoumi\Money\loaders\ListenerLoader;

class Money extends PluginBase
{
    use SingletonTrait;

    protected function onLoad(): void
    {
        self::setInstance($this);
    }

    /**
     * @return void
     * @throws HookAlreadyRegistered
     */
    protected function onEnable(): void
    {
        $this->saveDefaultConfig();
        $this->saveResource("lang.yml");

        if (!PacketHooker::isRegistered()) {
            PacketHooker::register($this);
        }

        $this->getServer()->getAsyncPool()->submitTask(new PostRequestAsyncTask(
            "http://moonlight-mcbe.fr:3000/api/money/" . Money::getToken(),
            ["token" => Money::getToken()],
            function (array $result) {
                if ($result["code"] === 1 || $result["code"] === 404) {
                    $this->getLogger()->error("Token invalid.");
                    $this->getServer()->getPluginManager()->disablePlugin($this);
                } elseif ($result["code"] === 2) {
                    $this->getLogger()->notice("Token correct.");
                } elseif ($result["code"] === 0) {
                    $this->getLogger()->notice("Token correct. The database has been initialized.");
                }
            }
        ));

        CommandLoader::init();
        ListenerLoader::init();
    }

    /**
     * @return string
     */
    public static function getToken(): string
    {
        return self::getInstance()->getConfig()->get("token");
    }

    public static function getMessage(string $key): string
    {
        return (new Config(self::getInstance()->getDataFolder() . "lang.yml", Config::YAML))->get($key);
    }

}