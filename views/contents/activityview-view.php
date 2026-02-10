<?php if ($_SESSION['userType'] == "Administrador"): ?>
	<div class="container-fluid">
		<div class="page-header">
			<h1 class="text-titles"><i class="zmdi zmdi-settings zmdi-hc-fw"></i> Datos de la actividad</h1>
		</div>
		<p class="lead">
			Bienvenido a la sección de calificación de la actividad. Acá podrá actualizar la nota personal de los estudiantes registrados en el sistema.
		</p>
	</div>

	<?php
	require_once "./controllers/activityController.php";
	$insActivity = new activityController();

	if (isset($_POST['respuesta_id'])) {
		echo $insActivity->update_note_activity_controller();
	}

	$code = explode("/", $_GET['views']);
	$data = $insActivity->data_activity_controller("All", $code[1]);

	if ($data->rowCount() > 0):
		$rows = $data->fetch();
	?>

		<div class="container-fluid">
			<p class="text-center">
				<a href="<?php echo SERVERURL; ?>activitylist/" class="btn btn-info btn-raised btn-sm">
					<i class="zmdi zmdi-long-arrow-return"></i> Volver a la lista
				</a>
			</p>

			<div class="page-header">
				<h1 class="text-titles"><i class="zmdi zmdi-videocam zmdi-hc-fw"></i> <small><?php echo $rows['Titulo']; ?></small></h1>
			</div>

			<div class="row">
				<div class="col-xs-12">
					<!-- Información general -->
					<ul class="list-unstyled">
						<li class="text-muted"><i class="zmdi zmdi-star-circle"></i> TÍTULO O TEMA:
							<a href="<?php echo SERVERURL; ?>classview/<?php echo $rows['clase_id']; ?>/">
								<strong><?php echo $rows['Titulo']; ?></strong>
							</a>
						</li>
						<li class="text-muted"><i class="zmdi zmdi-face"></i> ALUMNO:
							<a href="<?php echo SERVERURL; ?>studentinfo/<?php echo $rows['Codigo']; ?>/">
								<strong><?php echo $rows['Alumno']; ?></strong>
							</a>
						</li>
					</ul>

					<!-- Respuesta y adjuntos -->
					<div class="panel panel-primary">
						<div class="panel-heading">
							<h3 class="panel-title text-center"><i class="zmdi zmdi-info"></i> Respuesta a la tarea</h3>
						</div>
						<div class="panel-body">
							<?php echo $rows['Respuesta']; ?>

							<?php if ($rows['Adjuntos'] != ""): ?>
								<br><br>
								<h4 class="text-center"><i class="zmdi zmdi-cloud-download"></i> Archivos adjuntos</h4>
								<table class="table table-hover">
									<thead>
										<tr>
											<th>Archivo</th>
											<th class="text-center">Descargar</th>
										</tr>
									</thead>
									<tbody>
										<?php
										$attachment = explode(",", $rows['Adjuntos']);
										foreach ($attachment as $files):
										?>
											<tr>
												<td><?php echo $files; ?></td>
												<td class="text-center">
													<a href="<?php echo SERVERURL; ?>attachments/class/<?php echo $files; ?>" download="<?php echo $files; ?>" class="btn btn-primary btn-raised btn-xs">
														<i class="zmdi zmdi-download"></i>
													</a>
												</td>
											</tr>
										<?php endforeach; ?>
									</tbody>
								</table>
							<?php endif; ?>
						</div>
					</div>

					<!-- Formulario de calificación -->
					<div class="panel panel-success">
						<div class="panel-heading">
							<h3 class="panel-title"><i class="zmdi zmdi-graduation-cap"></i> Calificar actividad</h3>
						</div>
						<div class="panel-body">
							<form action="" method="POST" enctype="multipart/form-data" autocomplete="off">
								<input type="hidden" name="respuesta_id" value="<?php echo $rows['respuesta_id']; ?>">

								<div class="row">
									<div class="col-xs-12 col-sm-4 col-sm-offset-4">
										<div class="form-group label-floating">
											<label class="control-label">Nota actual: <?php echo ($rows['Nota'] != '' ? $rows['Nota'] : '-'); ?></label>
											<input pattern="[0-9]{1,2}" class="form-control" type="number" name="note" value="<?php echo $rows['Nota']; ?>" required maxlength="2" min="0" max="10">
										</div>
									</div>
								</div>

								<p class="text-center">
									<button type="submit" class="btn btn-success btn-raised">
										<i class="zmdi zmdi-refresh"></i> Actualizar nota
									</button>
								</p>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>

	<?php else: ?>
		<div class="container-fluid">
			<p class="lead text-center">Lo sentimos, ocurrió un error inesperado. La respuesta no existe o no está disponible.</p>
		</div>
	<?php endif; ?>

<?php
else:
	$logout2 = new loginController();
	echo $logout2->login_session_force_destroy_controller();
endif;
?>