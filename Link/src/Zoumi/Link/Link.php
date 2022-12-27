<?php

namespace Zoumi\Link;

use CortexPE\Commando\exception\HookAlreadyRegistered;
use CortexPE\Commando\PacketHooker;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\utils\SingletonTrait;
use Zoumi\Link\async\PostRequestAsyncTask;
use Zoumi\Link\loaders\CommandLoader;
use Zoumi\Link\loaders\ListenerLoader;

class Link extends PluginBase
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
            "http://moonlight-mcbe.fr:3000/api/link/" . Link::getToken(),
            ["token" => Link::getToken()],
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

        ListenerLoader::init();
        CommandLoader::init();
    }

    /**
     * @return string
     */
    public static function getToken(): string
    {
        return self::getInstance()->getConfig()->get("token");
    }

    /**
     * @param string $key
     * @return string
     */
    public static function getMessage(string $key): string
    {
        return (new Config(self::getInstance()->getDataFolder() . "lang.yml", Config::YAML))->get($key);
    }

}