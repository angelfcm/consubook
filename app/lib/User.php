<?php

namespace Lib;

class User {

	protected $username = '',
			  $password = '',
			  $authenticated = false,
			  $role = '',
			  $data = array();

	const ROLE_GUEST = 'Guest';
	const ROLE_ACCOUNT = 'Account';
	const ROLE_DEFAULT = 'Guest';

	public function __construct() {

		$this->setRole(self::ROLE_DEFAULT);
	}

	public function Authenticate()
	{

	}

	public function isLogged()
	{
		return $authenticated;
	}

	public function getUsername()
	{

	}

	public function getPassword()
	{

	}

	protected function setCredential($username, $password)
	{
		$this->username = $username;
		$this->password = $password;
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