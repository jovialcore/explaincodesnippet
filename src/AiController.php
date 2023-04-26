<?php

declare(strict_types=1);

namespace ExplainImgSnippetAi;

class AiController
{
    private $chatgptapikey;
    public function __construct(string $chatgptapikey)
    {
        $this->chatgptapikey = $chatgptapikey;
    }

    public function __invoke()
    {
        echo "extract the texxxxxxxxxxxxxxxxgtttttttttt from this apikey " . $this->chatgptapikey;
        exit;
    }
}
