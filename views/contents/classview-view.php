<?php
require_once "./controllers/videoController.php";
require_once "./controllers/activityController.php";
require_once "./controllers/commentController.php";

$insVideo = new videoController();
$insActivity = new activityController();
$insComment = new commentController();

$dateNow = date("Y-m-d");
$urls = SERVERURL . $_GET['views'];

if (isset($_POST['commentCode'])) {
	echo $insComment->delete_comment_controller($_POST['commentCode'], $_SESSION['userKey'], $urls);
}
if (isset($_POST['response'])) {
	echo $insActivity->add_response_activity_controller();
}

$code = explode("/", $_GET['views']);

$data = $insVideo->data_video_controller("Only", $code[1]);
if ($data->rowCount() > 0):
	$rows = $data->fetch();

	// Obtener respuesta del usuario actual
	$response_data = $insActivity->get_user_response_controller($rows['id'], $_SESSION['userKey']);
	$has_response = $response_data !== false;
	$current_response = $has_response ? ($response_data['Respuesta'] ?? '') : '';
	$current_adjuntos = $has_response ? ($response_data['Adjuntos'] ?? '') : '';
	$current_nota = $has_response ? ($response_data['Nota'] ?? 0) : 0;
?>
	<div class="container-fluid">
		<div class="page-header">
			<h1 class="text-titles text-center">
				<i class="zmdi zmdi-videocam zmdi-hc-fw"></i> <?php echo $rows['Titulo']; ?>
			</h1>
		</div>
	</div>

	<div class="container-fluid">
		<div class="row">
			<div class="col-xs-12 col-md-10 col-md-offset-1">

				<!-- Información básica -->
				<div class="full-box" style="padding: 15px; margin-bottom: 20px; background: #f5f5f5; border-radius: 8px;">
					<h3 class="text-center" style="margin-top: 0;"><i class="zmdi zmdi-info-outline"></i> Información de la clase</h3>
					<p class="text-center"><i class="zmdi zmdi-face"></i> <strong>Tutor:</strong> <?php echo $rows['Tutor']; ?></p>
					<p class="text-center"><i class="zmdi zmdi-calendar"></i> <strong>Fecha:</strong> <?php echo date("d/m/Y", strtotime($rows['Fecha'])); ?></p>
				</div>

				<!-- Video -->
				<div class="full-box text-center videoWrapper" style="margin-bottom: 30px;">
					<?php echo $rows['Video']; ?>
				</div>

				<!-- Descripción y adjuntos de la clase -->
				<div class="full-box thumbnail" style="padding: 20px; margin-bottom: 30px;">
					<h3 class="text-titles text-center"><i class="zmdi zmdi-format-align-left"></i> Descripción de la clase</h3>
					<div style="font-size: 16px; line-height: 1.6;">
						<?php echo $rows['Descripcion']; ?>
					</div>

					<?php if ($rows['Adjuntos'] != ""): ?>
						<hr style="margin: 30px 0;">
						<h4 class="text-titles text-center"><i class="zmdi zmdi-cloud-download"></i> Archivos adjuntos de la clase</h4>
						<table class="table table-striped">
							<thead>
								<tr>
									<th>Archivo</th>
									<th class="text-center">Descargar</th>
								</tr>
							</thead>
							<tbody>
								<?php
								$attachments_class = explode(",", $rows['Adjuntos']);
								foreach ($attachments_class as $file):
									$file = trim($file);
									if ($file !== ""):
								?>
										<tr>
											<td><?php echo $file; ?></td>
											<td class="text-center">
												<a href="<?php echo SERVERURL; ?>attachments/class/<?php echo $file; ?>" download class="btn btn-primary btn-raised">
													<i class="zmdi zmdi-download"></i> Descargar
												</a>
											</td>
										</tr>
								<?php endif;
								endforeach; ?>
							</tbody>
						</table>
					<?php endif; ?>
				</div>

				<!-- Actividad de la clase -->
				<div class="full-box thumbnail mt-5" style="padding: 20px;">
					<h3 class="text-titles text-center"><i class="zmdi zmdi-file-text"></i> Actividad de la clase</h3>
					<div style="font-size: 16px; line-height: 1.6;">
						<?php echo $rows['Actividad']; ?>
					</div>
				</div>

				<!-- Respuesta actual (si existe) -->
				<?php if ($has_response): ?>
					<div class="full-box thumbnail" style="padding: 20px; margin-top: 30px;">
						<h3 class="text-titles text-center"><i class="zmdi zmdi-assignment-check"></i> Tu respuesta enviada</h3>

						<?php if ($current_nota > 0): ?>
							<div class="text-center" style="margin-bottom: 20px;">
								<h4 style="color: #4CAF50;"><strong>Nota: <?php echo $current_nota; ?></strong></h4>
							</div>
						<?php endif; ?>

						<div style="font-size: 16px; line-height: 1.6; min-height: 120px; padding: 15px; background: #f9f9f9; border: 1px solid #ddd; border-radius: 6px;">
							<?php echo $current_response; ?>
						</div>

						<?php if ($current_adjuntos !== ""): ?>
							<hr style="margin: 30px 0;">
							<h4 class="text-titles text-center"><i class="zmdi zmdi-attachment-alt"></i> Tus archivos adjuntos</h4>
							<table class="table table-striped">
								<thead>
									<tr>
										<th>Archivo</th>
										<th class="text-center">Descargar</th>
									</tr>
								</thead>
								<tbody>
									<?php
									$attachments_activity = explode(",", $current_adjuntos);
									foreach ($attachments_activity as $file):
										$file = trim($file);
										if ($file !== ""):
									?>
											<tr>
												<td><?php echo $file; ?></td>
												<td class="text-center">
													<a href="<?php echo SERVERURL; ?>attachments/activity/<?php echo $file; ?>" download class="btn btn-primary btn-raised">
														<i class="zmdi zmdi-download"></i> Descargar
													</a>
												</td>
											</tr>
									<?php endif;
									endforeach; ?>
								</tbody>
							</table>
						<?php endif; ?>
					</div>
				<?php endif; ?>

				<!-- Formulario de respuesta -->
				<div class="full-box thumbnail" style="padding: 20px; margin-top: 30px;">
					<h3 class="text-titles text-center">
						<i class="zmdi zmdi-edit"></i> <?php echo $has_response ? 'Actualizar tu respuesta' : 'Enviar tu respuesta'; ?>
					</h3>

					<form action="" method="POST" enctype="multipart/form-data" autocomplete="off">
						<input type="hidden" name="codeVideo" value="<?php echo $rows['id']; ?>">
						<input type="hidden" name="codeUser" value="<?php echo $_SESSION['userKey']; ?>">

						<fieldset class="full-box" style="margin-bottom: 20px;">
							<legend><i class="zmdi zmdi-comment-text"></i> Texto de la respuesta</legend>
							<textarea name="response" id="spv-editor" class="full-box" style="min-height: 200px; font-size: 16px;" required><?php echo $current_response; ?></textarea>
						</fieldset>

						<fieldset class="full-box">
							<legend><i class="zmdi zmdi-attachment-alt"></i> <?php echo $has_response ? 'Agregar más archivos (los actuales se conservan)' : 'Adjuntar archivos a tu respuesta'; ?></legend>
							<div class="container-fluid">
								<div class="row">
									<div class="col-xs-12">
										<div class="form-group">
											<input type="file" name="attachments[]" multiple accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.ppt,.pptx">
											<div class="input-group">
												<input type="text" readonly class="form-control" placeholder="Elija los archivos adjuntos...">
												<span class="input-group-btn input-group-sm">
													<button type="button" class="btn btn-fab btn-fab-mini">
														<i class="zmdi zmdi-attachment-alt"></i>
													</button>
												</span>
											</div>
											<span>
												<small>Máximo 5MB por archivo. Formatos permitidos: imágenes (JPG/PNG), PDF, Word, PowerPoint.</small>
											</span>
										</div>
									</div>
								</div>
							</div>
						</fieldset>

						<p class="text-center" style="margin-top: 30px;">
							<button type="submit" class="btn btn-success btn-raised btn-lg">
								<i class="zmdi zmdi-check"></i> <?php echo $has_response ? 'Actualizar respuesta' : 'Guardar respuesta'; ?>
							</button>
						</p>
					</form>
				</div>

				<!-- Comentarios (más parecido al original: formulario separado y panel-info para la lista) -->
				<div class="full-box" style="margin-top: 40px;">
					<!-- Formulario de comentario -->
					<form action="<?php echo SERVERURL; ?>ajax/ajaxComment.php" class="ajaxDataForm well" data-form="AddComent" method="POST" enctype="multipart/form-data" autocomplete="off" style="margin-bottom: 40px;">
						<h3 class="text-titles text-center"><i class="zmdi zmdi-comment-edit"></i> Agregar un comentario</h3>
						<input type="hidden" name="codeClass" value="<?php echo $rows['id']; ?>">
						<input type="hidden" name="codeUser" value="<?php echo $_SESSION['userKey']; ?>">
						<input type="hidden" name="typeUSer" value="<?php echo $_SESSION['userType']; ?>">

						<div class="form-group">
							<label>Comentario</label>
							<textarea class="form-control" name="comment" rows="4" required style="font-size: 16px;"></textarea>
						</div>

						<div class="form-group">
							<label>Archivo adjunto (opcional)</label>
							<input type="file" name="attachment" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.ppt,.pptx">
							<div class="input-group">
								<input type="text" readonly class="form-control" placeholder="Elija un archivo adjunto...">
								<span class="input-group-btn input-group-sm">
									<button type="button" class="btn btn-fab btn-fab-mini">
										<i class="zmdi zmdi-attachment-alt"></i>
									</button>
								</span>
							</div>
							<span class="help-block">
								<small>Máximo 5MB. Formatos permitidos: imágenes (JPG/PNG), PDF, Word, PowerPoint.</small>
							</span>
						</div>

						<p class="text-center">
							<button type="submit" class="btn btn-info btn-raised">Enviar comentario</button>
						</p>

						<div class="full-box form-process"></div>
					</form>

					<!-- Lista de comentarios (panel-info original, sin sombras extras) -->
					<div class="panel panel-info">
						<div class="panel-heading">
							<h3 class="panel-title"><i class="zmdi zmdi-comments"></i> Comentarios de la clase</h3>
						</div>
						<div class="panel-body">
							<div class="list-group">
								<?php
								$page = explode("/", $_GET['views']);
								echo $insComment->pagination_comment_controller($code[1], $page[2], 10, $rows['id']);
								?>
							</div>
						</div>
					</div>
				</div>

			</div>
		</div>
	</div>
<?php else: ?>
	<div class="container-fluid">
		<div class="page-header">
			<h1 class="text-titles text-center"><i class="zmdi zmdi-videocam zmdi-hc-fw"></i> Clase</h1>
		</div>
		<p class="lead text-center">Lo sentimos, ocurrió un error inesperado.</p>
	</div>
<?php endif; ?>