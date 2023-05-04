<?php

declare(strict_types=1);

namespace ExplainImgSnippetAi;

use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Orhanerday\OpenAi\OpenAi;
use Aws\Textract\TextractClient;
use Aws\Textract\Exception\TextractException;

use Laminas\Diactoros\ServerRequestFactory;

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
        $request = ServerRequestFactory::fromGlobals();

        // Get the uploaded file from the request
        $file = $request->getUploadedFiles()['image'];

        $textractClient = new TextractClient([
            'version' => 'latest',
            'region' => getenv('AWS_REGION'),
            'credentials' => [
                'key'    => getenv('AWS_KEY'),
                'secret' => getenv('AWS_SECRET')
            ],
            'scheme' => 'https',
        ]);



        try {
            $result = $textractClient->detectDocumentText([
                'Document' => [
                    'Bytes' => file_get_contents($file->getStream()->getMetadata('uri'))
                ]
            ]);

            $words = "";
            foreach ($result->get('Blocks') as $block) {
                if ($block['BlockType'] != 'WORD') {
                    continue;
                }

                $words = $words . $block['Text'] . " ";
            }
        } catch (TextractException $e) {
            // output error message if fails
            echo $e->getMessage();
        }

        $openaikey = getenv('OPENAI_API_KEY');

        $open_ai = new OpenAi($openaikey);

        $chat =   $open_ai->chat([
            'model' => 'gpt-3.5-turbo',
            'messages' => [
 
                [
                    "role" => "user",
                    "content" => "Explain this code $: '$words' return it in a proper format "
                ],
            ],
            'temperature' => 1.0,
            'max_tokens' => 4000,
            'frequency_penalty' => 0,
            'presence_penalty' => 0,
        ]);


        // decode response
        // $d = json_decode($chat);

        // Get Content
        dd($chat);
        // return new JsonResponse($data);
    }
}
