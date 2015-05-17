/***********
// ANGULAR
***********/

var ROOT = '/consubook/';

var ngConfig = {

};

var ngDirectives = {

	ngUnique: function($http, $q) {
	    return {
	        require: 'ngModel',
	        link: function($scope, elm, attrs, ctrl) {

	            ctrl.$asyncValidators.exists = function(modelValue, viewValue){

	   				var field = attrs.ngUnique;
	   				var routes = {
	   					'username': ROOT+'singup/checkUsername',
	   					'email': ROOT+'singup/checkEmail'
	   				};
	   				var defer = $q.defer();

	   				if ( !routes[field] ) {
	   					alert('Este campo no puede ser comprobado, no está en la lista de chequeos!\n');
	   					defer.reject();
	   					return defer.promise;
	   				}

	            	return $http.get(routes[field]+'&'+field+'='+modelValue).then(function(response){
	            		
	            		if ( response.data.exists ) {
	            			defer.reject();
	            		} else {
	            			defer.resolve();
	            		}
	            		return defer.promise;
	            	});
	            };
	        }
	    };
	},

	ngCompare: function($http, $q, $parse, $log) {

		return {
			require: 'ngModel',
			scope: {
				compareValue: '=ngCompare',
				compareName: '@ngCompare'
			},
			link: function (scope, elm, attrs, modelCtrl) {

				modelCtrl.$validators.compare = function(modelValue){
					return modelValue == scope.compareValue;
				};
				scope.$watch('compareValue', function() {
					if ( modelCtrl.$dirty )
						modelCtrl.$validate();
				});
			}
		};
	}

};


var ngControllers = {

	singup: ['$scope', '$http', function($scope, $http) {

		$('#singup_form').submit(function(ev){ev.preventDefault()});

		$scope.config = {
			view: {
				captchaSrc: '/consubook/singup/showCaptcha/'
			}
		};

		$scope.data = {};

		$scope.submit = function() 
		{
			//singup_form.submit();return;
			if ( ! $scope.singup_form.$valid ) 
			{
				angular.forEach(singup_form.elements, function(element) {
					if ( element.name )
						$scope.singup_form[element.name].$setDirty();
				});
				alert('Por favor, revisa los campos.');
			} 
			else 
				singup_form.submit();
		};

		$scope.getCaptcha = function()
		{
			$scope.config.view.captchaSrc = '/consubook/singup/showCaptcha/'+Math.random();
		};

		$scope.checkPreviousData = function()
		{
			var data = $scope.data;

			if ( ! $.isEmptyObject(data) ) {

				data.password = "";
				data.confirmPassword = "";
				data.captcha = "";
				
				angular.element(document).ready(function() {
					angular.forEach(singup_form.elements, function(element) {
						if ( element.name )
							$scope.singup_form[element.name].$setDirty();
					});
					$scope.$digest();
				});
			}
		};

	}],

	login: ['$scope', '$http', function($scope, $http){

		$('#loginForm').submit(function(ev){ev.preventDefault()});

		$scope.user = {
			username_email: '',
			password: ''
		};
		$scope.errors = [];

		$scope.submit = function() {

			var username = '';
			var email = '';

			if ( $scope.user.username_email.match(/^[a-z0-9!#$%&'*+\/=?^_`{|}~.-]+@[a-z0-9]([a-z0-9-]*[a-z0-9])?(\.[a-z0-9]([a-z0-9-]*[a-z0-9])?)*$/i) )  // sacado de https://github.com/angular/angular.js/blob/master/src/ng/directive/input.js#L788
				email = $scope.user.username_email;
			else
				username = $scope.user.username_email;

			$http.post(ROOT+'user/login/', {username: username, email: email, password: $scope.user.password, remember: $scope.user.remember}).success(function(response){

				if ( response.valid ){
					location.reload();
				} else if ( response.errors ) {
					$scope.errors = response.errors;
				} else
					$scope.errors = ['Error interno, prueba más tarde.'];

			}).error(function(){
				$scope.errors = ['Error interno, prueba más tarde.'];
			});

		};

	}]
};

var ngConsubook = angular.module('consubook', ['ngMessages', 'ngCookies']);

ngConsubook
	.directive('ngUnique', ngDirectives.ngUnique)
	.directive('ngCompare', ngDirectives.ngCompare);

ngConsubook
	.controller('singup', ngControllers.singup)
	.controller('login', ngControllers.login);

/***********
// JQUERY
***********/

// Comportamiento para mostrar el dropdown de filtro de búsqueda al hacer focus en el input de búsqueda
$(function(){

	var $dropdown = $('#dropdown-search-by .dropdown-toggle');
	var $dropdown_menu = $dropdown.next('.dropdown-menu');
	var scope  = $('#consubook-btn-search')[0];

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

// Crea las instancias para los popover y soluciona el problema de posicionamiento al cambiar el tamaño de ventana
$(function(){
	$("[data-toggle=popover]").each(function(i, obj){
		var $this = $(this);
		var $popover = $($this.attr('data-target'));
		var popoverwidth = $popover.outerWidth();
		var popoverheight = $popover.outerHeight();
		var options = {};

		if ( $popover.length ) {
			options.html = true;
			options.content = function(){
				$popover.show(0); // Se muestra antes de insertarse en el popover de lo contrario se visualizaría si estaba oculto.
				return $popover;
			};
		}
		options.placement = function () { // Decide en qué posición del elemento originario se visualizará mejor el popover
	    	var position = $this.position();
			var freespace = {
				left: position.left,
				top: position.top,
				right: $(window).width() - ( position.left + $this.outerWidth() ),
				bottom: $(window).height() - ( position.top + $this.outerHeight() )
			};
			var placement = '';
	        if (freespace.left >= popoverwidth)
	            placement = "left";
	        else if (freespace.right >= popoverwidth)
	            placement = "right";
	        else if (freespace.top >= popoverheight)
	            placement = "top";
	        else placement = "bottom";

	        return placement;
		};

		$this.popover(options);
	});

	$(window).on('resize', function(){
		$("[data-toggle=popover]").each(function(i, obj){
			var $obj = $(obj);
			// Ajusta la posición del popover visible cuando la ventana se redimensiona.
			if ( $obj.attr('aria-describedby') ) // Cuando un popover estaba visible al elemento que originó el popover se le agrega este atributo. Así se evita mostrar el popover cuando no estaba visible fallando esta condición.
				$(obj).popover('show'); // Siempre que se llama a este método bootstrap recalcula la posición del objeto
		});
	});
});


function setCookie(name, value, expireDays) 
{
	//var dayname = ['Sun', 'Mon', 'Tue' , 'Wed', 'Thu', 'Fri', 'Sat'];
	//var monthname = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

	if ( ! expireDays || isNaN(expireDays) )
		expireDays = 0;

	var date = new Date();
	date.setDate(date.getDate() + expireDays);

	document.cookie = name+'='+value+';expires='+date.toGMTString();
}

function getCookie(name) 
{
	var cookie = document.cookie.match(new RegExp(name+'=(.*?);'));
	return cookie ? cookie[1] : '';
}

function removeCookie(name)
{
	var date = new Date();
	date.setDate(date.getDate()-1);

	document.cookie = name+'=;expires='+date.toGMTString();
}