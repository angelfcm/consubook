<?php

class CbkBooksCategories extends \Phalcon\Mvc\Model
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
    public $name;

    /**
     *
     * @var string
     */
    public $code;

    /**
     *
     * @var integer
     */
    public $parent_id;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->hasMany('id', 'CbkBooksCategories', 'parent_id');
        $this->belongsTo('parent_id', 'CbkBooksCategories', 'id');
    }

    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'id' => 'id', 
            'name' => 'name', 
            'code' => 'code', 
            'parent_id' => 'parent_id'
        );
    }

}
