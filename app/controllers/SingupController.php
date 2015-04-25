<?php

class SingupController extends ControllerBase
{
	public $errors = array();

    public function indexAction()
    {
    	if ( !$this->acl->isAllowed($this->user->getRole(), 'User', 'singup') ) {
    		$this->flash->error("Debes cerrar sesión para crear una cuenta nueva...");

    		$this->response->redirect('index/index');

    		return false;
    	}

    	if ( $this->request->isPost() ) {

    	
    		$username = $this->request->getPost('username');
    		$this->validation->add('username', new \Phalcon\Validation\Validator\PresenceOf(array(
			        'message' => 'Nombre de usuario requerido'
			)));

    		//if ( $this)

			//$this->response->redirect('singup/success');
    		return true;
    	}
    }

    public function successAction()
    {

    }

}

