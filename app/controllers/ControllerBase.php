<?php

use Phalcon\Mvc\Controller;

class ControllerBase extends Controller
{

	protected function initialize()
    {
    	// Si antes de este controlador ya había otro controlador ya no se agregan los assets ya que se duplicaran
    	// Esto sucede cuando se hace un forward en un controlador para otro controlador
    	if (  ! $this->dispatcher->getLastController() )
			$this->addAssets();

		$categories = CbkBooksCategories::find('parent_id IS NULL');// $this->loadSubcategories();

		$this->view->setVars(array(
			'categories' => $categories
		));
	}

	protected function addAssets()
	{
		$this->assets->addCss('bootstrap/dist/css/bootstrap-cerulean.min.css');
		$this->assets->addCss('css/consubook.css');
		$this->assets->addCss('bootstrap/dist/css/bootstrap-select.min.css');
	    
	    $this->assets->addJs('jquery/dist/js/jquery.min.js');
	    $this->assets->addJs('bootstrap/dist/js/bootstrap.min.js');
		$this->assets->addJs('bootstrap/dist/js/bootstrap-select.min.js');
		$this->assets->addJs('angular/dist/js/angular.min.js');
		$this->assets->addJs('angular/dist/js/angular-messages.min.js');
		$this->assets->addJs('angular/dist/js/angular-cookies.min.js');
		$this->assets->addJs('js/consubook.js');
	}

	public function loadSubcategories($id = null)
	{
		$result = null;

		// Si no hay un $id de categoría se comenzará desde las categorías principales
		if ( $id == null ) {
			$subcategories = CbkBooksCategories::find('parent_id IS NULL');
		} else {
			$subcategories = CbkBooksCategories::find(array(
				'parent_id = ?0',
				'bind' => array($id)
			));
		}

		if ( ! $subcategories )
			return $result;

		foreach( $subcategories as $subcategory ) {

			$result[] = array(
				'info' => $subcategory->toArray(),
				'children' => $this->loadSubcategories($subcategory->id)
			);
		}

		return $result;
	}



}
