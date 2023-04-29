<?php

declare(strict_types=1);

namespace ExplainImgSnippetAi;

use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Orhanerday\OpenAi\OpenAi;
use thiagoalessio\TesseractOCR\TesseractOCR;
use Intervention\Image\ImageManagerStatic as Image;

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



        // set the path to the image file

        $image_file = 'code.png';
        $dir_name = dirname(__FILE__);
        $image = $dir_name . '/' . $image_file;


        $image = Image::make('path/to/image.jpg');


        $openaikey = getenv('OPENAI_API_KEY');


        return new JsonResponse($data);
    }
}
