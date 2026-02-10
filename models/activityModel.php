<?php
if ($actionsRequired) {
    require_once "../core/mainModel.php";
} else {
    require_once "./core/mainModel.php";
}

class activityModel extends mainModel
{

    /*----------  Add Activity Model  ----------*/
    public function add_activity_model($data)
    {
        $query = self::connect()->prepare("INSERT INTO clase(Video,Fecha,Titulo,Tutor,Descripcion,Adjuntos,Actividad) VALUES(:Video,:Fecha,:Titulo,:Tutor,:Descripcion,:Adjuntos,:Actividad)");
        $query->bindParam(":Video", $data['Video']);
        $query->bindParam(":Fecha", $data['Fecha']);
        $query->bindParam(":Titulo", $data['Titulo']);
        $query->bindParam(":Tutor", $data['Tutor']);
        $query->bindParam(":Descripcion", $data['Descripcion']);
        $query->bindParam(":Adjuntos", $data['Adjuntos']);
        $query->bindParam(":Actividad", $data['Actividad']);
        $query->execute();
        return $query;
    }

    /*----------  Data Activity Model  ----------*/
    public function data_activity_model($data)
    {
        if ($data['Tipo'] == "Count") {
            $query = self::connect()->prepare("SELECT id FROM respuestas");
        } elseif ($data['Tipo'] == "Only") {
            $query = self::connect()->prepare("SELECT * FROM respuestas WHERE id=:id");
            $query->bindParam(":id", $data['id']);
        } elseif ($data['Tipo'] == "All") {
            $query = self::connect()->prepare("SELECT
                                                            c.id AS clase_id,
                                                            c.Titulo,
                                                            CONCAT(e.Nombres, ' ', e.Apellidos) AS Alumno,
                                                            res.Nota,
                                                            res.Adjuntos,
                                                            res.Respuesta,
                                                            res.Codigo,
                                                            res.id AS respuesta_id
                                                        FROM
                                                            respuestas res
                                                        INNER JOIN
                                                            clase c ON res.clase_id = c.id
                                                        INNER JOIN
                                                            estudiante e ON res.Codigo COLLATE utf8mb3_spanish_ci = e.Codigo
                                                        WHERE
                                                            res.id = :id");
            $query->bindParam(":id", $data['id']);
        }
        $query->execute();
        return $query;
    }

    public function update_note_activity_model($data)
    {
        $query = self::connect()->prepare("UPDATE respuestas SET Nota=:Nota WHERE id=:id");
        $query->bindParam(":Nota", $data['Nota']);
        $query->bindParam(":id", $data['id']);
        $query->execute();
        return $query;
    }
}
