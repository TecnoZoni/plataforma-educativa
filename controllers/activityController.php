<?php
if ($actionsRequired) {
    require_once "../models/activityModel.php";
} else {
    require_once "./models/activityModel.php";
}

class activityController extends activityModel
{
    public function add_response_activity_controller()
    {
        $id_clase = self::clean_string($_POST['codeVideo']);
        $codeUser = self::clean_string($_POST['codeUser']);
        $response = $_POST['response'];

        $AttMaxSize = 5120; // 5MB en KB

        // Ruta correcta DENTRO del proyecto (plataforma-educativa/attachments/activity/)
        $baseDir = dirname(__DIR__) . '/attachments/';
        $AttDir = $baseDir . 'activity/';

        $AttFinalName = "";
        $uploaded_files = [];

        // Crear carpetas si no existen (primero attachments, luego activity)
        if (!is_dir($baseDir)) {
            if (!mkdir($baseDir, 0777, true)) {
                return self::sweet_alert_single([
                    "title" => "Error crítico",
                    "text" => "No se pudo crear el directorio base para adjuntos. Contactá al administrador.",
                    "type" => "error"
                ]);
            }
        }
        if (!is_dir($AttDir)) {
            if (!mkdir($AttDir, 0777, true)) {
                return self::sweet_alert_single([
                    "title" => "Error crítico",
                    "text" => "No se pudo crear el directorio para adjuntos de actividades. Contactá al administrador.",
                    "type" => "error"
                ]);
            }
        }

        // Proceso de subida de archivos
        if (!empty($_FILES["attachments"]["name"][0])) {
            foreach ($_FILES["attachments"]['tmp_name'] as $key => $tmp_name) {
                if (empty($_FILES["attachments"]["name"][$key])) {
                    continue;
                }

                $AttNameOrig = $_FILES["attachments"]["name"][$key];
                $AttName = str_ireplace([" ", ","], "_", $AttNameOrig);
                $AttType = $_FILES['attachments']['type'][$key];
                $AttSize = $_FILES['attachments']['size'][$key];
                $finalPath = $AttDir . $AttName;

                $allowed_types = [
                    "image/jpeg",
                    "image/png",
                    "application/msword",
                    "application/vnd.ms-powerpoint",
                    "application/pdf",
                    "application/vnd.openxmlformats-officedocument.wordprocessingml.document",
                    "application/vnd.openxmlformats-officedocument.presentationml.presentation"
                ];

                // Validaciones
                if (!in_array($AttType, $allowed_types)) {
                    foreach ($uploaded_files as $uf) @unlink($AttDir . $uf);
                    return self::sweet_alert_single(["title" => "Error", "text" => "Tipo de archivo no permitido: {$AttName}", "type" => "error"]);
                }
                if (($AttSize / 1024) > $AttMaxSize) {
                    foreach ($uploaded_files as $uf) @unlink($AttDir . $uf);
                    return self::sweet_alert_single(["title" => "Error", "text" => "El archivo {$AttName} supera los 5MB", "type" => "error"]);
                }
                if (is_file($finalPath)) {
                    foreach ($uploaded_files as $uf) @unlink($AttDir . $uf);
                    return self::sweet_alert_single(["title" => "Error", "text" => "Ya existe un archivo con el nombre {$AttName}", "type" => "error"]);
                }

                // Subir archivo
                if (move_uploaded_file($tmp_name, $finalPath)) {
                    $uploaded_files[] = $AttName;
                    $AttFinalName = $AttFinalName === "" ? $AttName : $AttFinalName . "," . $AttName;
                } else {
                    foreach ($uploaded_files as $uf) @unlink($AttDir . $uf);
                    return self::sweet_alert_single(["title" => "Error", "text" => "No se pudo subir el archivo {$AttName}", "type" => "error"]);
                }
            }
        }

        // Buscar si ya existe respuesta del alumno
        $query = self::connect()->prepare("SELECT id, Adjuntos FROM respuestas WHERE clase_id=:clase_id AND Codigo=:Codigo");
        $query->execute([':clase_id' => $id_clase, ':Codigo' => $codeUser]);
        $existing = $query->fetch();

        if ($existing) {
            // Actualizar
            $old_adjuntos = $existing['Adjuntos'] ?? "";
            $final_adjuntos = $AttFinalName !== "" ? ($old_adjuntos !== "" ? $old_adjuntos . "," . $AttFinalName : $AttFinalName) : $old_adjuntos;

            $data = [
                "id" => $existing['id'],
                "Respuesta" => $response,
                "Adjuntos" => $final_adjuntos
            ];

            if (self::update_response_activity_model($data)) {
                return self::sweet_alert_reset(["title" => "¡Respuesta actualizada!", "text" => "Tu respuesta se actualizó correctamente", "type" => "success"]);
            } else {
                foreach ($uploaded_files as $uf) @unlink($AttDir . $uf);
                return self::sweet_alert_single(["title" => "Error", "text" => "No se pudo actualizar la respuesta", "type" => "error"]);
            }
        } else {
            // Insertar nueva
            $final_adjuntos = $AttFinalName;

            $data = [
                "clase_id" => $id_clase,
                "Respuesta" => $response,
                "Nota" => 0.0,
                "Codigo" => $codeUser,
                "Adjuntos" => $final_adjuntos
            ];

            if (self::add_response_activity_model($data)) {
                return self::sweet_alert_reset(["title" => "¡Respuesta guardada!", "text" => "Tu respuesta se guardó correctamente", "type" => "success"]);
            } else {
                foreach ($uploaded_files as $uf) @unlink($AttDir . $uf);
                return self::sweet_alert_single(["title" => "Error", "text" => "No se pudo guardar la respuesta", "type" => "error"]);
            }
        }
    }

