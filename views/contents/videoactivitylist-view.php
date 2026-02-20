<div class="container-fluid">
	<div class="page-header">
		<h1 class="text-titles"><i class="zmdi zmdi-file zmdi-hc-fw"></i> Actividades Pendientes <small>(Listado)</small></h1>
	</div>
	<p class="lead">
		En esta sección puede ver el listado de todas clases con actividades pendientes en la plataforma de <strong><?php echo COMPANY; ?></strong>. Haga clic en el botón
		<button class="btn btn-info btn-raised btn-xs"> <i class="zmdi zmdi-tv"></i> </button> para acceder a la actividad.
	</p>
</div>
<?php
require_once "./controllers/activityController.php";

$insActivity = new activityController();

$dateNow = date("Y-m-d");
?>
<div class="container-fluid">
	<div class="row">
		<div class="col-xs-12">
			<div class="panel panel-success">
				<div class="panel-heading">
					<h3 class="panel-title"><i class="zmdi zmdi-format-list-bulleted"></i> Lista de clases</h3>
				</div>
				<div class="panel-body">
					<div class="table-responsive">
						<?php
						$page = explode("/", $_GET['views']);
						echo $insActivity->pagination_activity_list_controller($page[1], 10);
						?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>