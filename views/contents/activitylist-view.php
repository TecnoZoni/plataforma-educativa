<?php if($_SESSION['userType']=="Administrador"): ?>
<div class="container-fluid">
	<div class="page-header">
	  <h1 class="text-titles"><i class="zmdi zmdi-assignment"></i> Actividades <small>(Listado)</small></h1>
	</div>
	<p class="lead">
		En esta sección puede ver el listado de todas las actividades registradas en el sistema, puede actualizar datos o eliminar una actividad cuando lo desee.
	</p>
</div>

<?php
	require_once "./controllers/activityController.php";

	$insVideo = new activityController();

	if(isset($_POST['search_init'])){
		$_SESSION['activitySearch']=$_POST['search_init'];
	}

	if(isset($_POST['search_destroy'])){
		unset($_SESSION['activitySearch']);
	}
?>
<div class="container-fluid">
	<?php if(!isset($_SESSION['activitySearch']) || empty($_SESSION['activitySearch'])): ?>
	<form action="" method="POST" enctype="multipart/form-data" autocomplete="off" class="well">
		<div class="row">
			<div class="col-xs-12 col-md-8 col-md-offset-2">
				<div class="form-group label-floating">
				  	<span class="control-label">Buscar actividad por título de clase o nombre de alumno</span>
				  	<input class="form-control" type="text" name="search_init" required="">
				</div>
			</div>
			<div class="col-xs-12">
				<p class="text-center">
			    	<button type="submit" class="btn btn-primary btn-raised btn-sm"><i class="zmdi zmdi-search"></i> &nbsp; Buscar</button>
			    </p>
			</div>
		</div>
	</form>
	<?php else: ?>
	<form action="" method="POST" enctype="multipart/form-data" autocomplete="off" class="well">
		<p class="lead text-center">Su última búsqueda fue <strong>“<?php echo $_SESSION['activitySearch']; ?>”</strong></p>
		<div class="row">
			<input class="form-control" type="hidden" name="search_destroy" value="1">
			<div class="col-xs-12">
				<p class="text-center">
			    	<button type="submit" class="btn btn-danger btn-raised btn-sm"><i class="zmdi zmdi-delete"></i> &nbsp; Eliminar búsqueda</button>
			    </p>
			</div>
		</div>
	</form>
	<?php endif; ?>
</div>
<div class="container-fluid">
	<div class="row">
		<div class="col-xs-12">
			<div class="panel panel-success">
			  	<div class="panel-heading">
			    	<h3 class="panel-title"><i class="zmdi zmdi-format-list-bulleted"></i> Lista de actividades<?php if(isset($_SESSION['activitySearch']) && !empty($_SESSION['activitySearch'])): ?> — búsqueda: "<?php echo $_SESSION['activitySearch']; ?>"<?php endif; ?></h3>
			  	</div>
			  	<div class="panel-body">
					<div class="table-responsive">
						<?php
							$page=explode("/", $_GET['views']);
							if(isset($_SESSION['activitySearch']) && !empty($_SESSION['activitySearch'])){
								echo $insVideo->pagination_activity_search_controller($page[1],10,$_SESSION['activitySearch']);
							}else{
								echo $insVideo->pagination_activity_controller($page[1],10);
							}
						?>
					</div>
			  	</div>
			</div>
		</div>
	</div>
</div>
<?php 
	else:
		$logout2 = new loginController();
        echo $logout2->login_session_force_destroy_controller(); 
	endif;
?>
