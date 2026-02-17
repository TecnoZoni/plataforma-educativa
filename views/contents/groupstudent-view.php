<?php if ($_SESSION['userType'] == "Administrador"): ?>
	<div class="container-fluid">
		<div class="page-header">
			<h1 class="text-titles"><i class="zmdi zmdi-accounts-add zmdi-hc-fw"></i> Asignar alumnos al grupo</h1>
		</div>
		<p class="lead">
			En esta sección puede agregar alumnos al grupo seleccionado (máximo 10 alumnos por grupo).
		</p>
	</div>
	<?php
	require_once "./controllers/groupController.php";

	$groupIns = new groupController();

	if (isset($_POST['id'])) {
		echo $groupIns->update_student_group_controller();
	}

	$code = explode("/", $_GET['views']);

	$data = $groupIns->data_group_controller("Only", $code[1]);
	if ($data->rowCount() > 0):
		$rows = $data->fetch();

		// Alumnos actualmente en este grupo
		$current_students_data = $groupIns->get_students_by_group_controller($rows['id']);
		$current_students = $current_students_data->fetchAll();
		$current_count = count($current_students);

		// Alumnos sin grupo asignado
		$available_students_data = $groupIns->get_students_by_dont_group_controller();
		$available_students = $available_students_data->fetchAll();
	?>
		<p class="text-center">
			<a href="<?php echo SERVERURL; ?>grouplist/" class="btn btn-info btn-raised btn-sm">
				<i class="zmdi zmdi-long-arrow-return"></i> Volver a lista de grupos
			</a>
		</p>

		<!-- Panel: Nombre del grupo y alumnos actuales -->
		<div class="container-fluid">
			<div class="row">
				<div class="col-xs-12">
					<div class="panel panel-primary">
						<div class="panel-heading">
							<h3 class=""><i class="zmdi zmdi-group"></i> Grupo: <?php echo htmlspecialchars($rows['Nombre']); ?></h3>
						</div>
						<div class="panel-body">
							<h4>Alumnos asignados (<?php echo $current_count; ?>/10)</h4>
							<?php if ($current_count > 0): ?>
								<table class="table table-hover table-responsive">
									<thead>
										<tr>
											<th>Código</th>
											<th>Nombres</th>
											<th>Apellidos</th>
										</tr>
									</thead>
									<tbody>
										<?php foreach ($current_students as $s): ?>
											<tr>
												<td><?php echo htmlspecialchars($s['Codigo']); ?></td>
												<td><?php echo htmlspecialchars($s['Nombres']); ?></td>
												<td><?php echo htmlspecialchars($s['Apellidos']); ?></td>
											</tr>
										<?php endforeach; ?>
									</tbody>
								</table>
							<?php else: ?>
								<p class="text-center">No hay alumnos asignados a este grupo.</p>
							<?php endif; ?>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- Panel: Formulario para agregar alumnos -->
		<div class="container-fluid">
			<div class="row">
				<div class="col-xs-12">
					<div class="panel panel-success">
						<div class="panel-heading">
							<h3 class="panel-title"><i class="zmdi zmdi-accounts-add"></i> Agregar alumnos</h3>
						</div>
						<div class="panel-body">
							<form action="" method="POST" autocomplete="off">
								<input type="hidden" name="id" value="<?php echo $rows['id']; ?>">

								<fieldset>
									<legend>Alumnos disponibles (sin grupo asignado)</legend>
									<div class="container-fluid">
										<div class="row">
											<div class="col-xs-12">
												<div class="form-group">
													<?php if (count($available_students) > 0 && $current_count < 10): ?>
														<select class="form-control" name="students[]" multiple size="10">
															<?php foreach ($available_students as $student): ?>
																<option value="<?php echo htmlspecialchars($student['Codigo']); ?>">
																	<?php echo htmlspecialchars($student['Nombres'] . ' ' . $student['Apellidos'] . ' (' . $student['Codigo'] . ')  '); ?>
																</option>
															<?php endforeach; ?>
														</select>
														<p class="help-block">
															Mantén presionada la tecla Ctrl (o Cmd en Mac) para seleccionar varios alumnos.<br>
															Puedes agregar hasta <?php echo 10 - $current_count; ?> alumnos más (límite total: 10).
														</p>
													<?php else: ?>
														<p class="text-danger">
															<?php echo $current_count >= 10 ? 'El grupo ya alcanzó el límite de 10 alumnos.' : 'No hay alumnos disponibles sin grupo asignado.'; ?>
														</p>
													<?php endif; ?>
												</div>
											</div>
										</div>
									</div>
								</fieldset>

								<?php if (count($available_students) > 0 && $current_count < 10): ?>
									<p class="text-center">
										<button type="submit" class="btn btn-success btn-raised btn-sm">
											<i class="zmdi zmdi-check"></i> Agregar alumnos seleccionados
										</button>
									</p>
								<?php endif; ?>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	<?php else: ?>
		<p class="lead text-center">Lo sentimos ocurrió un error inesperado</p>
<?php
	endif;
else:
	$logout2 = new loginController();
	echo $logout2->login_session_force_destroy_controller();
endif;
?>