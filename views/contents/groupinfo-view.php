<?php if ($_SESSION['userType'] == "Administrador"): ?>
	<div class="container-fluid">
		<div class="page-header">
			<h1 class="text-titles"><i class="zmdi zmdi-settings zmdi-hc-fw"></i> Datos del grupo</h1>
		</div>
		<p class="lead">
			Bienvenido a la sección de actualización de los datos del grupo. Acá podrá actualizar la información del grupo registrado en el sistema.
		</p>
	</div>
	<?php
	require_once "./controllers/groupController.php";

	$groupIns = new groupController();

	if (isset($_POST['id'])) {
		echo $groupIns->update_group_controller();
	}

	$code = explode("/", $_GET['views']);

	$data = $groupIns->data_group_controller("Only", $code[1]);
	if ($data->rowCount() > 0):
		$rows = $data->fetch();

		$categorias = $groupIns->get_categories_controller();
	?>
		<p class="text-center">
			<a href="<?php echo SERVERURL; ?>grouplist/" class="btn btn-info btn-raised btn-sm">
				<i class="zmdi zmdi-long-arrow-return"></i> Volver
			</a>
		</p>
		<div class="container-fluid">
			<div class="row">
				<div class="col-xs-12">
					<div class="panel panel-success">
						<div class="panel-heading">
							<h3 class="panel-title"><i class="zmdi zmdi-refresh"></i> Actualizar datos</h3>
						</div>
						<div class="panel-body">
							<form action="" method="POST" enctype="multipart/form-data" autocomplete="off">
								<fieldset>
									<legend><i class="zmdi zmdi-account-box"></i> Datos del grupo</legend><br>
									<input type="hidden" name="id" value="<?php echo $rows['id']; ?>">
									<div class="container-fluid">
										<div class="row">
											<div class="col-xs-12 col-sm-6">
												<div class="form-group label-floating">
													<label class="control-label">Nombre del grupo *</label>
													<input pattern="[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ ]{1,100}" class="form-control" type="text" name="name" value="<?php echo $rows['Nombre']; ?>" required="" maxlength="100">
												</div>
											</div>
											<div class="col-xs-12 col-sm-6">
												<div class="form-group label-floating">
													<label class="control-label">Recompensa *</label>
													<input pattern="[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ ]{1,100}" class="form-control" type="text" name="reward" value="<?php echo $rows['Recompensa']; ?>" required="" maxlength="100">
												</div>
											</div>
											<div class="col-xs-12 col-sm-6">
												<div class="form-group label-floating">
													<label class="control-label">Seleccione una categoría <span class="text-danger">*</span></label>
													<select name="category" class="form-control" required>
														<?php foreach ($categorias as $categoria): ?>
															<option value="<?php echo $categoria['id']; ?>"
																<?php echo $rows['categoria_id'] == $categoria['id'] ? 'selected' : ''; ?>>
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
									<button type="submit" class="btn btn-success btn-raised btn-sm"><i class="zmdi zmdi-refresh"></i> Guardar cambios</button>
								</p>
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