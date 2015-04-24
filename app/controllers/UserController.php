<?php

class UserController extends ControllerBase
{

    public function indexAction()
    {

    }

    public function singupAction()
    {

    	if ( !$this->acl->isAllowed($this->user->getRole(), 'User', 'singup') ) {
    		$this->flash->error("You don't have access to this module");

    		$this->response->redirect('index/index');
    	}
    }

}

