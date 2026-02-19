<?php
if ($actionsRequired) {
	require_once "../models/videoModel.php";
} else {
	require_once "./models/videoModel.php";
}

class videoController extends videoModel
{

	/*----------  Add Video Controller  ----------*/
	public function add_video_controller()
	{
		$title = self::clean_string($_POST['title']);
		$teacher = self::clean_string($_POST['teacher']);
		$date = self::clean_string($_POST['date']);
		$video = $_POST['code'];
		$description = $_POST['description'];
		$activity = $_POST['activity'];

		$AttMaxSize = 5120;
		$AttDir = "../attachments/class/";

		$ATC = 0;
		$AttFinalName = "";
		$AttFinalNameTMP = "";
		foreach ($_FILES["attachments"]['tmp_name'] as $key => $tmp_name) {
			if ($_FILES["attachments"]["name"][$key]) {

				$AttType = $_FILES['attachments']['type'][$key];
				$AttSize = $_FILES['attachments']['size'][$key];

				if ($AttType == "image/jpeg" || $AttType == "image/png" || $AttType == "application/msword" || $AttType == "application/vnd.ms-powerpoint" || $AttType == "application/pdf" || $AttType == "application/vnd.openxmlformats-officedocument.wordprocessingml.document" || $AttType == "application/vnd.openxmlformats-officedocument.presentationml.presentation") {
					if (($AttSize / 1024) <= $AttMaxSize) {

						$AttName = str_ireplace(",", "", $_FILES["attachments"]["name"][$key]);
						$AttName = str_ireplace(" ", "_", $_FILES["attachments"]["name"][$key]);
						$finalDir = $AttDir . $AttName;

						if (is_file($finalDir)) {
							if ($AttFinalNameTMP != "") {
								$delTMP = explode(",", $AttFinalNameTMP);
								foreach ($delTMP as $delFile) {
									$filesAD = $AttDir . $delFile;
									chmod($filesAD, 0777);
									unlink($filesAD);
								}
							}
							$dataAlert = [
								"title" => "¡Ocurrió un error inesperado!",
								"text" => "Ya existe un archivo con el nombre <b>" . $AttName . "</b> registrado en el sistema por favor cambie el nombre del archivo adjunto antes de subirlo",
								"type" => "error"
							];
							return self::sweet_alert_single($dataAlert);
							exit();
						} else {
							chmod($AttDir, 0777);

							if (move_uploaded_file($_FILES["attachments"]['tmp_name'][$key], $finalDir)) {
								if ($ATC == 0) {
									$AttFinalName .= $AttName;
									$AttFinalNameTMP .= $AttName;
									$ATC++;
								} else {
									$AttFinalName .= "," . $AttName;
									$AttFinalNameTMP .= $AttName;
								}
							} else {
								$dataAlert = [
									"title" => "¡Ocurrió un error inesperado!",
									"text" => "No se pudo cargar uno o más de los archivos adjuntos seleccionados",
									"type" => "error"
								];
								return self::sweet_alert_single($dataAlert);
								exit();
							}
						}
					} else {
						$dataAlert = [
							"title" => "¡Ocurrió un error inesperado!",
							"text" => "El tamaño de uno de los archivos supera el límite de peso máximo que son 5MB",
							"type" => "error"
						];
						return self::sweet_alert_single($dataAlert);
						exit();
					}
				} else {
					$dataAlert = [
						"title" => "¡Ocurrió un error inesperado!",
						"text" => "El tipo de formato de uno de los archivo que acaba de seleccionar no esta permitido",
						"type" => "error"
					];
					return self::sweet_alert_single($dataAlert);
					exit();
				}
			}
		}

		$query1 = self::execute_single_query("SELECT Titulo FROM clase WHERE Titulo='$title'");

		if ($query1->rowCount() >= 1) {
			$dataAlert = [
				"title" => "¡Ocurrió un error inesperado!",
				"text" => "El título de la clase ya se encuentra registrado por favor elija otro",
				"type" => "error"
			];
			return self::sweet_alert_single($dataAlert);
		} else {
			$data = [
				"Video" => $video,
				"Fecha" => $date,
				"Titulo" => $title,
				"Tutor" => $teacher,
				"Descripcion" => $description,
				"Adjuntos" => $AttFinalName,
				"Actividad" => $activity
			];
			if (self::add_video_model($data)) {
				$dataAlert = [
					"title" => "¡Clase registrada!",
					"text" => "La clase se registró con éxito en el sistema",
					"type" => "success"
				];
				return self::sweet_alert_reset($dataAlert);
			} else {
				if ($AttFinalName != "") {
					$files = explode(",", $AttFinalName);
					foreach ($files as $filesA) {
						chmod($AttDir . $filesA, 0777);
						unlink($AttDir . $filesA);
					}
				}
				$dataAlert = [
					"title" => "¡Ocurrió un error inesperado!",
					"text" => "No hemos podido registrar la clase, por favor intente nuevamente",
					"type" => "error"
				];
				return self::sweet_alert_single($dataAlert);
			}
		}
	}


