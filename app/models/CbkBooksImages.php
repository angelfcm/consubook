<?php

class CbkBooksImages extends \Phalcon\Mvc\Model
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
    public $image;

    /**
     *
     * @var string
     */
    public $extension;

    /**
     *
     * @var integer
     */
    public $size;

    /**
     *
     * @var integer
     */
    public $width;

    /**
     *
     * @var integer
     */
    public $height;

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
        $this->hasMany('id', 'CbkBooks', 'id_book_image');
    }

    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'id' => 'id', 
            'image' => 'image', 
            'extension' => 'extension', 
            'size' => 'size', 
            'width' => 'width', 
            'height' => 'height', 
            'created_at' => 'created_at', 
            'modified_at' => 'modified_at'
        );
    }

}
