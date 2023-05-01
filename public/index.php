<?php

declare(strict_types=1);


require_once dirname(__DIR__) . '/vendor/autoload.php';

use DI\Container;
use DI\ContainerBuilder;
use ExplainImgSnippetAi\AiController;
use FastRoute\RouteCollector;
use Relay\Relay;
use Laminas\Diactoros\ServerRequestFactory; // more like guzzlehttp client ---update: it is not


use Laminas\Diactoros\Request;

use Laminas\Diactoros\Response;
use Middlewares\FastRoute;
use Middlewares\RequestHandler;
use Narrowspark\HttpEmitter\SapiEmitter;
use Psr\Container\ContainerInterface;
use Whoops\Run;
use Whoops\Handler\PrettyPageHandler;

use Dotenv\Dotenv;

use function Di\create;
use function Di\get;

use function FastRoute\simpleDispatcher;

// error debug
$whoops = new Run(); // do dependency injection here
$whoops->pushHandler(new PrettyPageHandler());
$whoops->register();

// load environmental vars
$dotenv  = Dotenv::createUnsafeImmutable(__DIR__ . '/../'); //more info : https://dev.to/walternascimentobarroso/dotenv-in-php-45mn /// I can't use createImmutable because I think my php.ini settings will now allow me use $_ENV()
$dotenv->load();

// add your containers

$containerBuilder = new  ContainerBuilder();
$containerBuilder->useAutowiring(false);
//$containerBuilder->useAnnotations(false); // annotations are now disabled by default no need to add them

/// add definitions // register classes so that the DI can automatically create instances of those class in whatever situtatuion (contructor, methods injections, etc) they are called

// you can either use factories...I'm using objects below. Reason ebing that it is easier
$containerBuilder->addDefinitions(
    [
        'response' => function () {
            return new Response();
        },

        AiController::class => create()->constructor("I was  injected'", get('response')),


    ]
);

$container = $containerBuilder->build();


// defining the routes
$routes = simpleDispatcher(function (RouteCollector $r) {
    $r->post('/api/extract/imagetext', [AiController::class, 'api']);
});

// the different layers the request coming in has to pass before it gets to the application

$middleware[] = new FastRoute($routes); // middleware/layer to match url patterns
// if not for dependency injection, I would have instanciated the AiCOntroller class and pass it on the request handler, but I don't have to cos the DI is handling that
$middleware[] = new RequestHandler($container); // middleware/layer to return response from server back to the client base on the routing path/specification
$requestHandler = new Relay($middleware); //  put all the middlewares in the pipeline also know as dispatcher //also known as middleware dispatcher

$response = $requestHandler->handle(ServerRequestFactory::fromGlobals()); // pull all middlewares in the pipeline using the current http method returned

// emitters: baiscally doing the "header() or echo" but it implements PSR-7
$emitter = new SapiEmitter();
return $emitter->emit($response); // send response
