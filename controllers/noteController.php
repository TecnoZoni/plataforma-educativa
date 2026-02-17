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

        if ($notas->rowCount() === 0) {
            return 0.00;
        }

        $notasArray = $notas->fetchAll(PDO::FETCH_ASSOC);
        $suma = 0;
        foreach ($notasArray as $nota) {
            $suma += (float) $nota['Nota'];
        }

        $promedio = $suma / $notas->rowCount();
        return round($promedio, 2);
    }


    public function get_student_group_note_controller($codigo)
    {
        $codigo = self::clean_string($codigo);
        $notas = self::get_student_group_note_model($codigo);

        if ($notas->rowCount() === 0) {
            return 0.00;
        }

        $notasArray = $notas->fetchAll(PDO::FETCH_ASSOC);
        $suma = 0;
        foreach ($notasArray as $nota) {
            $suma += (float) $nota['Nota'];
        }

        $promedio = $suma / $notas->rowCount();
        return round($promedio, 2);
    }
}
