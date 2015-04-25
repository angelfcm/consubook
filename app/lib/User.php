<?php

namespace Lib;

class User {

	protected $username = '',
			  $is_authenticated = false,
			  $role = '',
			  $session = array();

	const ROLE_GUEST = 'Guest';
	const ROLE_ACCOUNT = 'Account';
	const ROLE_DEFAULT = 'Guest';

	public function __construct($session) 
	{	
		$this->session = $session;

		$this->authenticate();
	}

	public function authenticate( $username = "" , $email = "", $password = "" )
	{
		if ( !empty($this->session->get('user')) ) {

			$session_user = $this->session->get('user');

			$this->setRole($session_user['role']);
			$this->username = $session_user['username'];
			$this->is_authenticated = $session_user['is_authenticated'];

			return true;
		}
		else if ( ($username != "" || $email != "") && $password != "" ) {

			$account = \CbkUserAccount::findFirst(
				array(
					'username = ?0 OR email = ?0',
					'bind' => array($username, $email)
				)
			);

			if ( $account ) {

				if ( $this->security->checkHash($password, $account->password) ) {

					$this->setRole($account->CbkUserRole->name);
					$this->username = $account->username;
					$this->is_authenticated = true;
				}
				return true;
			}
		} 
		$this->setRole(self::ROLE_DEFAULT);
		return false;
	}

	public function isAuthenticated()
	{
		return $is_authenticated;
	}

	public function getUsername()
	{

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