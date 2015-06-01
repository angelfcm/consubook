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
            $orderDirection = $this->config->search->defaultOrderDirection;

        $oneWord = preg_match('/^[^ ]*$/', $query);

        $columns = 'CbkBooksImages.id as hasImage, CbkBooksImages.extension as imageExtension, CbkBooks.id, CbkBooks.title, CbkBooks.author, CbkBooks.editorial, CbkBooks.year, CbkBooks.edition, CbkBooks.id_category, CbkBooks.id_book_image, CbkBooks.created_at, CbkBooks.modified_at';
        $conditions = '';
        $categories_conditions = '';
        $bindData = array($query);

        // Si la consulta de búsqueda es una sola palabra o la longitud es menor a 4 carácteres, se usará like como comodín de búsqueda
        if ( $oneWord || strlen($query) < 4 ) {

            if ( $orderBy == 'relevance' ) // En consultas LIKE no es posible hacer ordenamiento por relevancia
                $orderBy = $this->config->search->defaultOrderBy == 'relevance' ? 'title' : $this->config->search->defaultOrderBy;

            $conditions = 'CbkBooks.'.$filter.' LIKE ?0';
            $categories_conditions  = 'books.'.$filter.' LIKE "%'.addslashes($query).'%"';
            $bindData[0] = '%'.$bindData[0].'%';

            $totalResults = CbkBooks::count(array(
                $filter.' LIKE ?0',
                'bind' => array( '%'.$query.'%' )
            ));

        } else {

            if ( $orderBy == 'relevance' ) {
                $columns .= ', (MATCH('.$filter.') AGAINST("'.addslashes($query).'")) as relevance';
            }

            $conditions = 'MATCH(CbkBooks.'.$filter.') AGAINST(?0)';
            $categories_conditions  = 'MATCH(books.'.$filter.') AGAINST("'.addslashes($query).'")';

            $totalResults = CbkBooks::count(array(
                'MATCH('.$filter.') AGAINST(?0)',
                'bind' => array($query)
            ));
        }

        // Se utiliza esta forma de consultar en la base de datos ya que al usar find() devuelve las tablas relacionadas y queremos optimizar este proceso en la busqueda rapida de libros.
        $books = CbkBooks::query()
            ->where($conditions)
            ->bind($bindData)
            ->limit($this->config->search->booksPerPage, ($page > 0 ? ($page-1)*$this->config->search->booksPerPage : 0))
            ->order($orderBy.' '.$orderDirection)
            ->columns($columns)
            ->leftJoin('CbkBooksImages', 'CbkBooks.id_book_image = CbkBooksImages.id')
            ->execute();

        $sql = 'SELECT id FROM cbk_books_categories WHERE parent_id IS NULL';
        $sqlresult = $this->pdo->query($sql)->fetchAll();

        foreach( $sqlresult as $row ){
            $x = $this->countBooksBySubcategories($row['id'], $categories_conditions);
            echo "{$row['id']}<pre>";print_r($x);echo "</pre>";
        }exit;

        $books_categories = $this->countBooksBySubcategories(null, $categories_conditions);
        $books_categories = json_decode(json_encode($books_categories));
        //echo "<pre>";print_r($books_categories); exit;

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
        $this->output['books_categories'] = $books_categories;
        $this->view->setVars($this->output);
    }

    /** 
      * Encuentra la cantidad de libros que hay por categorías jerárquicamente así como la información de las categorías,
      * si una categoría tiene subcategorías, devolverá la suma de todos los libros encontrados en esa categoría 
      * más los de sus subcategorías.
      * Devuelve un array en la forma
      * [
      *  'root': [ ['info' => [datos de la categoría], 'num_books': int, children': recursión de 'root' ], ... ]
      *  'num_books': int
      * ]
      */
    private function countBooksBySubcategories($id, $conditions = "")
    {
        // Si no hay un $id de categoría se comenzará desde las categorías principales
        $columns = 'cats.*, count(*) as num_books';
        $sql_children = 'SELECT '.$columns.' FROM cbk_books_categories cats LEFT JOIN cbk_books books ON cats.id = books.id_category WHERE '.$conditions.' GROUP BY cats.id';
        if ( ! $sql_children_result = $this->pdo->query($sql_children) || $sql_parent_result = $this->pdo->query($sql_parent) )
            throw new Exception($this->pdo->errorInfo()[2]);

        $subcategories = $sql_children_result->fetchAll();

        if ( count($subcategories) == 0 )
            return null;

        foreach( $subcategories as $subcategory ) {

            if ( )
            $subcategory['parent_id'] == NULL;
        }
/*
        if ( $id == null ) {
            $parent_condition = ' AND cats.parent_id IS NULL';
        } else {
            $parent_condition = ' AND cats.parent_id = ' . $id;
        }

        $columns = 'cats.id, cats.name, count(*) as num_books';
        $sql_children = 'SELECT '.$columns.' FROM cbk_books_categories cats LEFT JOIN cbk_books books ON cats.id = books.id_category WHERE '.$conditions.$parent_condition.' GROUP BY cats.id';
        $sql_parent = 'SELECT * FROM cbk_books_categories WHERE id = "'.$id.'"';

        if ( ! $sql_children_result = $this->pdo->query($sql_children) || $sql_parent_result = $this->pdo->query($sql_parent) )
            throw new Exception($this->pdo->errorInfo()[2]);

        $subcategories = $sql_children_result->fetchAll();
        $parent_category = $sql_parent_result->fetch();

        if ( count($subcategories) == 0 )
            return null;

        $total_num_books = 0;
        $result = array();
        
        $i = 0;
        foreach( $subcategories as $subcategory ) {

            $result['root'][$i]['info'] = $subcategory;

            if ( $children = $this->countBooksBySubcategories($subcategory['id'], $conditions) ) {
                $result['root'][$i]['children'] = $children['root'];
                $result['root'][$i]['num_books'] = $subcategory['num_books'] + $children['num_books'];
                $total_num_books += $subcategory['num_books'] + $children['num_books'];
            } else {
                $result['root'][$i]['children'] = null;
                $result['root'][$i]['num_books'] = $subcategory['num_books'];
                $total_num_books += $subcategory['num_books'];
            }
            $i++;
        }

        $result['num_books'] = $total_num_books;

        return $result;   */     
    }

    public function showBookAction() 
    {
        $author = $this->dispatcher->getParam('author');
        $title = $this->dispatcher->getParam('title');

        if ( ! $title ) {
            
            $this->dispatcher->forward(array(
                'controller' => 'Error',
                'action' => 'notFound'
            ));
            return false;
        }

        $author = addslashes(str_replace('-', ' ', $author));
        $title = addslashes(str_replace('-', ' ', $title));

        $book = CbkBooks::findFirst(array(
            "title = ?0 AND author = ?1",
            'bind' => array($title, $author)
        ));

        if ( ! $book ) {
            $this->dispatcher->forward(array(
                'controller' => 'Error',
                'action' => 'notFound'
            ));
            //$this->response->redirect('/error/notfound');
            return false;
        }

        $this->output['book'] = $book;
        $this->view->setVars($this->output);
    }

    public function showBookCoverAction()
    {
        $author = $this->dispatcher->getParam('author');
        $title_and_extension = $this->dispatcher->getParam('title_and_extension');

        if ( ! $author && ! $title_and_extension ) {
            $this->dispatcher->forward(array(
                'controller' => 'Error',
                'action' => 'notFound'
            ));
            return false;
        }

        $author = addslashes(str_replace('-', ' ', $author));
        $title = addslashes(preg_replace(array('/\-/', '/\..+?$/'), array(' ', ''), $title_and_extension));

        $book = CbkBooks::findFirst(array(
            'title = ?0 AND author = ?1',
            'bind' => array($title, $author)
        ));
        

        if ( !$book || !$book->CbkBooksImages ) {
            $this->dispatcher->forward(array(
                'controller' => 'Error',
                'action' => 'notFound'
            ));
            return false;
        }

        $bookImage = $book->CbkBooksImages;

        $mime = 'image/'.($bookImage->extension == 'jpg' ? 'jpeg' : $bookImage->extension);
        $name = $title_and_extension;
        $mtime = strtotime($bookImage->modified_at?$bookImage->modified_at: $bookImage->created_at);
        $size = $bookImage->size;

        //If they sent a If-Modified-Since header, compare it to the file's last modified time. 
        if (!isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) || $mtime > strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE'])){ 
            //Our modified time is newer, send our copy so they update the cache. 
            //[snip error handling] 

            header('Content-type: '.$mime); 
            header('Content-length: '.$size); 
          
            //This copy is valid for 5 days. 
            header('Expires: '.gmdate('D, d M Y H:i:s \G\M\T', time()+(24*60*60*5))); 
             
            //This copy was last modified today. 
            header('Last-modified: '.gmdate('D, d M Y H:i:s \G\M\T')); 

            header('Content-Disposition: inline; filename="'.$name.'"');
            
            
            echo base64_decode($bookImage->image);
        } 
        else { 
            //Their copy is still good.  Tell them that with a 304, then exit. 
            header('HTTP/1.1 304 Not Modified'); 
        } 
        /*
        $file = new \Lib\File();

        if ( ! $file->printFile('books/'.$image) ) {
            $this->dispatcher->forward(array(
                'controller' => 'Error',
                'action' => 'notFound'
            ));
            return false;
        }*/
        $this->view->disable();
    }

}