    /*----------  Pagination Activity Controller  ----------*/
    public function pagination_activity_controller($Pagina, $Registros)
    {
        $Pagina = self::clean_string($Pagina);
        $Registros = self::clean_string($Registros);

        $Pagina = (isset($Pagina) && $Pagina > 0) ? floor($Pagina) : 1;

        $Inicio = ($Pagina > 0) ? (($Pagina * $Registros) - $Registros) : 0;

        $Datos = self::execute_single_query("
        SELECT 
            c.id AS clase_id,
            c.Fecha,
            c.Titulo,
            CONCAT(e.Nombres, ' ', e.Apellidos) AS Alumno,
            res.Nota,
            res.id AS respuesta_id
        FROM 
            respuestas res
        INNER JOIN 
            clase c ON res.clase_id = c.id
        INNER JOIN 
            estudiante e ON res.Codigo COLLATE utf8mb3_spanish_ci = e.Codigo
        ORDER BY 
            c.id DESC, res.id ASC
        LIMIT $Inicio,$Registros
    ");
        $Datos = $Datos->fetchAll();

        $TotalQuery = self::execute_single_query("
        SELECT COUNT(*) 
        FROM respuestas res
        INNER JOIN clase c ON res.clase_id = c.id
        INNER JOIN estudiante e ON res.Codigo COLLATE utf8mb3_spanish_ci = e.Codigo
    ");
        $Total = $TotalQuery->fetchColumn();

        $Npaginas = ceil($Total / $Registros);

        $table = '
    <table class="table text-center">
        <thead>
            <tr>
                <th class="text-center">#</th>
                <th class="text-center">Fecha</th>
                <th class="text-center">Titulo</th>
                <th class="text-center">Alumno</th>
                <th class="text-center">Nota</th>
                <th class="text-center">Ver</th>
            </tr>
        </thead>
        <tbody>
    ';

        if ($Total >= 1) {
            $nt = $Inicio + 1;
            foreach ($Datos as $rows) {
                $nota_display = ($rows['Nota'] !== null && $rows['Nota'] != 0) ? $rows['Nota'] : '-';
                $table .= '
            <tr>
                <td>' . $nt . '</td>
                <td>' . date("d/m/Y", strtotime($rows['Fecha'])) . '</td>
                <td>' . $rows['Titulo'] . '</td>
                <td>' . htmlspecialchars($rows['Alumno']) . '</td>
                <td>' . $nota_display . '</td>
                <td>
                    <a href="' . SERVERURL . 'activityview/' . $rows['respuesta_id'] . '/" class="btn btn-info btn-raised btn-xs">
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
            <td colspan="8">No hay registros en el sistema</td>
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

    /*----------  Data Activity Controller  ----------*/
    public function data_activity_controller($Type, $Code)
    {
        $Type = self::clean_string($Type);
        $Code = self::clean_string($Code);

        $data = [
            "Tipo" => $Type,
            "id" => $Code
        ];

        if ($videodata = self::data_activity_model($data)) {
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

    public function update_note_activity_controller()
    {
        $id = self::clean_string($_POST['respuesta_id']);
        $nota = self::clean_string($_POST['note']);


        $data = [
            "id" => $id,
            "Nota" => $nota
        ];

        if (self::update_note_activity_model($data)) {
            $dataAlert = [
                "title" => "¡Nota actualizada!",
                "text" => "La nota de la actividad fue actualizada con éxito",
                "type" => "success"
            ];
            return self::sweet_alert_reset($dataAlert);
        } else {
            $dataAlert = [
                "title" => "¡Ocurrió un error inesperado!",
                "text" => "No hemos podido actualizar la nota de la actividad, por favor intente nuevamente",
                "type" => "error"
            ];
            return self::sweet_alert_single($dataAlert);
        }
    }

    /*----------  Get User Response Controller ----------*/
    public function get_user_response_controller($clase_id, $codigo)
    {
        return self::get_user_response_model($clase_id, $codigo);
    }
}
