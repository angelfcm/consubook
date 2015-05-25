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
    public $isbn;

    /**
     *
     * @var string
     */
    public $code;

    /**
     *
     * @var string
     */
    public $genre;

    /**
     *
     * @var string
     */
    public $image;

    /**
     *
     * @var integer
     */
    public $copies;

    /**
     *
     * @var integer
     */
    public $availables;

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
            'isbn' => 'isbn', 
            'code' => 'code', 
            'genre' => 'genre', 
            'image' => 'image', 
            'copies' => 'copies', 
            'availables' => 'availables', 
            'created_at' => 'created_at', 
            'modified_at' => 'modified_at'
        );
    }

}
