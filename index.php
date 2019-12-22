<?php

use Hyper\Application\HyperApp;
use Hyper\Application\HyperEventHook;
use Twig\Environment;

require __DIR__ . '/vendor/autoload.php';

new HyperApp(
   'Hyper Docs',
   false,
   new HyperEventHook([
       'onRenderingStarting' => function ($event) {
           /** @var Environment $twig */
           $twig = $event->data;

           $twig->addGlobal('docsVersion', '1.02.3-pre');
       }
   ])
);
