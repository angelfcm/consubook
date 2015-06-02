<?php

$router = new Phalcon\Mvc\Router();

$router->add('/confirm/{code}', array(
        'controller' => 'singup',
        'action' => 'confirmEmail'
))->setName('confirm');

$router->add('/singup/confirmEmail/{code}/', array( // con digonal final
        'controller' => 'singup',
        'action' => 'confirmEmail'
))->setName('confirm');

$router->add('/search/{filter}/{query}/([0-9]*)', array(
        'controller' => 'books',
        'action' => 'search',
        'page' => 3
))->setName('search');

$router->add('/search/{filter}/{query}', array( // sin diagonal final
        'controller' => 'books',
        'action' => 'search'
))->setName('search');

$router->add('/search/{filter}/{query}', array( // para busqueda por categorias
        'controller' => 'books',
        'action' => 'search'
))->setName('search');


$router->add('/book/{author}/{title}', array(
        'controller' => 'books',
        'action' => 'showBook'
))->setName('book');

$router->add('/book/{author}/{title}/', array( // con diagonal final
        'controller' => 'books',
        'action' => 'showBook'
))->setName('book');

$router->add('/book-cover/{author}/{title_and_extension}', array(
        'controller' => 'books',
        'action' => 'showBookCover'
))->setName('book-cover');

return $router;