<?php

namespace Zoumi\Link\async;

use pocketmine\scheduler\AsyncTask;
use Zoumi\Link\Link;

class PostRequestAsyncTask extends AsyncTask
{
    private string $request;
    private $callable;
    private array $body;

    public function __construct(string $request, array $body, ?callable $callable = null)
    {
        $this->request = $request;
        $this->callable = $callable;
        $this->body = $body;
    }

    public function onRun(): void
    {
        try {
            $context = stream_context_create(["http" => [
                'method' => 'POST',
                'header' => 'Content-type: application/json',
                'content' => json_encode($this->body)
            ],
                'ssl' => [
                    'SNI_enabled' => true,
                    'SNI_server_name' => "moonlight-mcbe.fr"
                ]]);
            $result = file_get_contents($this->request, false, $context);
            $this->setResult(json_decode($result, null, 512, JSON_OBJECT_AS_ARRAY));
        } catch (\Exception $exception) {
            $this->setResult(["code" => 404]);
        }
    }

    public function onCompletion(): void
    {
        Link::getInstance()->getLogger()->debug("Request post send, result: \n" . json_encode($this->getResult()));
        if (!empty($this->callable)) {
            call_user_func($this->callable, $this->getResult());
        }
    }

}