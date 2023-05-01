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


        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, Authorization");
    }

    public function api(): ResponseInterface
    {
        $request = ServerRequestFactory::fromGlobals();

        // Get the uploaded file from the request
        $file = $request->getUploadedFiles()['image'];
        dd($file);
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
                    'Bytes' => file_get_contents('/opt/lampp/htdocs/Projects/explain-img-snippet-ai/src/Snap.png')
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
