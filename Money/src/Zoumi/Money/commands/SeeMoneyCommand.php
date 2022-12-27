<?php

namespace Zoumi\Money\commands;

use CortexPE\Commando\args\TargetArgument;
use CortexPE\Commando\BaseCommand;
use CortexPE\Commando\exception\ArgumentOrderException;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\Server;
use Zoumi\Money\async\GetRequestAsyncTask;
use Zoumi\Money\async\PostRequestAsyncTask;
use Zoumi\Money\Money;

class SeeMoneyCommand extends BaseCommand
{

    /**
     * @throws ArgumentOrderException
     */
    protected function prepare(): void
    {
        $this->registerArgument(0, new TargetArgument("target", true));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if ($sender instanceof Player) {
            if (!isset($args["target"])) {
                Server::getInstance()->getAsyncPool()->submitTask(new PostRequestAsyncTask(
                    "http://moonlight-mcbe.fr:3000/api/money/user/get/" . Money::getToken() . "&" . $sender->getName(),
                    ["token" => Money::getToken(), "username" => $sender->getName()],
                    function (array $result) use ($sender) {
                        if ($result["code"] === 0) {
                            $sender->sendMessage(str_replace(["{money}"], [$result["money"]], Money::getMessage("see-yourself")));
                        }
                    }
                ));
            } else {
                Server::getInstance()->getAsyncPool()->submitTask(new PostRequestAsyncTask(
                    "http://moonlight-mcbe.fr:3000/api/money/user/get/" . Money::getToken() . "&" . $args["target"],
                    ["token" => Money::getToken(), "username" => $args["target"]],
                    function (array $result) use ($sender, $args) {
                        if ($result["code"] === 0) {
                            $sender->sendMessage(str_replace(["{target}", "{money}"], [$args["target"], $result["money"]], Money::getMessage("see-target")));
                        } elseif ($result["code"] === 3) {
                            $sender->sendMessage(Money::getMessage("player-not-found"));
                        }
                    }
                ));
            }
        }
    }

}