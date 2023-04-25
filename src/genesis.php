<?php

declare(strict_types=1);

namespace ExplainImgSnippetAi;

use Laminas\Diactoros\Response;
use Psr\Http\Message\ResponseInterface;

class genesis
{
    private $a_variable;

    private $response;


    public function __construct(string $a_variale)
    {
        $this->a_variable = $a_variale;
    }


    public function __invoke()
    {
        echo "hi " . $this->a_variable;

        exit;
    }
}
