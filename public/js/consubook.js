
var ngConsubook = angular.module('consubook', []);

ngConsubook
	.controller('test', ['$scope', function($scope) {



}]);





// Comportamiento para mostrar el dropdown de filtro de búsqueda al hacer focus en el input de búsqueda
$(function(){

	var $dropdown = $('#dropdown-search-by');
	var $dropdown_menu = $dropdown.find('.dropdown-menu');
	// Error inesperado cuando se utiliza dropdown('toggle') el comportamiento de los botones deja de funcioanr, no es posible seleccionar despues de esto
/*
 	$('#consubook-btn-search').on('focus', function(){

 		
 		if ( $dropdown_menu.css('display') == 'none' ) {
 			$dropdown.on("hide.bs.dropdown", function() { return false; });
 			$dropdown_menu.dropdown('toggle');
 		}
 	}).on('blur', function(){
 		
 		if ( $dropdown_menu.css('display') == 'block' ) {
 			$dropdown.off("hide.bs.dropdown");
 			$dropdown_menu.dropdown('toggle');
 		}
 	});
*/
});