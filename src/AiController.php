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

        $originalString = "
        ```
         This code is defining a PHP class with three properties: chatgptapikey, response, and a constructor method. 
         
         The chatgptapikey property is declared as private, meaning it can only be accessed within the class itself. 
         
         The response property is also declared as private and is expected to be an instance of the ResponseInterface class.
         
         The constructor method is declared as public. It takes in two parameters, a string chatgptapikey and an instance of the ResponseInterface class. 
         
         Within the constructor method, the response property is assigned the value of the response parameter. The chatgptapikey property is also assigned the value of the chatgptapikey parameter.
         
         Proper format code:
         
         ```
         class ExampleClass 
         {
             private chatgptapikey;
             private response;
         
             public function __construct(string chatgptapikey, ResponseInterface response)
             {
                  = response;
                 = chatgptapikey;
             }
         }
         ```
         ";



        $substring = substr($originalString, strpos($originalString, "Proper format code"));

        if ($substring !== false) {
            dd($substring);
        }
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
                    "content" => "Explain this code '$words' and start with 'Explanation: //explanation should be her/next'. The return code in a proper format and start with 'code : //the code should be here/next' "
                ],
            ],
            'temperature' => 1.0,
            'max_tokens' => 4000,
            'frequency_penalty' => 0,
            'presence_penalty' => 0,
        ]);


        // decode response
        $d = json_decode($chat);

        // Get Content
        $result = $d->choices[0]->message->content;

        $code = substr($result, strpos($result, "Proper format code"));
        $explanationStart = substr($result, strpos($result, 'Explanation'));
        $explanationEnd = substr($result, strpos($result, 'Explanation'));
    }
}
