<?php

namespace Zoumi\Money\listeners;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\Server;
use Zoumi\Money\async\GetRequestAsyncTask;
use Zoumi\Money\async\PostRequestAsyncTask;
use Zoumi\Money\Money;

class PlayerListener implements Listener
{

    /**
     * @param PlayerLoginEvent $event
     * @return void
     */
    public function onLogin(PlayerLoginEvent $event): void
    {
        $player = $event->getPlayer();
        Server::getInstance()->getAsyncPool()->submitTask(new PostRequestAsyncTask(
            "http://moonlight-mcbe.fr:3000/api/money/user/create/" . Money::getToken() . "&" . $player->getName(),
            ["token" => Money::getToken(), "username" => $player->getName()]
        ));
    }

}