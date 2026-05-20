<?php if ($_SESSION['userType'] == "Administrador"): ?>
	<div class="container-fluid">
		<div class="page-header">
			<h1 class="text-titles"><i class="zmdi zmdi-chart"></i> Estadísticas del grupo</h1>
		</div>
		<p class="lead">
			En esta sección puede ver los integrantes del grupo, el promedio individual de cada alumno y dos cálculos del promedio del grupo.
		</p>
	</div>

	<div class="container-fluid">
		<ul class="breadcrumb breadcrumb-tabs">
			<li>
				<a href="<?php echo SERVERURL; ?>grouplist/" class="btn btn-success">
					<i class="zmdi zmdi-format-list-bulleted"></i> Lista de grupos
				</a>
			</li>
		</ul>
	</div>

	<?php
	require_once "./controllers/groupController.php";
	$insGroup = new groupController();

	$page = explode("/", $_GET['views']);
	$id = isset($page[1]) ? $page[1] : null;

	$stats = $insGroup->group_stats_controller($id);

	if (!$stats):
	?>
		<div class="container-fluid">
			<p class="lead text-center">No se encontró el grupo solicitado.</p>
		</div>
	<?php else: ?>

		<div class="container-fluid">
			<div class="row">
				<!-- Panel datos del grupo -->
				<div class="col-xs-12 col-md-6">
					<div class="panel panel-info">
						<div class="panel-heading">
							<h3 class="panel-title"><i class="zmdi zmdi-info"></i> Datos del grupo</h3>
						</div>
						<div class="panel-body">
							<ul class="list-unstyled">
								<li><strong>Nombre:</strong> <?php echo htmlspecialchars($stats['grupo']['Nombre']); ?></li>
								<li><strong>Categoría:</strong> <?php echo htmlspecialchars($stats['grupo']['Categoria'] ?? '—'); ?></li>
								<li><strong>Recompensa:</strong> <?php echo htmlspecialchars($stats['grupo']['Recompensa']); ?></li>
								<li><strong>Total de alumnos:</strong> <?php echo $stats['total_alumnos']; ?></li>
							</ul>
						</div>
					</div>
				</div>

				<!-- Panel promedios del grupo (dos cálculos) -->
				<div class="col-xs-12 col-md-6">
					<div class="panel panel-success">
						<div class="panel-heading">
							<h3 class="panel-title"><i class="zmdi zmdi-chart"></i> Promedios del grupo</h3>
						</div>
						<div class="panel-body">
							<div class="row text-center">
								<div class="col-xs-6" style="border-right: 1px solid #eee;">
									<h1 style="font-size: 3em; color: #4CAF50; margin: 0;">
										<?php echo number_format($stats['promedio_por_alumno'], 2); ?>
									</h1>
									<p class="text-muted" style="margin-top: 8px;">
										<strong>Por alumno</strong><br>
										<small>Media de los promedios individuales</small>
									</p>
								</div>
								<div class="col-xs-6">
									<h1 style="font-size: 3em; color: #2196F3; margin: 0;">
										<?php echo number_format($stats['promedio_global'], 2); ?>
									</h1>
									<p class="text-muted" style="margin-top: 8px;">
										<strong>Global</strong><br>
										<small>Media sobre todas las notas</small>
									</p>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>

			<!-- Tabla de alumnos con promedios -->
			<div class="row">
				<div class="col-xs-12">
					<div class="panel panel-primary">
						<div class="panel-heading">
							<h3 class="panel-title"><i class="zmdi zmdi-accounts"></i> Alumnos integrantes</h3>
						</div>
						<div class="panel-body">
							<div class="table-responsive">
								<table class="table text-center">
									<thead>
										<tr>
											<th class="text-center">#</th>
											<th class="text-center">Nombres</th>
											<th class="text-center">Apellidos</th>
											<th class="text-center">Promedio individual</th>
											<th class="text-center">Ver alumno</th>
										</tr>
									</thead>
									<tbody>
										<?php if (count($stats['alumnos']) === 0): ?>
											<tr>
												<td colspan="5">El grupo no tiene alumnos asignados</td>
											</tr>
										<?php else: $n = 1;
											foreach ($stats['alumnos'] as $a): ?>
												<tr>
													<td><?php echo $n++; ?></td>
													<td><?php echo htmlspecialchars($a['Nombres']); ?></td>
													<td><?php echo htmlspecialchars($a['Apellidos']); ?></td>
													<td>
														<?php echo $a['Promedio'] > 0 ? number_format($a['Promedio'], 2) : '—'; ?>
													</td>
													<td>
														<a href="<?php echo SERVERURL; ?>studentinfo/<?php echo $a['Codigo']; ?>/" class="btn btn-success btn-raised btn-xs">
															<i class="zmdi zmdi-eye"></i>
														</a>
													</td>
												</tr>
										<?php endforeach;
										endif; ?>
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

	<?php endif; ?>

<?php
else:
	$logout2 = new loginController();
	echo $logout2->login_session_force_destroy_controller();
endif;
?>
