<?php
if ($actionsRequired) {
	require_once "../models/groupModel.php";
} else {
	require_once "./models/groupModel.php";
}

class groupController extends groupModel
{
	/*----------  Add Group Controller  ----------*/
	public function add_group_controller()
	{
		// Verificar que lleguen todos los campos requeridos
		if (!isset($_POST['name'], $_POST['reward'], $_POST['category'])) {
			return '';
		}

		// Limpiar y trim
		$name    = trim(self::clean_string($_POST['name']));
		$reward  = trim(self::clean_string($_POST['reward']));
		$cat_str = self::clean_string($_POST['category']);

		// Convertir categoría a entero y validar que sea numérico puro
		$category = intval($cat_str);

		// Validaciones server-side (imposible saltarse)
		$errors = [];

		if ($name === '' || strlen($name) > 100 || !preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9 ]+$/u', $name)) {
			$errors[] = "Nombre inválido: solo letras, acentos y espacios (1-100 caracteres).";
		}

		if ($reward === '' || strlen($reward) > 100 || !preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9 ]+$/u', $reward)) {
			$errors[] = "Recompensa inválida: letras, números, acentos y espacios (1-100 caracteres).";
		}

		if ($cat_str !== (string)$category || $category <= 0) {
			$errors[] = "Categoría inválida (debe ser un número entero positivo).";
		}

		if (!empty($errors)) {
			$text = implode('<br>', $errors);
			$dataAlert = [
				"title" => "Datos incorrectos",
				"text"  => $text,
				"type"  => "error"
			];
			return self::sweet_alert_single($dataAlert);
		}

		// Validar que la categoría exista en la base de datos
		$categorias = self::get_categories_controller();
		$categoria_valida = false;
		foreach ($categorias as $cat) {
			if ((int)$cat['id'] === $category) {
				$categoria_valida = true;
				break;
			}
		}

		if (!$categoria_valida) {
			$dataAlert = [
				"title" => "Categoría no válida",
				"text"  => "La categoría seleccionada no existe en el sistema.",
				"type"  => "error"
			];
			return self::sweet_alert_single($dataAlert);
		}

		// Datos para el modelo (corregido nombre de columna según diagrama de BD)
		$dataGroup = [
			"Nombre"      => $name,
			"Recompensa"  => $reward,
			"categoria_id" => $category
		];

		$result = self::add_group_model($dataGroup);

		if ($result->rowCount() === 1) {
			unset($_POST);
			$dataAlert = [
				"title" => "¡Grupo registrado!",
				"text"  => "El grupo se registró con éxito en el sistema",
				"type"  => "success"
			];
			return self::sweet_alert_single($dataAlert);
		} else {
			$dataAlert = [
				"title" => "Error",
				"text"  => "No se pudo registrar el grupo. Inténtelo de nuevo.",
				"type"  => "error"
			];
			return self::sweet_alert_single($dataAlert);
		}
	}

	public function get_categories_controller()
	{
		return groupModel::get_categories_model();
	}

	public function delete_group_controller($code)
	{
		$code = self::clean_string($code);

		if (self::delete_account($code) && self::delete_group_model($code)) {
			$dataAlert = [
				"title" => "¡Grupo eliminado!",
				"text" => "El grupo ha sido eliminado del sistema satisfactoriamente",
				"type" => "success"
			];
			return self::sweet_alert_single($dataAlert);
		} else {
			$dataAlert = [
				"title" => "¡Ocurrió un error inesperado!",
				"text" => "No pudimos eliminar el grupo por favor intente nuevamente",
				"type" => "error"
			];
			return self::sweet_alert_single($dataAlert);
		}
	}

	public function pagination_group_controller($Pagina, $Registros)
	{
		$Pagina = self::clean_string($Pagina);
		$Registros = self::clean_string($Registros);

		$Pagina = (isset($Pagina) && $Pagina > 0) ? floor($Pagina) : 1;

		$Inicio = ($Pagina > 0) ? (($Pagina * $Registros) - $Registros) : 0;

		$Datos = self::execute_single_query("
				SELECT * FROM grupos ORDER BY id ASC LIMIT $Inicio,$Registros
			");
		$Datos = $Datos->fetchAll();

		$Total = self::execute_single_query("SELECT * FROM grupos");
		$Total = $Total->rowCount();

		$Npaginas = ceil($Total / $Registros);

		$table = '
			<table class="table text-center">
				<thead>
					<tr>
						<th class="text-center">#</th>
						<th class="text-center">Nombre</th>
						<th class="text-center">Recompensa</th>
						<th class="text-center">Categoría</th>
						<th class="text-center">A. Grupo</th>
						<th class="text-center">A. Estudiantes</th>
						<th class="text-center">Eliminar</th>
					</tr>
				</thead>
				<tbody>
			';

		if ($Total >= 1) {
			$nt = $Inicio + 1;
			foreach ($Datos as $rows) {
				$table .= '
					<tr>
						<td>' . $nt . '</td>
						<td>' . $rows['Nombre'] . '</td>
						<td>' . $rows['Recompensa'] . '</td>
						<td>' . $rows['categoria_id'] . '</td>
						<td>
							<a href="' . SERVERURL . 'groupinfo/' . $rows['id'] . '/" class="btn btn-success btn-raised btn-xs">
								<i class="zmdi zmdi-refresh"></i>
							</a>
						</td>
						<td>
							<a href="' . SERVERURL . 'groupstudent/' . $rows['id'] . '/" class="btn btn-success btn-raised btn-xs">
								<i class="zmdi zmdi-refresh"></i>
							</a>
						</td>
						<td>
							<a href="#!" class="btn btn-danger btn-raised btn-xs btnFormsAjax" data-action="delete" data-id="del-' . $rows['id'] . '">
								<i class="zmdi zmdi-delete"></i>
							</a>
							<form action="" id="del-' . $rows['id'] . '" method="POST" enctype="multipart/form-data">
								<input type="hidden" name="groupCode" value="' . $rows['id'] . '">
							</form>
						</td>
					</tr>
					';
				$nt++;
			}
		} else {
			$table .= '
				<tr>
					<td colspan="5">No hay registros en el sistema</td>
				</tr>
				';
		}

		$table .= '
				</tbody>
			</table>
			';

		if ($Total >= 1) {
			$table .= '
					<nav class="text-center full-width">
						<ul class="pagination pagination-sm">
				';

			if ($Pagina == 1) {
				$table .= '<li class="disabled"><a>«</a></li>';
			} else {
				$table .= '<li><a href="' . SERVERURL . 'studentlist/' . ($Pagina - 1) . '/">«</a></li>';
			}

			for ($i = 1; $i <= $Npaginas; $i++) {
				if ($Pagina == $i) {
					$table .= '<li class="active"><a href="' . SERVERURL . 'studentlist/' . $i . '/">' . $i . '</a></li>';
				} else {
					$table .= '<li><a href="' . SERVERURL . 'studentlist/' . $i . '/">' . $i . '</a></li>';
				}
			}

			if ($Pagina == $Npaginas) {
				$table .= '<li class="disabled"><a>»</a></li>';
			} else {
				$table .= '<li><a href="' . SERVERURL . 'studentlist/' . ($Pagina + 1) . '/">»</a></li>';
			}

			$table .= '
						</ul>
					</nav>
				';
		}

		return $table;
	}

	public function update_group_controller()
	{
		$id = self::clean_string($_POST['id']);
		$name = self::clean_string($_POST['name']);
		$recompensa = self::clean_string($_POST['reward']);
		$categoria_id = self::clean_string($_POST['category']);

		$data = [
			"id" => $id,
			"Nombre" => $name,
			"Recompensa" => $recompensa,
			"categoria_id" => $categoria_id
		];

		if (self::update_group_model($data)) {
			$dataAlert = [
				"title" => "¡Grupo actualizado!",
				"text" => "Los datos del grupo fueron actualizados con éxito",
				"type" => "success"
			];
			return self::sweet_alert_single($dataAlert);
		} else {
			$dataAlert = [
				"title" => "¡Ocurrió un error inesperado!",
				"text" => "No hemos podido actualizar los datos del grupo, por favor intente nuevamente",
				"type" => "error"
			];
			return self::sweet_alert_single($dataAlert);
		}
	}

	public function data_group_controller($Type, $Id)
	{
		$Type = self::clean_string($Type);
		$Id = self::clean_string($Id);

		$data = [
			"Tipo" => $Type,
			"Id" => $Id
		];

		if ($groupdata = self::data_group_model($data)) {
			return $groupdata;
		} else {
			$dataAlert = [
				"title" => "¡Ocurrió un error inesperado!",
				"text" => "No hemos podido seleccionar los datos del grupo",
				"type" => "error"
			];
			return self::sweet_alert_single($dataAlert);
		}
	}

	public function get_students_by_dont_group_controller()
	{
		return self::get_students_by_dont_group_model();
	}

	public function get_students_by_group_controller($id)
	{
		return self::get_students_by_group_model($id);
	}

	public function update_student_group_controller()
	{
		// ID del grupo
		$grupo_id = self::clean_string($_POST['id'] ?? '');

		if ($grupo_id === '' || !is_numeric($grupo_id) || $grupo_id <= 0) {
			$dataAlert = [
				"title" => "Error",
				"text"  => "ID de grupo inválido.",
				"type"  => "error"
			];
			return self::sweet_alert_single($dataAlert);
		}

		// Verificar que el grupo exista
		$check_group = self::execute_single_query("SELECT id FROM grupos WHERE id = '$grupo_id'");
		if ($check_group->rowCount() == 0) {
			$dataAlert = [
				"title" => "Error",
				"text"  => "El grupo seleccionado no existe.",
				"type"  => "error"
			];
			return self::sweet_alert_single($dataAlert);
		}

		// Alumnos seleccionados
		if (!isset($_POST['students']) || !is_array($_POST['students']) || empty($_POST['students'])) {
			$dataAlert = [
				"title" => "Información",
				"text"  => "No se seleccionaron alumnos para agregar.",
				"type"  => "info"
			];
			return self::sweet_alert_single($dataAlert);
		}

		$students = $_POST['students'];
		$students = array_map([self::class, 'clean_string'], $students);
		$students = array_unique(array_filter($students)); // eliminar vacíos y duplicados

		if (empty($students)) {
			$dataAlert = [
				"title" => "Información",
				"text"  => "No hay alumnos válidos para agregar.",
				"type"  => "info"
			];
			return self::sweet_alert_single($dataAlert);
		}

		// Contar alumnos actuales en el grupo
		$current = self::get_students_by_group_model($grupo_id);
		$current_count = $current->rowCount();

		// Verificar límite de 10
		if (($current_count + count($students)) > 10) {
			$dataAlert = [
				"title" => "Límite excedido",
				"text"  => "No se pueden agregar todos los alumnos: se superaría el límite de 10 por grupo.",
				"type"  => "error"
			];
			return self::sweet_alert_single($dataAlert);
		}

		// Validar cada estudiante
		foreach ($students as $codigo) {
			// Existe en tabla estudiante
			$check_est = self::execute_single_query("SELECT Codigo FROM estudiante WHERE Codigo = '$codigo'");
			if ($check_est->rowCount() == 0) {
				$dataAlert = [
					"title" => "Error",
					"text"  => "El estudiante con código $codigo no existe en el sistema.",
					"type"  => "error"
				];
				return self::sweet_alert_single($dataAlert);
			}

			// No está asignado a ningún grupo
			$check_asig = self::execute_single_query("SELECT id FROM estudiante_grupo WHERE codigo = '$codigo'");
			if ($check_asig->rowCount() > 0) {
				$dataAlert = [
					"title" => "Error",
					"text"  => "El estudiante con código $codigo ya está asignado a un grupo.",
					"type"  => "error"
				];
				return self::sweet_alert_single($dataAlert);
			}
		}

		if (self::update_student_group_model($grupo_id, $students)) {
			$dataAlert = [
				"title" => "¡Alumnos agregados!",
				"text"  => "Los alumnos se agregaron correctamente al grupo.",
				"type"  => "success"
			];
		} else {
			$dataAlert = [
				"title" => "Error",
				"text"  => "No se pudieron agregar los alumnos. Inténtelo nuevamente.",
				"type"  => "error"
			];
		}

		return self::sweet_alert_single($dataAlert);
	}
}
