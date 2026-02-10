<?php
if ($actionsRequired) {
    require_once "../models/activityModel.php";
} else {
    require_once "./models/activityModel.php";
}

class activityController extends activityModel
{
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
}
