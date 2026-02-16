<div class="page-header text-center">
	<h1 class="text-titles">
		<i class="zmdi zmdi-graduation-cap zmdi-hc-fw"></i>
		Bienvenido a <small><?php echo COMPANY; ?></small>
	</h1>
	<p class="lead text-muted">
		Seleccione <a href="<?php echo SERVERURL; ?>videonow/" class="text-info"><strong>CLASES DE HOY</strong></a>
		para ver las clases del día o <a href="<?php echo SERVERURL; ?>videolist/" class="text-info"><strong>LISTADO DE CLASES</strong></a>
		para acceder a todas las clases impartidas.
	</p>
</div>

<?php
require_once "./controllers/noteController.php";
$insNote = new noteController();
$notaIndividual = $insNote->get_student_note_controller($_SESSION['userKey']);
$notaGrupal = $insNote->get_student_group_note_controller($_SESSION['userKey']);
?>

<!-- Tarjetas de notas (modernas, grandes iconos, números destacados) -->
<div class="container-fluid">
	<div class="row">
		<!-- Nota Individual -->
		<div class="col-xs-12 col-md-6">
			<div class="panel panel-primary" style="border: none; border-radius: 15px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.2);">
				<div class="panel-heading text-center" style="background: linear-gradient(135deg, #2196F3, #21CBF3); padding: 25px;">
					<i class="zmdi zmdi-account-circle zmdi-hc-4x" style="color: white;"></i>
					<h3 class="panel-title text-white" style="margin-top: 15px; font-size: 24px;">Mi nota individual</h3>
				</div>
				<div class="panel-body text-center" style="padding: 40px 20px; background: #f9f9f9;">
					<h1 style="font-size: 90px; font-weight: bold; color: #2196F3; margin: 0;">
						<?php echo $notaIndividual; ?>
					</h1>
					<p class="text-muted" style="font-size: 18px; margin-top: 10px;">Promedio personal</p>
				</div>
			</div>
		</div>

		<!-- Nota Grupal -->
		<div class="col-xs-12 col-md-6">
			<div class="panel panel-success" style="border: none; border-radius: 15px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.2);">
				<div class="panel-heading text-center" style="background: linear-gradient(135deg, #4CAF50, #8BC34A); padding: 25px;">
					<i class="zmdi zmdi-accounts zmdi-hc-4x" style="color: white;"></i>
					<h3 class="panel-title text-white" style="margin-top: 15px; font-size: 24px;">Nota grupal</h3>
				</div>
				<div class="panel-body text-center" style="padding: 40px 20px; background: #f9f9f9;">
					<h1 style="font-size: 90px; font-weight: bold; color: #4CAF50; margin: 0;">
						<?php echo $notaGrupal; ?>
					</h1>
					<p class="text-muted" style="font-size: 18px; margin-top: 10px;">Promedio del grupo</p>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Banner (imagen atractiva, full-width) -->
<div class="container-fluid mb-5">
	<img src="<?php echo SERVERURL; ?>views/assets/img/banner.png" alt="<?php echo COMPANY; ?>" class="img-responsive center-block" style="border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.3);">
</div>