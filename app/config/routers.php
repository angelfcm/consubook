<?php

$router = new Phalcon\Mvc\Router();

$router->add('/singup/confirmEmail/{code}', array(
        'controller' => 'singup',
        'action' => 'confirmEmail'
))->setName('confirm');

$router->add('/search/{filter}/{query}/([0-9]*)', array(
        'controller' => 'books',
        'action' => 'search',
        'page' => 3
))->setName('search');

$router->add('/book/{title}', array(
        'controller' => 'books',
        'action' => 'showBook'
))->setName('book');

return $router;