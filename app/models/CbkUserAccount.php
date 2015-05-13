<?php

use Phalcon\Mvc\Model\Validator;

class CbkUserAccount extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     */
    public $id;

    /**
     *
     * @var string
     */
    public $username;

    /**
     *
     * @var string
     */
    public $email;

    /**
     *
     * @var string
     */
    public $password;

    /**
     *
     * @var string
     */
    public $firstname;

    /**
     *
     * @var string
     */
    public $lastname;

    /**
     *
     * @var string
     */
    public $gender;

    /**
     *
     * @var integer
     */
    public $age;

    /**
     *
     * @var string
     */
    public $phone;

    /**
     *
     * @var string
     */
    public $address;

    /**
     *
     * @var integer
     */
    public $user_role_id;

    public $created_at;

    public $modified_at;

    /**
     * Validations and business logic
     */
    public function validation()
    {
        if ($this->validationHasFailed() == true) {
            return false;
        }
    }

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->belongsTo('user_role_id', 'CbkUserRole', 'id');
        $this->hasOne('id', 'CbkUserConfirmation', 'account_id');
    }

    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'id' => 'id', 
            'username' => 'username', 
            'email' => 'email', 
            'password' => 'password', 
            'firstname' => 'firstname', 
            'lastname' => 'lastname', 
            'gender' => 'gender', 
            'age' => 'age', 
            'phone' => 'phone', 
            'address' => 'address', 
            'user_role_id' => 'user_role_id',
            'created_at' => 'created_at',
            'modified_at' => 'modified_at'
        );
    }

}
