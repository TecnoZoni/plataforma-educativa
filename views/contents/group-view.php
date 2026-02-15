<?php if ($_SESSION['userType'] === "Administrador"): ?>
	<div class="container-fluid">
		<div class="page-header">
			<h1 class="text-titles"><i class="zmdi zmdi-accounts-outline zmdi-hc-fw"></i> Grupos <small>(Estudiantes)</small></h1>
		</div>
		<p class="lead">
			Bienvenido a la sección de grupos. Aquí podrás registrar nuevos grupos. Los campos marcados con <span class="text-danger">*</span> son obligatorios.
		</p>
	</div>

	<div class="container-fluid">
		<ul class="breadcrumb breadcrumb-tabs">
			<li class="active">
				<a href="<?php echo SERVERURL; ?>group/" class="btn btn-info">
					<i class="zmdi zmdi-plus"></i> Nuevo
				</a>
			</li>
			<li>
				<a href="<?php echo SERVERURL; ?>grouplist/" class="btn btn-success">
					<i class="zmdi zmdi-format-list-bulleted"></i> Lista
				</a>
			</li>
		</ul>
	</div>

	<?php
	require_once "./controllers/groupController.php";

	$insGroup = new groupController();

	// Procesar el formulario
	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		echo $insGroup->add_group_controller();
	}

	$categorias = $insGroup->get_categories_controller();
	?>

	<div class="container-fluid">
		<div class="row">
			<div class="col-xs-12">
				<div class="panel panel-info">
					<div class="panel-heading">
						<h3 class="panel-title"><i class="zmdi zmdi-plus"></i> Nuevo Grupo</h3>
					</div>
					<div class="panel-body">
						<form action="" method="POST" autocomplete="off">
							<fieldset>
								<legend><i class="zmdi zmdi-account-box"></i> Datos del grupo</legend>
								<br>
								<div class="container-fluid">
									<div class="row">
										<div class="col-xs-12 col-sm-6">
											<div class="form-group label-floating">
												<label class="control-label">Nombre <span class="text-danger">*</span></label>
												<input
													pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9 ]{1,100}"
													class="form-control"
													type="text"
													name="name"
													value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name'], ENT_QUOTES, 'UTF-8') : ''; ?>"
													required
													maxlength="100">
											</div>
										</div>

										<div class="col-xs-12 col-sm-6">
											<div class="form-group label-floating">
												<label class="control-label">Recompensa <span class="text-danger">*</span></label>
												<input
													pattern="[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ ]{1,100}"
													class="form-control"
													type="text"
													name="reward"
													value="<?php echo isset($_POST['reward']) ? htmlspecialchars($_POST['reward'], ENT_QUOTES, 'UTF-8') : ''; ?>"
													required
													maxlength="100">
											</div>
										</div>

										<div class="col-xs-12 col-sm-6">
											<div class="form-group label-floating">
												<label class="control-label">Seleccione una categoría <span class="text-danger">*</span></label>
												<select name="category" class="form-control" required>
													<?php foreach ($categorias as $categoria): ?>
														<option value="<?php echo $categoria['id']; ?>"
															<?php echo isset($_POST['category']) && $_POST['category'] == $categoria['id'] ? 'selected' : ''; ?>>
															<?php echo htmlspecialchars($categoria['Nombre'], ENT_QUOTES, 'UTF-8'); ?>
														</option>
													<?php endforeach; ?>
												</select>
											</div>
										</div>
									</div>
								</div>
							</fieldset>

							<p class="text-center">
								<button type="submit" class="btn btn-info btn-raised btn-sm">
									<i class="zmdi zmdi-floppy"></i> Guardar
								</button>
							</p>
						</form>
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