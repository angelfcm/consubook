<?php

class CbkBooks extends \Phalcon\Mvc\Model
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
    public $title;

    /**
     *
     * @var string
     */
    public $author;

    /**
     *
     * @var string
     */
    public $editorial;

    /**
     *
     * @var string
     */
    public $year;

    /**
     *
     * @var string
     */
    public $edition;

    /**
     *
     * @var integer
     */
    public $id_category;

    /**
     *
     * @var integer
     */
    public $id_book_image;

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
        $this->hasMany('id', 'CbkBooksCopies', 'id_book');
        $this->belongsTo('id_category', 'CbkBooksCategories', 'id');
        $this->belongsTo('id_book_image', 'CbkBooksImages', 'id');
    }

    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'id' => 'id', 
            'title' => 'title', 
            'author' => 'author', 
            'editorial' => 'editorial', 
            'year' => 'year', 
            'edition' => 'edition', 
            'id_category' => 'id_category', 
            'id_book_image' => 'id_book_image', 
            'created_at' => 'created_at', 
            'modified_at' => 'modified_at'
        );
    }

}
