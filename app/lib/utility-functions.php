<?php

function stripAccents($string){
	return str_replace(
		array("à", "á", "â", "ã", "ä", "ç", "è", "é", "ê", "ë", "ì", "í", "î", "ï", "ñ", "ò", "ó", "ô", "õ", "ö", "ù", "ú", "û", "ü", "ý", "ÿ", "À", "Á", "Â", "Ã", "Ä", "Ç", "È", "É", "Ê", "Ë", "Ì", "Í", "Î", "Ï", "Ñ", "Ò", "Ó", "Ô", "Õ", "Ö", "Ù", "Ú", "Û", "Ü", "Ý"), 
		array("a", "a", "a", "a", "a", "c", "e", "e", "e", "e", "i", "i", "i", "i", "n", "o", "o", "o", "o", "o", "u", "u", "u", "u", "y", "y", "A", "A", "A", "A", "A", "C", "E", "E", "E", "E", "I", "I", "I", "I", "N", "O", "O", "O", "O", "O", "U", "U", "U", "U", "Y"), 
		$string
	);
}

function prettyUrlQuery($string) {
	return stripAccents(strtolower(str_replace(' ', '-', $string)));
}

/**
 * Recorre todos los elementos anidados en un array de manera recursiva.
 * @PARÁMETROS
 * $data: array( array('padre'=>mixed, 'hijos' => array(...)), ... ) 
 *		  Es un conjunto de elementos dentro de una array donde cada elemento es un array asociativo 
 *        que contiene un elemento de la información del elemento actual o elemento padre y el otro un
 *        conjunto de elementos que repiten este procedimiento recursivamente (elementos hijos)
 * $config: array(
 * 	'parent_field' => string, // Nombre del campo de los datos que almacena información del elemento actual o elemento padre
 *	'children_field' => string // Nombre del campo de los datos que almacena subelementos recursivos del elemento actual o elementro padre
 * )
 * $listeners: array(
 *	'beforeChildren' => function($parent_data: mixed, $hasChildren: bool){},
 *  'afterChildren' => function($parent_data: mixed, $hasChildren: bool){}
 * )
 * Son los listeners que utilizará el usuario para saber que hacer cada vez que se va a imprimir un elemento padre o elementos hijos
 * beforeChildren:  función que se llamará cada vez que un nuevo elemento se vaya a imprimir
 * 		$parent_data: es el contenido de la información del elemento padre
 *      $hasChildren: indica si habrá elementos hijos dentro del padre para su posterior impresión
 * afterChildren: función que se llamará cada vez que un elemento padre junto con sus hijos hayan sido impresos
 */
function recursiveForeach($data, $config, $listeners) {

	/**
	 * Validación de argumentos...
	 */
	$listeners_names = array('onBeforeChildren', 'onAfterChildren');

	if ( ! is_array($listeners) ) {
		throw new Exception('Listeners en formato de arreglo requeridos en el tercer parámetro.');
		return;
	}
	if ( count(array_intersect_key($listeners_names, array_keys($listeners))) != count($listeners_names) ) {
		throw new Exception('No se especificaron los listeners requeridos: <pre>'.print_r($listeners_names, true).'</pre>');
		return;
	}
	foreach( $listeners as $name=>$listener ) {
		if ( ! is_callable($listener) ) {
			throw new Exception('Listener '.$name.' no se especifico como una función.');
			return;
		}
	}
	if ( ! isset($config['parent_field']) || ! isset($config['children_field']) ) {
		throw new Exception('Los campos "parent_field" y "children_field" son requeridos dentro de un arreglo como segundo parámetro.');
		return;
	}

	/**
	 * Fin validación de argumentos...
	 */

	if ( ! $data ) {
		return;
	}

	foreach ( $data as $parent ) {

		$parent_data = $parent[$config['parent_field']];
		$hasChildren = isset($parent[$config['children_field']]) && count($parent[$config['children_field']]) > 0 ? true : false;

		$listeners['onBeforeChildren']($parent_data, $hasChildren);

		recursiveForeach($parent[$config['children_field']], $config, $listeners);

		$listeners['onAfterChildren']($parent_data, $hasChildren);
	}
}