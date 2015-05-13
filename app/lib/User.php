<?php

namespace Lib;

class User extends \Phalcon\Di\Injectable {

	protected $username = '',
			  $is_authenticated = false,
			  $role = '',
			  $session = array(),
			  $data = array();

	const ROLE_GUEST = 'Guest';
	const ROLE_ACCOUNT = 'Account';
	const ROLE_DEFAULT = 'Guest';

	public function __construct(\Phalcon\Session\AdapterInterface $session) 
	{
		$this->session = $session;

		$this->authenticate();
	}

	public function authenticate( $username = "" , $email = "", $password = "" )
	{
		$auth_params = ($username != "" || $email != "") && $password != "";

		if ( $this->session->has('user') && !$auth_params ) {

			$session_user = $this->session->get('user');

			$this->setRole($session_user['role']);
			$this->username = $session_user['username'];
			$this->is_authenticated = $session_user['is_authenticated'];

			return true;

		} else if ( $auth_params ) {

			$this->unauthenticate();

			$account = \CbkUserAccount::findFirst(
				array(
					'username = ?0 OR email = ?1',
					'bind' => array($username, $email)
				)
			);

			if ( $account ) {

				if ( $this->security->checkHash($password, $account->password) ) {

					$this->setRole($account->CbkUserRole ? $account->CbkUserRole->name : self::ROLE_GUEST);
					$this->username = $account->username;
					$this->is_authenticated = true;

					$this->session->set('user', array(
						'role' => $this->getRole(),
						'username' => $this->getUsername(),
						'is_authenticated' => $this->isAuthenticated()
					));
				}
				return true;
			}
		}
		 
		$this->unauthenticate();

		return false;
	}

	public function unauthenticate()
	{
		$this->setRole(self::ROLE_DEFAULT);
		$this->username = "";
		$this->is_authenticated = false;

		$this->session->remove('user');	
	}

	public function isAuthenticated()
	{
		return $this->is_authenticated;
	}

	public function getUsername()
	{
		return $this->username;
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