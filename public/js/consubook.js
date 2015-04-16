
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
	var focused = false;
	var blured = false;

 	$dropdown_menu.find('.btn').on('click', function(){ 

 		$dropdown.parent().one('hide.bs.dropdown', function() { return false; });
 		scope.focus();
 	});

 	$('#consubook-btn-search').on('focus', function(){
		
 		$dropdown.dropdown('toggle');
 		$dropdown.parent().one('hide.bs.dropdown', function() {  return false; });

 		this.focus();
 	});

}); 