<?php

use Phalcon\Forms\Element\Text;
use Phalcon\Validation\Validator;
use Forms\SingupForm;

class SingupController extends ControllerBase
{
	public $output = array(
		'errors' => array(),
		'data' => array()
	);

    protected $account;

    public function indexAction()
    {
    	if ( !$this->acl->isAllowed($this->user->getRole(), 'User', 'singup') ) {
    		$this->flash->error("Debes cerrar sesión para crear una cuenta nueva...");

    		$this->response->redirect('');

    		return false;
    	}

    	if ( $this->request->isPost() &&  $this->validateForm() ) {

            $account = new CbkUserAccount();
            $account->username = $this->request->getPost('username');
            $account->email = $this->request->getPost('email');
            $account->password = $this->security->hash($this->request->getPost('password'));
            $account->firstname = $this->request->getPost('firstname');
            $account->lastname = $this->request->getPost('lastname');
            $account->gender = $this->request->getPost('gender');
            $account->phone = $this->request->getPost('phone');
            $account->created_at = new \Phalcon\Db\RawValue('NOW()');

            $userRole = CbkUserRole::findFirst(array(
                'name=?0', 
                'bind' => array(\Lib\User::ROLE_ACCOUNT)
            ));

            if ( $userRole )
                $account->user_role_id = $userRole->id;

            $account->confirmed = false;
            $account->create();
            $account = CbkUserAccount::findFirst($account->id); // Muestra la fecha creada con NOW() correctamente.

            $this->sendEmailConfirmation($account);
            $this->user->authenticate($account->username, $account->email, $this->request->getPost('password'));

            //$this->response->redirect('singup/success');
            $this->dispatcher->forward(
                array(
                    'controller' => 'singup',
                    'action' => 'success',
                    'params' => array('newAccount' => true)
                )
            );

    		return true;
    	}

    	$this->getForm();
    }

    public function successAction()
    {
        if ( ! $this->dispatcher->getParam('newAccount') )  {
            $this->response->redirect('');
            return false;
        }
    }

    public function checkUsernameAction()
    {
    	$this->view->disable();
    	$this->output['exists'] = true;
    	if ( $this->request->isGet() && $this->request->get('username') ) {
	 	  	$this->output['exists'] = !$this->isUnique($this->request->get('username'), 'username');
    	} else {
    		$this->output['errors'][] = "Bad GET request data.";
    	}

    	echo json_encode($this->output);
    }

    public function checkEmailAction()
    {
    	$this->view->disable();
    	$this->output['exists'] = true;
    	if ( $this->request->isGet() && $this->request->get('email') ) {
	    	$this->output['exists'] = !$this->isUnique($this->request->get('email'), 'email');
    	} else {
    		$this->output['errors'][] = "Bad GET request data.";
    	}

    	echo json_encode($this->output);
    }

    public function showCaptchaAction() 
    {
        $img = new \Lib\Securimage\Securimage();

        // You can customize the image by making changes below, some examples are included - remove the "//" to uncomment

        //$img->ttf_file        = './Quiff.ttf';
        //$img->captcha_type    = Securimage::SI_CAPTCHA_MATHEMATIC; // show a simple math problem instead of text
        //$img->case_sensitive  = true;                              // true to use case sensitve codes - not recommended
        //$img->image_height    = 90;                                // height in pixels of the image
        //$img->image_width     = $img->image_height * M_E;          // a good formula for image size based on the height
        //$img->perturbation    = 0.75;                               // 1.0 = high distortion, higher numbers = more distortion
        //$img->image_bg_color  = new Securimage_Color("#0099CC");   // image background color
        //$img->text_color      = new Securimage_Color("#ff00EA");   // captcha text color
        //$img->num_lines       = 8;                                 // how many lines to draw over the image
        //$img->line_color      = new Securimage_Color("#0000CC");   // color of lines over the image
        //$img->image_type      = SI_IMAGE_JPEG;                     // render as a jpeg image
        //$img->signature_color = new \Lib\Securimage_Color(rand(0, 64),
        //                                           rand(64, 128),
        //                                           rand(128, 255));  // random signature color
        // see securimage.php for more options that can be set
        $img->image_width  = 270;
        $img->image_height = 70; 
        $img->captcha_type= \Lib\Securimage\Securimage::SI_CAPTCHA_WORDS;

        // set namespace if supplied to script via HTTP GET
        if (!empty($_GET['namespace'])) $img->setNamespace($_GET['namespace']);

        $img->show(); 
    }

    public function checkCaptchaAction()
    {
        $this->view->disable();
        $this->output['valid'] = false;
        if ( $this->request->isGet() && $this->request->get('key') ) {
            $this->output['valid'] = $this->validateCaptcha($this->request->get('key'));
        } else {
            $this->output['errors'][] = "Bad GET request data.";
        }

        echo json_encode($this->output);
    }

