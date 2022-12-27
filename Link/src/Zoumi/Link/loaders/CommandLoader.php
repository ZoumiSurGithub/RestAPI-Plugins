<?php

namespace Zoumi\Link\loaders;

use Zoumi\Link\commands\LinkCommand;
use Zoumi\Link\commands\UnlinkCommand;
use Zoumi\Link\Link;

class CommandLoader
{

    public static function init(): void
    {
        $plugin = Link::getInstance();
        $plugin->getServer()->getCommandMap()->registerAll("Link", [
            new LinkCommand($plugin, "link", "Allows you to obtain your code to link up."),
            new UnlinkCommand($plugin, "unlink", "Allows you to unlink to your discord account.")
        ]);
    }

}