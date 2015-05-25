<?php

namespace Lib;

use Phalcon\Validation\Validator;

class User extends \Phalcon\Di\Injectable {

	protected $is_authenticated = false,
			  $role = '',
			  $session = null,
			  $data = null;

	const ROLE_GUEST = 'Guest';
	const ROLE_ACCOUNT = 'Account';
	const ROLE_DEFAULT = 'Guest';

	public function __construct(\Phalcon\Session\AdapterInterface $session) 
	{
		$this->session = $session;
		$this->setRole(self::ROLE_DEFAULT);

		$username = "";
		$email = "";
		$password = "";

		if ( ( $this->cookies->has('username') || $this->cookies->has('email') ) && $this->cookies->has('password') ) {

			$username = $this->cookies->get('username');
			$username = $username->getValue();
			$email = $this->cookies->get('email');
			$email = $email->getValue();
			$password = $this->cookies->get('password');
			$password = $password->getValue();
			if ( strlen($password) > strlen($this->config->application->crypt_key) ) // se asegura que la contraseña sea de longitud más larga que la llave de encriptación ya que es un requisito para la desencriptación o habrá excepción no controlada
				$password = $this->crypt->decrypt($password);
			else $password = "";
		}

		$this->authenticate($username, $email, $password);
	}

	public function authenticate( $username = "" , $email = "", $password = "" )
	{
		$auth_params = ($username != "" || $email != "") && $password != "";

		if ( !$this->session->has('user') && !$auth_params ) {
			return false;
		}

		if ( $this->session->has('user') ) {

			$account = \CbkUserAccount::findFirst(array(
				'id = ?0',
				'bind' => array($this->session->get('user')['account_id'])
			));
		} else if ( $auth_params ) {

			$account = \CbkUserAccount::findFirst(
				array(
					'username = ?0 OR email = ?1',
					'bind' => array($username, $email)
				)
			);

			if ( $account && ! $this->security->checkHash($password, $account->password) )
				$account = null;
		}

		if ( $account ) {

			$this->setRole($account->CbkUserRole ? $account->CbkUserRole->name : self::ROLE_GUEST);
			$this->is_authenticated = true;

			$this->session->set('user', array(
				'account_id' => $account->id
			));

			$this->data = array(
				'account_id' => $account->id,
				'username' => $account->username,
				'email' => $account->email,
				'firstname' => $account->firstname,
				'lastname' => $account->lastname,
				'gender' => $account->gender,
				'phone' => $account->phone,
				'address' => $account->address,
				'created_at' => $account->created_at,
				'modified_at' => $account->modified_at
			);

			return true;
		}
		$this->unauthenticate();

		return false;
	}

	public function unauthenticate()
	{
		$this->setRole(self::ROLE_DEFAULT);
		$this->is_authenticated = false;

		
		$this->data = null;

		setcookie('username', '', -time(), $this->config->application->baseUri);
		setcookie('email', '', -time(), $this->config->application->baseUri);
		setcookie('password', '', -time(), $this->config->application->baseUri);

		$this->session->destroy();
	}

	public function isAuthenticated()
	{
		return $this->is_authenticated;
	}

	public function getInfo($prop)
	{
		return $this->data[$prop];
	}

	private function setRole($role)
	{
		if ( $role != self::ROLE_ACCOUNT && $role != self::ROLE_GUEST && $role != self::ROLE_DEFAULT )
			return false;
		$this->role = $role;

		return true;
	}

	public function getRole()
	{
		return $this->role;
	}
}