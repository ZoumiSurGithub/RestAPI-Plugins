<?php

namespace Zoumi\Money\commands;

use CortexPE\Commando\args\FloatArgument;
use CortexPE\Commando\args\TargetArgument;
use CortexPE\Commando\BaseCommand;
use CortexPE\Commando\exception\ArgumentOrderException;
use pocketmine\command\CommandSender;
use pocketmine\Server;
use Zoumi\Money\async\GetRequestAsyncTask;
use Zoumi\Money\async\PostRequestAsyncTask;
use Zoumi\Money\Money;

class RemoveMoneyCommand extends BaseCommand
{

    /**
     * @throws ArgumentOrderException
     */
    protected function prepare(): void
    {
        $this->setPermission("money.command.removemoney");
        $this->registerArgument(0, new TargetArgument("target"));
        $this->registerArgument(1, new FloatArgument("money"));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if (!$this->testPermission($sender)) return;
        Server::getInstance()->getAsyncPool()->submitTask(new GetRequestAsyncTask(
            "http://moonlight-mcbe.fr:3000/api/money/user/get/" . Money::getToken() . "&" . $args["target"],
            ["token" => Money::getToken(), "username" => $args["target"]],
            function (array $result) use ($sender, $args) {
                if ($result["code"] === 0) {
                    Server::getInstance()->getAsyncPool()->submitTask(new PostRequestAsyncTask(
                        "http://moonlight-mcbe.fr:3000/api/money/user/set/" . Money::getToken() . "&" . $args["target"] . "&" . ($result["money"] - $args["money"]),
                        ["token" => Money::getToken(), "username" => $args["target"], "money" => ($result["money"] - $args["money"])],
                        function (array $result1) use ($sender, $args) {
                            if ($result1["code"] === 0) {
                                $sender->sendMessage(str_replace(["{target}", "{money}"], [$args["target"], $args["money"]], Money::getMessage("remove-money")));
                            } else {
                                $sender->sendMessage(Money::getMessage("internal-server-error"));
                            }
                        }
                    ));
                } elseif ($result["code"] === 3) {
                    $sender->sendMessage(Money::getMessage("player-not-found"));
                }
            }
        ));
    }

}