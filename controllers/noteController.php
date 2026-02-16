<?php
if ($actionsRequired) {
    require_once "../models/noteModel.php";
} else {
    require_once "./models/noteModel.php";
}

class noteController extends noteModel
{

    public function get_student_note_controller($codigo)
    {
        $codigo = self::clean_string($codigo);
        $notas = self::get_student_note_model($codigo);
        $cantNotas = $notas->rowCount();
        $promedio = 0;

        if ($notas->rowCount() > 0) {
            $notas = $notas->fetchAll(PDO::FETCH_ASSOC);
            foreach ($notas as $nota) {
                $promedio += $nota['Nota'];
            }
            $promedio = $promedio / $cantNotas;
            return round($promedio, 2);
        } else {
            return self::sweet_alert_single([
                "title" => "Error crítico",
                "text" => "No se encontraron notas para el estudiante.",
                "type" => "error"
            ]);
        }
    }

    public function get_student_group_note_controller($codigo)
    {
        $codigo = self::clean_string($codigo);
        $notas = self::get_student_group_note_model($codigo);
        $cantNotas = $notas->rowCount();
        $promedio = 0;

        if ($notas->rowCount() > 0) {
            $notas = $notas->fetchAll(PDO::FETCH_ASSOC);
            foreach ($notas as $nota) {
                $promedio += $nota['Nota'];
            }
            $promedio = $promedio / $cantNotas;
            return round($promedio, 2);
        } else {
            return self::sweet_alert_single([
                "title" => "Error crítico",
                "text" => "No se encontraron notas para el grupo del estudiante.",
                "type" => "error"
            ]);
        }
    }

}
