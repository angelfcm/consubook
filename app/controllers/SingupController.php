<?php

use Phalcon\Forms\Element\Text;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\StringLength;

class SingupController extends ControllerBase
{
	public $error = array("a"=>2);

    public function indexAction()
    {
    	if ( !$this->acl->isAllowed($this->user->getRole(), 'User', 'singup') ) {
    		$this->flash->error("Debes cerrar sesión para crear una cuenta nueva...");

    		$this->response->redirect('index/index');

    		return false;
    	}

    	if ( $this->request->isPost() &&  $this->validateForm() ) {

    	


    		//if ( $this)

			//$this->response->redirect('singup/success');
    		return true;
    	}

    	$this->getForm();
    }

    public function validateForm() {

    	$this->validation->add('username', new \Phalcon\Validation\Validator\PresenceOf(array(
		        'message' => 'Nombre de usuario requerido'
		)));

		$this->error['validation'] = $this->validation->validate($_POST);

    	return (count($this->error['validation']) == 0);
    }

    public function getForm() {

    	$this->view->setVar('error', $this->error);
    }

    public function successAction()
    {

    }

}

