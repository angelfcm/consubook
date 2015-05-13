<?php

$router = new Phalcon\Mvc\Router();

$router->add('/singup/confirmEmail/{code}', array(
        'controller' => 'singup',
        'action' => 'confirmEmail'
))->setName('confirm');

return $router;