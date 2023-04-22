<?php

declare(strict_types=1);


use DI\ContainerBuilder;
use Controller\Aicontroller;

use function Di\create;

require_once dirname(__DIR__) . '/vendor/autoload';


$aicontroller  = new Aicontroller();
$aicontroller->extractCodeText();



$containerBuilder = new  ContainerBuilder();
$containerBuilder->useAutowiring(false);
//$containerBuilder->useAnnotations(false); // annotations are now disabled by default no need to add them

/// add definitions
$containerBuilder->addDefinitions(
    [
        Aicontroller::class => create(Aicontroller::class)
    ]
);
$container = $containerBuilder->build();
// let Di create the objects aka instantiate
$extractText = $container->get(\Controller\Aicontroller::class);
