<?php

// Es importante no usar ControllerBase y ser llamado unicamente por dispatcher->forward() para no llamar dos veces al inicializador de ControllerBase y duplicar datos de salida

use Phalcon\Mvc\Controller;

class ErrorController extends ControllerBase
{

    public function notFoundAction()
    {

        //$this->view->setVar('message', 'Página no encontrada!');
        //Set status code
        $this->response->setRawHeader("HTTP/1.1 404 OK");
        $this->response->setRawHeader('status: 404');
        //Set the content of the response
        $this->response->setContent("Sorry, the page doesn't exist");

        //Send response to the client
        $this->response->send();
    }

    public function uncaughtExceptionAction()
    {
        
        $this->view->setVar('exception', $this->dispatcher->getParam('exception'));
    }
}