    protected function validateForm() 
    {
        $this->validation->add('username', new Validator\Regex(array(
            'pattern' => "/^[a-z0-9]{4,24}$/i",
            'message' => "El nombre de usuario debe tener entre 4 y 24 carácteres alfanuméricos de longitud (a-z, 0-9)"
        )));

        $this->validation->add('email', new Validator\Email(array(
            'message' => "El email no es válido"
        )));

        $this->validation->add('password', new Validator\PresenceOf(array(
            'message' => "La contraseña es requerida",
        )));

        $this->validation->add('password', new Validator\Confirmation(array(
            'message' => "La contraseña no coincide",
            'with' => 'confirmPassword'
        )));

        $this->validation->add('firstname', new Validator\Regex(array(
            'pattern' => "/^[a-z ñáéíóú]{4,30}$/i",
            'message' => "Nombre inválido (sólo letras y espacios entre 4 y 30 carácteres)"
        )));

        $this->validation->add('lastname', new Validator\Regex(array(
            'pattern' => "/^[a-z ñáéíóú]{4,30}$/i",
            'message' => "Apellido inválido (sólo letras y espacios entre 4 y 30 carácteres)"
        )));

        $this->validation->add('gender', new Validator\InclusionIn(array(
          'message' => 'Sexo indefinido',
          'domain' => array('male', 'female')
        )));

        $this->validation->add('phone', new Validator\Regex(array(
            'pattern' => "/^[0-9]{10}$/i",
            'message' => "Solo se admiten 10 digitos sin espacios, lada + número"
        )));
        
        foreach ( $this->validation->validate($_POST) as $error ) {
            $this->output['errors'][] = $error->getMessage();
        }

        if ( ! $this->request->hasPost('captcha') || ! $this->validateCaptcha($this->request->getPost('captcha')) )
            $this->output['errors'][] = 'El código de seguridad de la imagen no coindice, por favor vuelve a escribirlo';

        if ( ! $this->isUnique($this->request->getPost('username'), 'username') ) 
            $this->output['errors'][] = 'El nombre de usuario ya existe';

        if ( ! $this->isUnique($this->request->getPost('email'), 'email') ) 
            $this->output['errors'][] = 'Ya hay una cuenta con el email proporcionado';

        return (count($this->output['errors']) == 0);
    }

    private function getForm() 
    {
        $this->output['data'] = $_POST;
        $this->view->setVars($this->output);
    }

    protected function validateCaptcha($key) 
    {
        $securimage = new \Lib\Securimage\Securimage();
        return $securimage->check($key) == true;
    }

    protected function isUnique($value = "", $field = "")
    {
        if ( ! $field )
            return false;

        $user = CbkUserAccount::findFirst(array(
            $field.' = ?0',
            'bind' => array($value),
            'columns' => array($field)
        ));

        return $user ? false : true;
    }

    protected function sendEmailConfirmation(CbkUserAccount $account)
    {
        $confirmation = new CbkUserConfirmation();
        $confirmation->account_id = $account->id;
        $confirmation->confirmed = false;
        $confirmation->created_at = new \Phalcon\Db\RawValue('NOW()');
        $confirmation->modified_at = null;

        do{
            $confirmation->code = md5(uniqid('foobar')); // código irrepetible basado en el tiempo.
        } while ( !$confirmation->isCodeUnique() ); // Casi imposible que tenga que realizar el cálculo de nuevo.

        $confirmation->create();

        $template = new \Phalcon\Mvc\View();
        $template->setViewsDir($this->config->application->viewsDir);
        $template = $template->getRender('email-templates', 'account-confirmation', array('account'=>$account, 'confirmation'=>$confirmation), function($view){
            $view->setRenderLevel(\Phalcon\Mvc\View::LEVEL_LAYOUT);
        });

        $this->mail->send(array(
            'to' => array($account->email => $account->username),
            'subject' => 'Consubook - Confirma tu cuenta!',
            'content' => $template
        ));
    }

    public function confirmEmailAction()
    {
        $this->output['confirmed'] = false;

        if ( $this->dispatcher->getParam('code') ) {

            $confirmation = CbkUserConfirmation::findFirst(array(
                'code = ?0', 
                'bind' => array($this->dispatcher->getParam('code'))
            ));

            if ( $confirmation->confirmed ) {
                $this->response->redirect('');
            }

            if ( $confirmation ) {
                $this->output['confirmed'] = $confirmation->confirmed = true;
                $confirmation->modified_at = new \Phalcon\Db\RawValue('NOW()');
                $confirmation->save();
            }
        }

        $this->view->setVars($this->output);
    }
}

