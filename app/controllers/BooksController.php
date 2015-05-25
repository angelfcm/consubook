<?php

class BooksController extends ControllerBase
{
	public $output = array(
		'errors' => array(),
		'data' => array()
	);

	public function indexAction()
	{
		$this->dispatcher->forward(array(
			'controller' => 'books',
			'action' => 'search'
		));
	}

    public function searchAction()
    {
        $orderBy = $this->request->hasQuery('order') ? $this->request->getQuery('order') : $this->config->search->defaultOrderBy;
        $orderDirection = $this->request->hasQuery('dir') ? $this->request->getQuery('dir') : $this->config->search->defaultOrderDirection;
    	$filter = $this->dispatcher->getParam('filter');
    	$query = $this->dispatcher->getParam('query');
    	$page = $this->dispatcher->getParam('page');

    	if ( !$filter  || !$query || ! in_array($filter, $this->config->search->filtersSearch->toArray()) ) {
    		$this->response->redirect('/');
    		return false;
    	}

        if ( ! in_array(strtolower($orderBy), $this->config->search->filtersOrder->toArray()) )
            $orderBy = $this->config->search->defaultOrderBy;
        if ( ! in_array(strtolower($orderDirection), array('asc', 'desc')) )
            $orderBy = $this->config->search->defaultOrderDirection;

    	$oneWord = preg_match('/^[^ ]*$/', $query);

    	// Si la consulta de búsqueda es una sola palabra o la longitud es menor a 4 carácteres, se usará like como comodín de búsqueda
    	if ( $oneWord || strlen($query) < 4 ) {
            if ( $orderBy == 'relevance' ) // En consultas LIKE no es posible hacer ordenamiento por relevancia
                $orderBy = $this->config->search->defaultOrderBy == 'relevance' ? 'title' : $this->config->search->defaultOrderBy;

    		$books = CbkBooks::find(array(
    			$filter.' LIKE ?0',
    			'bind' => array( '%'.$query.'%' ),
    			'limit' => $this->config->search->booksPerPage,
    			'offset' => ($page > 0 ? ($page-1)*$this->config->search->booksPerPage : 0),
                'order' => $orderBy.' '.$orderDirection
    		));

    		$totalResults = CbkBooks::count(array(
    			$filter.' LIKE ?0',
    			'bind' => array( '%'.$query.'%' ),
    		));
    	}
    	else { // Para búsquedas de más de una palabra se usará índices fulltext

            $columns = 'title, author, editorial, year, isbn, code, genre, image, copies, availables, created_at, modified_at';
            if ( $orderBy == 'relevance' ) {
                $columns .= ', (MATCH('.$filter.') AGAINST("'.addslashes($query).'")) as relevance';
            }

    	   	$books = CbkBooks::find(array(
    			'MATCH('.$filter.') AGAINST(?0)',
    			'bind' => array($query),
    			'limit' => $this->config->search->booksPerPage,
    			'offset' => ($page > 0 ? ($page-1)*$this->config->search->booksPerPage : 0),
                'order' => $orderBy.' '.$orderDirection,
                'columns' => $columns
    		));

    		$totalResults = CbkBooks::count(array(
				'MATCH('.$filter.') AGAINST(?0)',
    			'bind' => array($query),
    		));
    	}

    	$this->output['books'] = $books;
    	$this->output['totalResults'] = $totalResults;
    	$this->output['currentPage'] = $page > 0 ? $page : 1;
    	$this->output['totalPages'] =  ceil($totalResults / $this->config->search->booksPerPage);
    	$this->output['query'] = $query;
        $this->output['query_link'] = str_replace(' ', '+', $query);
    	$this->output['filter'] = $filter;
        $this->output['orderBy'] = $orderBy;
        $this->output['orderDirection'] = $orderDirection;
        $this->output['oneWordQuery'] = $oneWord;
    	$this->view->setVars($this->output);
    }

    public function showBookAction() 
    {
    	$title = $this->dispatcher->getParam('title');

        if ( ! $title ) {
            
            $this->dispatcher->forward(array(
                'controller' => 'Error',
                'action' => 'notFound'
            ));
            return false;
        }

        $title = addslashes(str_replace('-', ' ', $title));
        $id = null;

        // Se da posibilidad de poner el id del libro en lugar del título
        if ( preg_match('/^[0-9]+$/', $title) ) {
            $id = $title;
        }

        $conditions = '';

        if ($id)
            $conditions = "id = '{$id}'";
        else 
            $conditions = "title = '{$title}'";

        $book = CbkBooks::findFirst($conditions);

        if ( ! $book ) {

            $this->dispatcher->forward(array(
                'controller' => 'Error',
                'action' => 'notFound'
            ));
            return false;
        }

        $this->output['book'] = $book;
        $this->view->setVars($this->output);
    }

}

