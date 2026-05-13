<?php if($_SESSION['userType']=="Administrador"): ?>
<div class="container-fluid">
	<div class="page-header">
	  <h1 class="text-titles"><i class="zmdi zmdi-face zmdi-hc-fw"></i> Usuarios <small>(Estudiantes)</small></h1>
	</div>
	<p class="lead">
		En esta sección puede ver el listado de todos los estudiantes registrados en el sistema, puede actualizar datos o eliminar un estudiante cuando lo desee.
	</p>
</div>
<div class="container-fluid">
	<ul class="breadcrumb breadcrumb-tabs">
	  	<li class="active">
	  	<a href="<?php echo SERVERURL; ?>student/" class="btn btn-info">
	  		<i class="zmdi zmdi-plus"></i> Nuevo
	  	</a>
	  	</li>
	  	<li>
	  		<a href="<?php echo SERVERURL; ?>studentlist/" class="btn btn-success">
	  			<i class="zmdi zmdi-format-list-bulleted"></i> Lista
	  		</a>
	  	</li>
	</ul>
</div>
<?php
	require_once "./controllers/studentController.php";
	$insStudent = new studentController();

	if(isset($_POST['studentCode'])){
		echo $insStudent->delete_student_controller($_POST['studentCode']);
	}

	if(isset($_POST['search_init'])){
		$_SESSION['studentSearch']=$_POST['search_init'];
	}

	if(isset($_POST['search_destroy'])){
		unset($_SESSION['studentSearch']);
	}
?>
<div class="container-fluid">
	<?php if(!isset($_SESSION['studentSearch']) || empty($_SESSION['studentSearch'])): ?>
	<form action="" method="POST" enctype="multipart/form-data" autocomplete="off" class="well">
		<div class="row">
			<div class="col-xs-12 col-md-8 col-md-offset-2">
				<div class="form-group label-floating">
				  	<span class="control-label">Buscar estudiante por nombre o apellido</span>
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
		<p class="lead text-center">Su última búsqueda fue <strong>“<?php echo $_SESSION['studentSearch']; ?>”</strong></p>
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
			    	<h3 class="panel-title"><i class="zmdi zmdi-format-list-bulleted"></i> Lista de Estudiantes<?php if(isset($_SESSION['studentSearch']) && !empty($_SESSION['studentSearch'])): ?> — búsqueda: "<?php echo $_SESSION['studentSearch']; ?>"<?php endif; ?></h3>
			  	</div>
			  	<div class="panel-body">
					<div class="table-responsive">
						<?php
							$page=explode("/", $_GET['views']);
							if(isset($_SESSION['studentSearch']) && !empty($_SESSION['studentSearch'])){
								echo $insStudent->pagination_student_search_controller($page[1],10,$_SESSION['studentSearch']);
							}else{
								echo $insStudent->pagination_student_controller($page[1],10);
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