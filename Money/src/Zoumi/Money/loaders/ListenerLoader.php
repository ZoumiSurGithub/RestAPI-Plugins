<?php

namespace Zoumi\Money\loaders;

use Zoumi\Money\listeners\PlayerListener;
use Zoumi\Money\Money;

class ListenerLoader
{

    public static function init(): void
    {
        $plugin = Money::getInstance();
        $plugin->getServer()->getPluginManager()->registerEvents(new PlayerListener(), $plugin);
    }

}