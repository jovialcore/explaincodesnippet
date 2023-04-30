<?php

declare(strict_types=1);

namespace ExplainImgSnippetAi;

use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Orhanerday\OpenAi\OpenAi;
use Aws\Textract\TextractClient;
use Aws\Textract\Exception\TextractException;

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
        $textractClient = new TextractClient([
            'version' => 'latest',
            'region' => getenv('AWS_REGION'),
            'credentials' => [
                'key'    => getenv('AWS_KEY'),
                'secret' => getenv('AWS_SECRET')
            ],
            'scheme' => 'https',
        ]);
        $data =   ['message' => "Hello, {$this->chatgptapikey} world!"];

        try {
            $result = $textractClient->detectDocumentText([
                'Document' => [
                    'Bytes' => file_get_contents('/opt/lampp/htdocs/Projects/explain-img-snippet-ai/src/code.png')
                ]
            ]);

            foreach ($result->get('Blocks') as $block) {
                if ($block['BlockType'] != 'WORD') {
                    continue;
                }

                echo $block['Text'] . " ";
            }
        } catch (TextractException $e) {
            // output error message if fails
            echo $e->getMessage();
        }

        $openaikey = getenv('OPENAI_API_KEY');


        return new JsonResponse($data);
    }
}
