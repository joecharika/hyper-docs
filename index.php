<?php

use Hyper\Functions\Debug;
use Hyper\SQL\Query;
use Hyper\SQL\SQLType;

require __DIR__ . '/vendor/autoload.php';

new \Hyper\Application\HyperApp(
   'Hyper Docs',
   'auto',
   false,
   new \Hyper\Application\HyperEventHook([
       'onRenderingStarting' => function ($event) {
           /** @var Environment $twig */
           $twig = $event->data;

           $twig->addGlobal('docsVersion', '1.02-pre');
       }
   ])
);

// $q = new Query();

// $q->createTable('persons',
//     [
//         'id' => SQLType::int()->primaryKey()->notNull()->autoIncrement()->default(uniqid()),
//         'rating' => SQLType::type('text')
//     ]
// );

// Debug::print($q->toSql());

// Debug::print($q->getParams());



//HyperApp::$dbConfig = new DatabaseConfig();

//Debug::dump(HyperApp::$dbConfig);

//$db = new \Hyper\Database\DatabaseContext('user');

//Debug::dump($db->deleteWhere('username', '=', 'test'));

//Debug::dump(Obj::toInstance('\\Models\\User', ['id' => 1]));