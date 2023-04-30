<?php

declare(strict_types=1);

namespace ExplainImgSnippetAi;

use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Orhanerday\OpenAi\OpenAi;

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
        $data =   ['message' => "Hello, {$this->chatgptapikey} world!"];

        $openaikey = getenv('OPENAI_API_KEY');


        return new JsonResponse($data);
    }
}
