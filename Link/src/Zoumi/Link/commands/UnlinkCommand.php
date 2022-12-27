<?php

namespace Zoumi\Link\commands;

use CortexPE\Commando\BaseCommand;
use CortexPE\Commando\constraint\InGameRequiredConstraint;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\Server;
use Zoumi\Link\async\GetRequestAsyncTask;
use Zoumi\Link\async\PostRequestAsyncTask;
use Zoumi\Link\Link;

class UnlinkCommand extends BaseCommand
{

    /**
     * @return void
     */
    protected function prepare(): void
    {
        $this->addConstraint(new InGameRequiredConstraint($this));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if (!$sender instanceof Player) return;
        Server::getInstance()->getAsyncPool()->submitTask(new GetRequestAsyncTask(
            "http://moonlight-mcbe.fr:3000/api/link/user/get/" . Link::getToken() . "&" . $sender->getName(),
            ["token" => Link::getToken(), "username" => $sender->getName()],
            function (array $result) use ($sender) {
                var_dump($result);
                if ($result["code"] === 3) {
                    $sender->sendMessage(Link::getMessage("player-not-found"));
                } elseif ($result["code"] === 0) {
                    if (empty($result["content"]["discord_id"])) {
                        $sender->sendMessage(Link::getMessage("already-unlink"));
                    } else {
                        Server::getInstance()->getAsyncPool()->submitTask(new PostRequestAsyncTask(
                            "http://moonlight-mcbe.fr:3000/api/link/user/unlink/" . Link::getToken() . "&" . $sender->getName(),
                            ["token" => Link::getToken(), "username" => $sender->getName()],
                            function (array $result) use ($sender) {
                                if ($result["code"] === 0) {
                                    $sender->sendMessage(Link::getMessage("unlink"));
                                }
                            }
                        ));
                    }
                }
            }
        ));
    }

}