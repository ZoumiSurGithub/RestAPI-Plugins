<?php

namespace Zoumi\Link\loaders;

use pocketmine\Server;
use Zoumi\Link\Link;
use Zoumi\Link\listeners\PlayerListener;

class ListenerLoader
{

    /**
     * @return void
     */
    public static function init(): void
    {
        $plugin = Link::getInstance();
        $plugin->getServer()->getPluginManager()->registerEvents(new PlayerListener(), $plugin);
    }

}