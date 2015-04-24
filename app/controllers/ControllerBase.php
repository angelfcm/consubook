<?php

use Phalcon\Mvc\Controller;

class ControllerBase extends Controller
{

	protected function initialize()
    {	
		$this->assets->addCss('bootstrap/dist/css/bootstrap-cerulean.min.css');
		$this->assets->addCss('css/consubook.css');
		$this->assets->addCss('bootstrap/dist/css/bootstrap-select.min.css');
	    

	    $this->assets->addJs('jquery/dist/js/jquery.min.js');
		$this->assets->addJs('bootstrap/dist/js/bootstrap.min.js');
		$this->assets->addJs('angular/dist/js/angular.min.js');
		$this->assets->addJs('js/consubook.js');
		$this->assets->addJs('bootstrap/dist/js/bootstrap-select.min.js');




	}

}
