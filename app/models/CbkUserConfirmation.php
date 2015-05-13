<?php

use Phalcon\Mvc\Model\Validator;

class CbkUserConfirmation extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     */
    public $id;

    /**
     *
     * @var integer
     */
    public $account_id;

    /**
     *
     * @var string
     */
    public $code;

    /**
     *
     * @var integer
     */
    public $confirmed;

    /**
     *
     * @var string
     */
    public $created_at;

    /**
     *
     * @var string
     */
    public $modified_at;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->belongsTo('account_id', 'Cbk_user_account', 'id', array('alias' => 'Cbk_user_account'));
    }

    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'id' => 'id', 
            'account_id' => 'account_id', 
            'code' => 'code', 
            'confirmed' => 'confirmed', 
            'created_at' => 'created_at', 
            'modified_at' => 'modified_at'
        );
    }

    public function isCodeUnique()
    {
        $this->validate(new Validator\Uniqueness(array(
            'field' => 'code'
        )));

        return !$this->validationHasFailed();
    }

}
