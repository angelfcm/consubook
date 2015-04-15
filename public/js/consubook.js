
var ngConsubook = angular.module('consubook', []);

ngConsubook
	.controller('test', ['$scope', function($scope) {



}]);





// Comportamiento para mostrar el dropdown de filtro de búsqueda al hacer focus en el input de búsqueda
$(function(){

	var $dropdown = $('#dropdown-search-by .dropdown-toggle');
	var $dropdown_menu = $dropdown.next('.dropdown-menu');
	var close = true;
	var scope  = $('#consubook-btn-search')[0];

 	$('#consubook-btn-search').on('focus', function(){

 		//if ( $dropdown.attr('aria-expanded') == 'false' ) {
 			$dropdown.parent().off('hide.bs.dropdown').on('hide.bs.dropdown', function() {  return false; });
 			$dropdown.dropdown('toggle');
 			this.focus();
 		//}
 	}).on('blur', function(){

 		//if ( $dropdown.attr('aria-expanded') == 'true' ) {
 		$dropdown_menu.one('click', function(){  
			scope.focus();
 		});
 		//}	
 		$dropdown.parent().off("hide.bs.dropdown");
 	});

});