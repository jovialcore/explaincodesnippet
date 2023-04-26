<?php

declare(strict_types=1);

namespace ExplainImgSnippetAi;

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

    public function __invoke(): ResponseInterface
    {
        $response = $this->response->withHeader('Content-Type', 'text/html');
        $response->getBody()
            ->write("<html><head></head><body>Hello, {$this->chatgptapikey} world!</body></html>");

        return $response;
    }
}
