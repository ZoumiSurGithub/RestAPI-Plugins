<?php

namespace Zoumi\Money\async;

use pocketmine\scheduler\AsyncTask;
use pocketmine\utils\Internet;
use Zoumi\Money\Money;

class PostRequestAsyncTask extends AsyncTask
{
    private string $request;
    private $result;
    private array $args = [];

    public function __construct(string $request, array $args, callable $result)
    {
        $this->request = $request;
        $this->result = $result;
        $this->args = $args;
    }

    public function onRun(): void
    {
        $result = Internet::postURL($this->request, $this->args);
        $this->setResult($result);
    }

    public function onCompletion(): void
    {
        Money::getInstance()->getRestAPILogger()->debug("Request post send, result: \n" . $this->getResult());
        call_user_func($this->result, $this->getResult());
    }

}