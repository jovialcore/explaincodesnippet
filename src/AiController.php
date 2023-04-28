<?php

declare(strict_types=1);

namespace ExplainImgSnippetAi;

use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;

class AiController
{
    private $chatgptapikey;
    private $response;

    public function __construct(string $chatgptapikey, ResponseInterface $response)
    {
        $this->response = $response;
        $this->chatgptapikey = $chatgptapikey;
    }



    public function api(): ResponseInterface
    {
        $apiKey =  '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';
        $data =   ['message' => "Hello, {$this->chatgptapikey} world!"];
        return new JsonResponse($data);
    }
}
