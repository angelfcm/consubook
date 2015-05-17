<?php

use Phalcon\Validation\Validator;

class UserController extends ControllerBase
{

	public $output = array(
		'errors' => array(),
		'data' => array()
	);

    public function indexAction()
    {

    }

    public function loginAction()
    {
    	$this->view->disable();
    	$this->output['valid'] = false;

    	if ( $this->acl->isAllowed($this->user->getRole(), 'User', 'login') ) {

	    	if ( $this->request->isPost() ) {

	    		if ( $this->validateForm() ) {

		    		if ( ! $this->user->authenticate( $this->request->getPost('username'), $this->request->getPost('email'), $this->request->getPost('password') ) ) {
		    			$this->output['errors'][] = "Nombre de usuario o contraseña incorrecta.";
		    		} else {

						$this->output['valid'] = true;

                        if ( $this->request->hasPost('remember') &&  $this->request->getPost('remember') ) {
                            setcookie('username', $this->request->getPost('username'), time()+60*60*24*30, $this->config->application->baseUri);
                            setcookie('email', $this->request->getPost('email'), time()+60*60*24*30, $this->config->application->baseUri);
                            setcookie('password', $this->crypt->encrypt($this->request->getPost('password')), time()+60*60*24*30, $this->config->application->baseUri);
                        }
                    }
				}
	    	} else {
	    		$this->output['errors'][] = "Bad request data!";
	    	}
    	} else {

    		$this->output['errors'][] = "Not allowed!";
    	}

    	echo json_encode($this->output);
    }

    public function logoutAction()
    {   
    	if ( $this->user->isAuthenticated() )
    		$this->user->unauthenticate();

    	$this->response->redirect('');
    }

    public function validateForm()
    {
    	$_POST = json_decode(file_get_contents("php://input"), true);

	    $this->validation->add('password', new Validator\PresenceOf(array(
	    	'message' => "La contraseña es requerida",
		)));

        foreach ( $this->validation->validate($_POST) as $error ) {
            $this->output['errors'][] = $error->getMessage();
        }

        if ( !$this->request->hasPost('username') && !$this->request->hasPost('email') || empty($this->request->getPost('username')) && empty($this->request->getPost('email')) )
        	$this->output['errors'][] = 'Usuario o email requerido';

        if ( ! $this->request->hasPost('password') )
        	$this->output['errors'][] = 'Contraseña requerida';

 		return (count($this->output['errors']) == 0);
    }

}

