<?php

namespace Zoumi\Money\loaders;

use Zoumi\Money\commands\AddMoneyCommand;
use Zoumi\Money\commands\RemoveMoneyCommand;
use Zoumi\Money\commands\SeeMoneyCommand;
use Zoumi\Money\commands\SetMoneyCommand;
use Zoumi\Money\Money;

class CommandLoader
{

    public static function init(): void
    {
        $plugin = Money::getInstance();
        $plugin->getServer()->getCommandMap()->registerAll("Money", [
            new SeeMoneyCommand($plugin, "seemoney", "Permet de voir sa monnaie."),
            new AddMoneyCommand($plugin, "addmoney", "Permet d'ajouter de la monnaie à un joueur."),
            new RemoveMoneyCommand($plugin, "removemoney", "Permet de retirer de la monnaie à un joueur."),
            new SetMoneyCommand($plugin, "setmoney", "Permet de définir la monnaie d'un joueur.")
        ]);
    }

}