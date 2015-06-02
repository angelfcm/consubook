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
        $filter = strtolower($this->dispatcher->getParam('filter'));
        $query = $this->dispatcher->getParam('query');
        $page = $this->dispatcher->getParam('page');
        $category_id = $this->request->hasQuery('category') ? $this->request->getQuery('category') : null;
        $category_id = CbkBooksCategories::findFirst($category_id) ? $category_id : null;

        if ( !$filter  || !$query || ! in_array($filter, $this->config->search->filtersSearch->toArray()) ) {
            $this->response->redirect('/');
            return false;
        }

        if ( ! in_array(strtolower($orderBy), $this->config->search->filtersOrder->toArray()) )
            $orderBy = $this->config->search->defaultOrderBy;
        if ( ! in_array(strtolower($orderDirection), array('asc', 'desc')) )
            $orderDirection = $this->config->search->defaultOrderDirection;

        if ( $orderBy == 'category' ) 
            $orderBy = 'id_category';

        if ( $filter == 'category' ) {
            if ( $query == 'all' ) { 
                $category_id = null; // provoca que se busquen libros por todas las categorias
            } else {
                $cat_query = CbkBooksCategories::findFirst(array('name = ?0', 'bind' => array($query)));
                $category_id = $cat_query  ? $cat_query->id : null;
                if ( ! $cat_query ) 
                    $this->response->redirect('/');        
            }
        }

        $oneWord = preg_match('/^[^ ]*$/', $query);

        $columns = 'CbkBooksImages.id as hasImage, CbkBooksImages.extension as imageExtension, CbkBooks.id, CbkBooks.title, CbkBooks.author, CbkBooks.editorial, CbkBooks.year, CbkBooks.edition, CbkBooks.id_category, CbkBooks.id_book_image, CbkBooks.created_at, CbkBooks.modified_at';
        $conditions = '';
        $categories_conditions = '';
        $bindData = array($query);

        /* Lista de categorias para filtracion */
        $categoriesListIds = array($category_id);
        
        if ( $category_id ) {

            $subcategories = $this->loadSubcategories($category_id);
            recursiveForeach(
                $subcategories,
                array('parent_field'=>'info', 'children_field'=>'children'),
                array(
                    'onBeforeChildren' => function($data, $hasChildren) use (&$categoriesListIds) {
                        $categoriesListIds[] = (int)$data->id;
                    },
                    'onAfterChildren' => function($data, $hasChildren) {
                    }
                )
            ); 
        }
        $sqlCategoriesListCondition = '';
        if ( $category_id )
            $sqlCategoriesListCondition  = " AND id_category IN(".implode($categoriesListIds, ',').") ";

        /* Busqueda de libros para LIKE o MATCH dependiendo el tamaño y palabras de la consulta */
        if ( $filter == 'category') 
        {
            if ( $orderBy == 'relevance' ) // En consultas LIKE no es posible hacer ordenamiento por relevancia
                $orderBy = $this->config->search->defaultOrderBy == 'relevance' ? 'category' : $this->config->search->defaultOrderBy;

            if ( $orderBy == 'category' ) 
                $orderBy = 'id_category';

            $sqlCategoriesListCondition  = " id_category IN(".implode($categoriesListIds, ',').") ";

            // Cálculo del total de los resultados con like y filtrado por categorias
            $totalResults = CbkBooks::count(array(
                ($category_id ?  $sqlCategoriesListCondition : '')
            ));
        }
        else
        // Si la consulta de búsqueda es una sola palabra o la longitud es menor a 4 carácteres, se usará like como comodín de búsqueda
        if ( $oneWord || strlen($query) < 4 ) {

            if ( $orderBy == 'relevance' ) // En consultas LIKE no es posible hacer ordenamiento por relevancia
                $orderBy = $this->config->search->defaultOrderBy == 'relevance' ? 'title' : $this->config->search->defaultOrderBy;

            if ( $orderBy == 'category' ) 
                $orderBy = 'id_category';

            $conditions = 'CbkBooks.'.$filter.' LIKE ?0';
            $categories_conditions  = 'books.'.$filter.' LIKE "%'.addslashes($query).'%"';
            $bindData[0] = '%'.$bindData[0].'%';

            // Cálculo del total de los resultados con like y filtrado por categorias
            $totalResults = CbkBooks::count(array(
                $filter.' LIKE ?0'.($category_id ?  $sqlCategoriesListCondition : ''),
                'bind' => array( '%'.$query.'%' )
            ));

        } else {

            if ( $orderBy == 'relevance' ) {
                $columns .= ', (MATCH('.$filter.') AGAINST("'.addslashes($query).'")) as relevance';
            }

            if ( $orderBy == 'category' ) 
                $orderBy = 'id_category';

            $conditions = 'MATCH(CbkBooks.'.$filter.') AGAINST(?0)';
            $categories_conditions  = 'MATCH(books.'.$filter.') AGAINST("'.addslashes($query).'")';

            $totalResults = CbkBooks::count(array(
                'MATCH('.$filter.') AGAINST(?0)'.($category_id ?  $sqlCategoriesListCondition : ''),
                'bind' => array($query)
            ));
        }

        $books_categories = $this->numBooksInCategory($category_id, $categories_conditions);
        $books_categories = json_decode(json_encode($books_categories));

        // Se utiliza esta forma de consultar en la base de datos ya que al usar find() devuelve las tablas relacionadas y queremos optimizar este proceso en la busqueda rapida de libros.
        $books = CbkBooks::query()
            ->where($conditions.($category_id ?  $sqlCategoriesListCondition : ''))
            ->bind($bindData)
            ->limit($this->config->search->booksPerPage, ($page > 0 ? ($page-1)*$this->config->search->booksPerPage : 0))
            ->order($orderBy.' '.$orderDirection)
            ->columns($columns)
            ->leftJoin('CbkBooksImages', 'CbkBooks.id_book_image = CbkBooksImages.id')
            ->execute();

        $this->output['books'] = $books;
        $this->output['totalResults'] = $totalResults;
        $this->output['currentPage'] = $page > 0 ? $page : 1;
        $this->output['totalPages'] =  ceil($totalResults / $this->config->search->booksPerPage);
        $this->output['query'] = $query;
        $this->output['filter'] = $filter;
        $this->output['orderBy'] = $orderBy == 'id_category' ? 'category' : $orderBy;
        $this->output['orderDirection'] = $orderDirection;
        $this->output['oneWordQuery'] = $oneWord;
        $this->output['books_categories'] = $books_categories;
        $this->output['category_id'] = $category_id;
        $this->output['lineageCategory'] = $category_id ? $this->loadLineageSubategory($category_id) : null;

        $this->output['getUrlSearch'] = function($urlData = array()) use($filter, $query, $page, $orderBy, $orderDirection, $category_id) {

            $filter = isset($urlData['filter']) ? $urlData['filter'] : $filter;
            $query = isset($urlData['query']) ? $urlData['query'] : $query;
            $page = isset($urlData['page']) ? $urlData['page'] : ($page > 0 ? $page : 1);
            $orderBy = isset($urlData['orderBy']) ? $urlData['orderBy'] : $orderBy;
            $orderDirection = isset($urlData['orderDirection']) ? $urlData['orderDirection'] : $orderDirection;
            $category_id = isset($urlData['category_id']) ? $urlData['category_id'] : $category_id;

            return $this->url->get(array(
                'for'=>'search', 
                'filter' => $filter, 
                'query' =>  str_replace(' ', '+', $query), 
                'page' => $page
            ))
            ."?order=".($orderBy == 'id_category' ? 'category' : $orderBy)
            ."&dir=".$orderDirection
            .(($category_id && $filter != 'category')? '&category='.$category_id : '');
        };

        $this->view->setVars($this->output);
    }

    private function numBooksInCategory($id, $conditions = "")
    {
        $columns = 'cats.*, count(*) as num_books';

        $sql_cats = 'SELECT '.$columns.' FROM cbk_books_categories cats INNER JOIN cbk_books books ON cats.id = books.id_category '.($conditions ? 'WHERE '.$conditions : '').' GROUP BY cats.id';

        if ( ! $sql_cats_result = $this->pdo->query($sql_cats) )
            throw new Exception($this->pdo->errorInfo()[2]);

        $cats = $sql_cats_result->fetchAll();

        if ( count($cats) == 0 )
            return null;

        $result = array();

        foreach ( $cats as $cat ) {
            
            $parent = null;

            if ( $cat['parent_id'] != $id && $cat['parent_id'] != NULL ) {

                $parent_id = $cat['parent_id'];
                do {
                    
                    $parent = $this->pdo->query('SELECT * FROM cbk_books_categories WHERE id = '.$parent_id)->fetch();
                    $parent_id = $parent ? $parent['parent_id'] : null;
                } 
                while ( $parent && $parent['parent_id'] != $id && $parent['parent_id'] != NULL );
            } else 
                $parent = $cat;

            if ( $parent['parent_id'] == $id || $id == NULL ) {
                if ( ! isset($result[$parent['id']]) )
                    $result[$parent['id']] = array('id' => $parent['id'], 'name' => $parent['name'], 'num_books' => (int)$cat['num_books']);
                else
                    $result[$parent['id']]['num_books'] += (int) $cat['num_books'];
            }
        }
        return $result;
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

