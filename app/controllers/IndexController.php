<?php

class IndexController extends ControllerBase
{

    public function indexAction()
    {
    	$subcategories = $this->loadSubcategories();

    	$this->view->setVars(array(
			'subcategories' => $subcategories
		));
    }

}

