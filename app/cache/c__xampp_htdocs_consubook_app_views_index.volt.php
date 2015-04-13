<!DOCTYPE html>
<html>
	<head>
		<meta name="viewport" content="width=device-width, user-scalable=no">
		<title>Consubook</title>
		<!--<link rel="stylesheet" href="bootstrap/dist/css/bootstrap.min.css" />
		<link rel="stylesheet" href="bootstrap/dist/css/bootstrap-theme.min.css" />-->
		<link rel="stylesheet" href="bootstrap/dist/css/bootstrap-cerulean.min.css" />
		<link rel="stylesheet" href="css/consubook.css" />
		<?php $this->assets->outputCss(); ?>	
		<script type="text/javascript" src="jquery/dist/js/jquery.min.js"></script>
		<script type="text/javascript" src="bootstrap/dist/js/bootstrap.min.js"></script>
		<script type="text/javascript" src="angular/dist/js/angular.min.js"></script>
		<script type="text/javascript" src="js/consubook.js"></script>
	</head>
	<body ng-controller="test">
		<nav class="navbar navbar-default">
			<div class="container">
		    	<!-- Brand and toggle get grouped for better mobile display -->
		    	<div class="navbar-header">
	      			<a class="navbar-brand" href="#">Consubook!</a>
   			 	</div>
   			 	<form class="navbar-form navbar-left" role="search">
					<div class="form-group">
					    <div class="input-group">
					    	<span class="input-group-btn">
					    		<button class="btn btn-default dropdown-toggle" data-toggle="dropdown">
					    			<span class="glyphicon glyphicon-chevron-down"></span>
					    		</button>
					    		<ul class="dropdown-menu">
					                <li><a href="#"><input type="radio" ng-model="search-by" /><?php echo 555; ?></a></li>
					                <li><a href="#">Category 2</a></li>
					                <li><a href="#">Category 3</a></li>
					                <li><a href="#">Category 4</a></li>
					                <li><a href="#">Category 5</a></li> 	
					            </ul>
					    	</span>
					    	<input type="text" class="form-control" placeholder="¿Qué libro estás buscando?" id="consubook-btn-search">
					    	<span class="input-group-btn">
					   			<button type="submit" class="btn btn-default">
					   				<span class="glyphicon glyphicon-search"></span>
					   			</button>
							</span>
					    </div>
					</div>
				</form>
				<div class="nav navbar-nav navbar-right">
			 		<a tabindex="0" class="btn navbar-btn btn-info" role="button" data-popover-content="#popover_sign_up" data-toggle="popover">Registrate!</a>
			    	<a tabindex="0" class="btn navbar-btn btn-default" role="button" data-popover-content="#popover_log_in" data-toggle="popover">Ingresar</a>
				</div>
			</div>
		</nav>
[[name]]
		<div class="container" >
		<?php echo $this->getContent(); ?>
		</div>
		<?php $this->assets->outputJs(); ?>
	</body>
</html>