<?php

class CbkBooksCopies extends \Phalcon\Mvc\Model
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
    public $id_book;

    /**
     *
     * @var string
     */
    public $isbn;

    /**
     *
     * @var string
     */
    public $inventary_code;

    /**
     *
     * @var integer
     */
    public $available;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->belongsTo('id_book', 'CbkBooks', 'id');
    }

    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'id' => 'id', 
            'id_book' => 'id_book', 
            'isbn' => 'isbn', 
            'inventary_code' => 'inventary_code', 
            'available' => 'available'
        );
    }

}