	/*----------  Pagination Video Controller  ----------*/
	public function pagination_video_controller($Pagina, $Registros)
	{
		$Pagina = self::clean_string($Pagina);
		$Registros = self::clean_string($Registros);

		$Pagina = (isset($Pagina) && $Pagina > 0) ? floor($Pagina) : 1;

		$Inicio = ($Pagina > 0) ? (($Pagina * $Registros) - $Registros) : 0;

		$Datos = self::execute_single_query("
				SELECT * FROM clase ORDER BY Fecha DESC LIMIT $Inicio,$Registros
			");
		$Datos = $Datos->fetchAll();

		$Total = self::execute_single_query("SELECT * FROM clase");
		$Total = $Total->rowCount();

		$Npaginas = ceil($Total / $Registros);

		$table = '
			<table class="table text-center">
				<thead>
					<tr>
						<th class="text-center">#</th>
						<th class="text-center">Fecha</th>
						<th class="text-center">Titulo</th>
						<th class="text-center">Tutor</th>
						<th class="text-center">Ver</th>
						<th class="text-center">Actualizar</th>
						<th class="text-center">Habilitar</th>
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
						<td>' . date("d/m/Y", strtotime($rows['Fecha'])) . '</td>
						<td>' . $rows['Titulo'] . '</td>
						<td>' . $rows['Tutor'] . '</td>
						<td>
							<a href="' . SERVERURL . 'classview/' . $rows['id'] . '/" class="btn btn-info btn-raised btn-xs">
								<i class="zmdi zmdi-tv"></i>
							</a>
						</td>
						<td>
							<a href="' . SERVERURL . 'classinfo/' . $rows['id'] . '/" class="btn btn-success btn-raised btn-xs">
								<i class="zmdi zmdi-refresh"></i>
							</a>
						</td>
						<td>
							<a href="' . SERVERURL . 'classgroup/' . $rows['id'] . '/" class="btn btn-primary btn-raised btn-xs">
								<i class="zmdi zmdi-accounts-list"></i>
							</a>
						</td>
						<td>
							<form action="' . SERVERURL . 'ajax/ajaxVideo.php" method="POST" enctype="multipart/form-data" autocomplete="off" data-form="DellVideo" class="ajaxDataForm">
								<input type="hidden" name="videoCode" value="' . $rows['id'] . '">
								<button type="submit" class="btn btn-danger btn-raised btn-xs">
									<i class="zmdi zmdi-delete"></i>
								</button>
								<div class="full-box form-process"></div>
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
				$table .= '<li><a href="' . SERVERURL . 'classlist/' . ($Pagina - 1) . '/">«</a></li>';
			}

			for ($i = 1; $i <= $Npaginas; $i++) {
				if ($Pagina == $i) {
					$table .= '<li class="active"><a href="' . SERVERURL . 'classlist/' . $i . '/">' . $i . '</a></li>';
				} else {
					$table .= '<li><a href="' . SERVERURL . 'classlist/' . $i . '/">' . $i . '</a></li>';
				}
			}

			if ($Pagina == $Npaginas) {
				$table .= '<li class="disabled"><a>»</a></li>';
			} else {
				$table .= '<li><a href="' . SERVERURL . 'classlist/' . ($Pagina + 1) . '/">»</a></li>';
			}

			$table .= '
						</ul>
					</nav>
				';
		}

