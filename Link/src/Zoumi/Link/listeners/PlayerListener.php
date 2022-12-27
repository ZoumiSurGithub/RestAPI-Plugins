<?php

namespace Zoumi\Link\listeners;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\Server;
use Zoumi\Link\async\GetRequestAsyncTask;
use Zoumi\Link\async\PostRequestAsyncTask;
use Zoumi\Link\Link;

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
            "http://moonlight-mcbe.fr:3000/api/link/user/create/" . Link::getToken() . "&" . $player->getName(),
            ["token" => Link::getToken(), "username" => $player->getName()]
        ));
    }

}