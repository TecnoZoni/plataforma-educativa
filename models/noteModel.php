<?php
if ($actionsRequired) {
    require_once "../core/mainModel.php";
} else {
    require_once "./core/mainModel.php";
}

class noteModel extends mainModel
{

    public function get_student_note_model($codigo)
    {
        $query = self::connect()->prepare("SELECT Nota FROM respuestas WHERE Codigo=:codigo");
        $query->bindParam(":codigo", $codigo);
        $query->execute();
        return $query;
    }

    public function get_student_group_note_model($codigo)
    {
        $query = self::connect()->prepare("SELECT 
                                                        r.Nota
                                                    FROM 
                                                        respuestas r
                                                    WHERE 
                                                        r.Codigo COLLATE utf8mb3_spanish_ci IN (
                                                            SELECT eg.codigo 
                                                            FROM estudiante_grupo eg
                                                            WHERE eg.grupo_id IN (
                                                                SELECT grupo_id 
                                                                FROM estudiante_grupo 
                                                                WHERE codigo COLLATE utf8mb3_spanish_ci = :codigo
                                                            )
                                                        );");
        $query->bindParam(":codigo", $codigo);
        $query->execute();
        return $query;
    }
}