		return $table;
	}


	/*----------  Pagination Video Now Controller  ----------*/
	public function pagination_video_now_controller($Pagina, $Registros, $datenow)
	{
		$Pagina = self::clean_string($Pagina);
		$Registros = self::clean_string($Registros);

		$Pagina = (isset($Pagina) && $Pagina > 0) ? floor($Pagina) : 1;

		$Inicio = ($Pagina > 0) ? (($Pagina * $Registros) - $Registros) : 0;

		$Datos = self::execute_single_query("
				SELECT * FROM clase WHERE Fecha='$datenow' ORDER BY Fecha DESC LIMIT $Inicio,$Registros
			");
		$Datos = $Datos->fetchAll();

		$Total = self::execute_single_query("SELECT * FROM clase WHERE Fecha='$datenow'");
		$Total = $Total->rowCount();

		$Npaginas = ceil($Total / $Registros);

		$table = '
			<table class="table text-center">
				<thead>
					<tr>
						<th class="text-center">#</th>
						<th class="text-center">Fecha</th>
						<th class="text-center">Titulo</th>
						<th class="text-center">Tutor</th>
						<th class="text-center">Ver</th>
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
						<td>' . date("d/m/Y", strtotime($rows['Fecha'])) . '</td>
						<td>' . $rows['Titulo'] . '</td>
						<td>' . $rows['Tutor'] . '</td>
						<td>
							<a href="' . SERVERURL . 'classview/' . $rows['id'] . '/" class="btn btn-info btn-raised btn-xs">
								<i class="zmdi zmdi-tv"></i>
							</a>
						</td>
					</tr>
					';
				$nt++;
			}
		} else {
			$table .= '
				<tr>
					<td colspan="5">No hay clases para hoy</td>
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
				$table .= '<li><a href="' . SERVERURL . 'videonow/' . ($Pagina - 1) . '/">«</a></li>';
			}

			for ($i = 1; $i <= $Npaginas; $i++) {
				if ($Pagina == $i) {
					$table .= '<li class="active"><a href="' . SERVERURL . 'videonow/' . $i . '/">' . $i . '</a></li>';
				} else {
					$table .= '<li><a href="' . SERVERURL . 'videonow/' . $i . '/">' . $i . '</a></li>';
				}
			}

			if ($Pagina == $Npaginas) {
				$table .= '<li class="disabled"><a>»</a></li>';
			} else {
				$table .= '<li><a href="' . SERVERURL . 'videonow/' . ($Pagina + 1) . '/">»</a></li>';
			}

			$table .= '
						</ul>
					</nav>
				';
		}

		return $table;
	}


	/*----------  Pagination Video Search Controller  ----------*/
	public function pagination_video_search_controller($Pagina, $Registros, $search)
	{
		$Pagina = self::clean_string($Pagina);
		$Registros = self::clean_string($Registros);
		$search = self::clean_string($search);

		$Pagina = (isset($Pagina) && $Pagina > 0) ? floor($Pagina) : 1;

		$Inicio = ($Pagina > 0) ? (($Pagina * $Registros) - $Registros) : 0;

		$Datos = self::execute_single_query("
				SELECT * FROM clase WHERE Titulo LIKE '%$search%' OR Tutor LIKE '%$search%' ORDER BY id DESC LIMIT $Inicio,$Registros
			");
		$Datos = $Datos->fetchAll();

		$Total = self::execute_single_query(
			"
				SELECT * FROM clase WHERE Titulo LIKE '%$search%' OR Tutor LIKE '%$search%'"
		);
		$Total = $Total->rowCount();

		$Npaginas = ceil($Total / $Registros);

		$table = '
			<table class="table text-center">
				<thead>
					<tr>
						<th class="text-center">#</th>
						<th class="text-center">Fecha</th>
						<th class="text-center">Titulo</th>
						<th class="text-center">Tutor</th>
						<th class="text-center">Ver</th>
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
						<td>' . date("d/m/Y", strtotime($rows['Fecha'])) . '</td>
						<td>' . $rows['Titulo'] . '</td>
						<td>' . $rows['Tutor'] . '</td>
						<td>
							<a href="' . SERVERURL . 'classview/' . $rows['id'] . '/" class="btn btn-info btn-raised btn-xs">
								<i class="zmdi zmdi-tv"></i>
							</a>
						</td>
					</tr>
					';
				$nt++;
			}
		} else {
			$table .= '
				<tr>
					<td colspan="5">
						No hay clases registradas que coincidan con el término de búsqueda que acaba de ingresar
					</td>
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
				$table .= '<li><a href="' . SERVERURL . 'search/' . ($Pagina - 1) . '/">«</a></li>';
			}

			for ($i = 1; $i <= $Npaginas; $i++) {
				if ($Pagina == $i) {
					$table .= '<li class="active"><a href="' . SERVERURL . 'search/' . $i . '/">' . $i . '</a></li>';
				} else {
					$table .= '<li><a href="' . SERVERURL . 'search/' . $i . '/">' . $i . '</a></li>';
				}
			}

			if ($Pagina == $Npaginas) {
				$table .= '<li class="disabled"><a>»</a></li>';
			} else {
				$table .= '<li><a href="' . SERVERURL . 'search/' . ($Pagina + 1) . '/">»</a></li>';
			}

			$table .= '
						</ul>
					</nav>
				';
		}

		return $table;
	}


	/*----------  Pagination Video List Controller  ----------*/
	public function pagination_video_list_controller($Pagina, $Registros)
{
    $Pagina = self::clean_string($Pagina);
    $Registros = self::clean_string($Registros);

    $Pagina = (isset($Pagina) && $Pagina > 0) ? floor($Pagina) : 1;
    $Inicio = ($Pagina > 0) ? (($Pagina * $Registros) - $Registros) : 0;

    // Verificar sesión del estudiante
    if (!isset($_SESSION['userKey']) || empty($_SESSION['userKey'])) {
        return '<p class="lead text-center text-danger">Error: Sesión de estudiante no válida.</p>';
    }

    $codigo_estudiante = self::clean_string($_SESSION['userKey']);

    // Obtener grupo del estudiante
    $query_grupo = self::execute_single_query("
        SELECT grupo_id 
        FROM estudiante_grupo 
        WHERE codigo = '$codigo_estudiante' 
        LIMIT 1
    ");

    if ($query_grupo->rowCount() == 0) {
        return '
            <table class="table text-center">
                <thead>
                    <tr>
                        <th class="text-center">#</th>
                        <th class="text-center">Fecha</th>
                        <th class="text-center">Titulo</th>
                        <th class="text-center">Tutor</th>
                        <th class="text-center">Ver</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="5">Aún no tienes un grupo asignado. Contacta al administrador.</td>
                    </tr>
                </tbody>
            </table>
        ';
    }

    $grupo_data = $query_grupo->fetch();
    $grupo_id = $grupo_data['grupo_id'];

    // Clases asignadas al grupo del estudiante
    $Datos = self::execute_single_query("
        SELECT c.* 
        FROM clase c
        INNER JOIN grupo_clase gc ON c.id = gc.clase_id
        WHERE gc.grupo_id = '$grupo_id'
        ORDER BY c.Fecha DESC 
        LIMIT $Inicio, $Registros
    ");
    $Datos = $Datos->fetchAll();

    // Total para paginación
    $TotalQuery = self::execute_single_query("
        SELECT COUNT(*) as total 
        FROM clase c
        INNER JOIN grupo_clase gc ON c.id = gc.clase_id
        WHERE gc.grupo_id = '$grupo_id'
    ");
    $Total = (int)($TotalQuery->fetch()['total'] ?? 0);

    $Npaginas = ceil($Total / $Registros);

    $table = '
        <table class="table text-center">
            <thead>
                <tr>
                    <th class="text-center">#</th>
                    <th class="text-center">Fecha</th>
                    <th class="text-center">Titulo</th>
                    <th class="text-center">Tutor</th>
                    <th class="text-center">Ver</th>
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
                    <td>' . date("d/m/Y", strtotime($rows['Fecha'])) . '</td>
                    <td>' . htmlspecialchars($rows['Titulo']) . '</td>
                    <td>' . htmlspecialchars($rows['Tutor']) . '</td>
                    <td>
                        <a href="' . SERVERURL . 'classview/' . $rows['id'] . '/" class="btn btn-info btn-raised btn-xs">
                            <i class="zmdi zmdi-tv"></i>
                        </a>
                    </td>
                </tr>
            ';
            $nt++;
        }
    } else {
        $table .= '
            <tr>
                <td colspan="5">No hay clases asignadas a tu grupo todavía.</td>
            </tr>
        ';
    }

    $table .= '</tbody></table>';

    // Paginación
    if ($Total >= 1) {
        $table .= '
            <nav class="text-center full-width">
                <ul class="pagination pagination-sm">
        ';

        if ($Pagina == 1) {
            $table .= '<li class="disabled"><a>«</a></li>';
        } else {
            $table .= '<li><a href="' . SERVERURL . 'videolist/' . ($Pagina - 1) . '/">«</a></li>';
        }

        for ($i = 1; $i <= $Npaginas; $i++) {
            if ($Pagina == $i) {
                $table .= '<li class="active"><a href="' . SERVERURL . 'videolist/' . $i . '/">' . $i . '</a></li>';
            } else {
                $table .= '<li><a href="' . SERVERURL . 'videolist/' . $i . '/">' . $i . '</a></li>';
            }
        }

        if ($Pagina == $Npaginas) {
            $table .= '<li class="disabled"><a>»</a></li>';
        } else {
            $table .= '<li><a href="' . SERVERURL . 'videolist/' . ($Pagina + 1) . '/">»</a></li>';
        }

        $table .= '</ul></nav>';
    }

    return $table;
}


	/*----------  Delete Video Controller  ----------*/
	public function delete_video_controller($code)
	{
		$code = self::clean_string($code);

		$query1 = self::execute_single_query("SELECT Adjuntos FROM clase WHERE id='$code'");
		$rows = $query1->fetch();

		$query2 = self::execute_single_query("SELECT Adjunto FROM comentarios WHERE id='$code'");
		$rows2 = $query2->fetch();

		if (self::delete_video_model($code) && self::execute_single_query("DELETE FROM comentarios WHERE id='$code'")) {

			//Delete files from Video
			$filesA = explode(",", $rows['Adjuntos']);
			$AttDir = "../attachments/class/";
			foreach ($filesA as $AttClass) {
				if ($AttClass != "") {
					chmod($AttDir . $AttClass, 0777);
					unlink($AttDir . $AttClass);
				}
			}

			//Delete files from Comments
			$filesA2 = explode(",", $rows2['Adjunto']);
			$AttDir2 = "../attachments/comments/";
			foreach ($filesA2 as $AttClass2) {
				if ($AttClass2 != "") {
					chmod($AttDir2 . $AttClass2, 0777);
					unlink($AttDir2 . $AttClass2);
				}
			}


			$dataAlert = [
				"title" => "¡Clase eliminada!",
				"text" => "La clase ha sido eliminada del sistema satisfactoriamente",
				"type" => "success"
			];
			return self::sweet_alert($dataAlert);
		} else {
			$dataAlert = [
				"title" => "¡Ocurrió un error inesperado!",
				"text" => "No pudimos eliminar la clase por favor intente nuevamente",
				"type" => "error"
			];
			return self::sweet_alert_single($dataAlert);
		}
	}


	/*----------  Delete Video Attachment Controller  ----------*/
	public function delete_video_attachment_controller($code, $name, $urls)
	{
		$code = self::clean_string($_POST['idAtt']);
		$name = $_POST['nameAtt'];

		$AttDir = "./attachments/class/";

		chmod($AttDir . $name, 0777);
		if (unlink($AttDir . $name)) {
			$query1 = self::execute_single_query("SELECT * FROM clase WHERE id='$code'");
			$rows = $query1->fetch();
			$filesA = explode(",", $rows['Adjuntos']);
			$catt = 0;
			$attFinal = "";
			foreach ($filesA as $AttClass) {
				if ($AttClass != $name) {
					if ($catt <= 0) {
						$attFinal .= $AttClass;
					} else {
						$attFinal .= "," . $AttClass;
					}
					$catt++;
				}
			}
			if (self::execute_single_query("UPDATE clase SET Adjuntos='$attFinal' WHERE id='$code'")) {
				$dataAlert = [
					"title" => "¡Archivo eliminado!",
					"text" => "El archivo adjunto de la clase fue eliminado con éxito",
					"type" => "success"
				];
				return self::sweet_alert_url_reload($dataAlert, $urls);
			} else {
				$dataAlert = [
					"title" => "¡Ocurrió un error inesperado!",
					"text" => "No pudimos eliminar el archivo adjunto de la clase por favor intente nuevamente",
					"type" => "error"
				];
				return self::sweet_alert_single($dataAlert);
			}
		} else {
			$dataAlert = [
				"title" => "¡Ocurrió un error inesperado!",
				"text" => "No pudimos eliminar el archivo adjunto de la clase por favor intente nuevamente",
				"type" => "error"
			];
			return self::sweet_alert_single($dataAlert);
		}
	}


	/*----------  Data Video Controller  ----------*/
	public function data_video_controller($Type, $Code)
	{
		$Type = self::clean_string($Type);
		$Code = self::clean_string($Code);

		$data = [
			"Tipo" => $Type,
			"id" => $Code
		];

		if ($videodata = self::data_video_model($data)) {
			return $videodata;
		} else {
			$dataAlert = [
				"title" => "¡Ocurrió un error inesperado!",
				"text" => "No hemos podido seleccionar los datos de la clase",
				"type" => "error"
			];
			return self::sweet_alert_single($dataAlert);
		}
	}

	/*----------  Update Video Controller  ----------*/
	public function update_video_controller()
	{
		$code = self::clean_string($_POST['upid']);
		$title = self::clean_string($_POST['title']);
		$teacher = self::clean_string($_POST['teacher']);
		$date = self::clean_string($_POST['date']);
		$video = $_POST['upcode'];
		$description = $_POST['description'];
		$activity = $_POST['activity'];

		$AttMaxSize = 5120;
		$AttDir = "../attachments/class/";

		$ATC = 0;
		$AttFinalName = "";
		$AttFinalNameTMP = "";

		foreach ($_FILES["attachments"]['tmp_name'] as $key => $tmp_name) {
			if ($_FILES["attachments"]["name"][$key]) {

				$AttType = $_FILES['attachments']['type'][$key];
				$AttSize = $_FILES['attachments']['size'][$key];

				if ($AttType == "image/jpeg" || $AttType == "image/png" || $AttType == "application/msword" || $AttType == "application/vnd.ms-powerpoint" || $AttType == "application/pdf" || $AttType == "application/vnd.openxmlformats-officedocument.wordprocessingml.document" || $AttType == "application/vnd.openxmlformats-officedocument.presentationml.presentation") {
					if (($AttSize / 1024) <= $AttMaxSize) {

						$AttName = str_ireplace(",", "", $_FILES["attachments"]["name"][$key]);
						$AttName = str_ireplace(" ", "_", $_FILES["attachments"]["name"][$key]);
						$finalDir = $AttDir . $AttName;

						if (is_file($finalDir)) {
							if ($AttFinalNameTMP != "") {
								$delTMP = explode(",", $AttFinalNameTMP);
								foreach ($delTMP as $delFile) {
									$filesAD = $AttDir . $delFile;
									chmod($filesAD, 0777);
									unlink($filesAD);
								}
							}
							$dataAlert = [
								"title" => "¡Ocurrió un error inesperado!",
								"text" => "Ya existe un archivo con el nombre <b>" . $AttName . "</b> registrado en el sistema por favor cambie el nombre del archivo adjunto antes de subirlo",
								"type" => "error"
							];
							return self::sweet_alert_single($dataAlert);
							exit();
						} else {
							chmod($AttDir, 0777);

							if (move_uploaded_file($_FILES["attachments"]['tmp_name'][$key], $finalDir)) {
								if ($ATC == 0) {
									$AttFinalName .= $AttName;
									$AttFinalNameTMP .= $AttName;
									$ATC++;
								} else {
									$AttFinalName .= "," . $AttName;
									$AttFinalNameTMP .= $AttName;
								}
							} else {
								$dataAlert = [
									"title" => "¡Ocurrió un error inesperado!",
									"text" => "No se pudo cargar uno o más de los archivos adjuntos seleccionados",
									"type" => "error"
								];
								return self::sweet_alert_single($dataAlert);
								exit();
							}
						}
					} else {
						$dataAlert = [
							"title" => "¡Ocurrió un error inesperado!",
							"text" => "El tamaño de uno de los archivos supera el límite de peso máximo que son 5MB",
							"type" => "error"
						];
						return self::sweet_alert_single($dataAlert);
						exit();
					}
				} else {
					$dataAlert = [
						"title" => "¡Ocurrió un error inesperado!",
						"text" => "El tipo de formato de uno de los archivo que acaba de seleccionar no esta permitido",
						"type" => "error"
					];
					return self::sweet_alert_single($dataAlert);
					exit();
				}
			}
		}

		$query1 = self::execute_single_query("SELECT Adjuntos FROM clase WHERE id='$code'");
		$rows1 = $query1->fetch();

		if ($rows1['Adjuntos'] != "") {
			$finalAtts = $rows1['Adjuntos'] . "," . $AttFinalName;
		} else {
			$finalAtts = $AttFinalName;
		}

		$data = [
			"id" => $code,
			"Titulo" => $title,
			"Fecha" => $date,
			"Video" => $video,
			"Tutor" => $teacher,
			"Descripcion" => $description,
			"Actividad" => $activity,
			"Adjuntos" => $finalAtts
		];

		if (self::update_video_model($data)) {
			$dataAlert = [
				"title" => "¡Clase actualizada!",
				"text" => "Los datos de la clase fueron actualizados con éxito",
				"type" => "success"
			];
			return self::sweet_alert($dataAlert);
		} else {
			$dataAlert = [
				"title" => "¡Ocurrió un error inesperado!",
				"text" => "No hemos podido actualizar los datos de la clase, por favor intente nuevamente",
				"type" => "error"
			];
			return self::sweet_alert_single($dataAlert);
		}
	}

	public function set_class_group_controller($code, $groups)
	{
		$clase_id = self::clean_string($code);

		if (!is_numeric($clase_id) || $clase_id <= 0) {
			$dataAlert = [
				"title" => "¡Ocurrió un error inesperado!",
				"text" => "ID de clase inválido",
				"type" => "error"
			];
			return self::sweet_alert_single($dataAlert);
		}

		if (!is_array($groups) || empty($groups)) {
			$dataAlert = [
				"title" => "¡Atención!",
				"text" => "Debes seleccionar al menos un grupo",
				"type" => "warning"
			];
			return self::sweet_alert_single($dataAlert);
		}

		$grupos_limpios = [];
		foreach ($groups as $grupo_id) {
			$grupo_id = self::clean_string($grupo_id);
			if (is_numeric($grupo_id) && $grupo_id > 0) {
				$grupos_limpios[] = (int)$grupo_id;
			}
		}

		if (empty($grupos_limpios)) {
			$dataAlert = [
				"title" => "¡Ocurrió un error inesperado!",
				"text" => "No hay grupos válidos",
				"type" => "error"
			];
			return self::sweet_alert_single($dataAlert);
		}

		if (self::set_class_group_model($clase_id, $grupos_limpios)) {
			$dataAlert = [
				"title" => "¡Éxito!",
				"text" => "La clase fue asignada correctamente a los grupos seleccionados",
				"type" => "success"
			];
			return self::sweet_alert_single($dataAlert);
		} else {
			$dataAlert = [
				"title" => "¡Ocurrió un error inesperado!",
				"text" => "No se pudo guardar la asignación, intente nuevamente",
				"type" => "error"
			];
			return self::sweet_alert_single($dataAlert);
		}
	}

	public function get_assigned_groups_to_class_controller($clase_id)
	{
		$clase_id = self::clean_string($clase_id);
		return self::execute_single_query("SELECT grupo_id FROM grupo_clase WHERE clase_id = '$clase_id'");
	}
}
