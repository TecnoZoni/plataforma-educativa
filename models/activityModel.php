<?php
if ($actionsRequired) {
    require_once "../core/mainModel.php";
} else {
    require_once "./core/mainModel.php";
}

class activityModel extends mainModel
{

    /*----------  Add Activity Model  ----------*/
    public function add_response_activity_model($data)
    {
        $query = self::connect()->prepare("INSERT INTO respuestas(clase_id,Respuesta,Nota,Codigo,Adjuntos) VALUES(:clase_id,:Respuesta,:Nota,:Codigo,:Adjuntos)");
        $query->bindParam(":clase_id", $data['clase_id']);
        $query->bindParam(":Respuesta", $data['Respuesta']);
        $query->bindParam(":Nota", $data['Nota']);
        $query->bindParam(":Codigo", $data['Codigo']);
        $query->bindParam(":Adjuntos", $data['Adjuntos']);
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
                                                            c.Actividad,
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

    /*----------  Update Response Activity Model ----------*/
    protected function update_response_activity_model($data)
    {
        $query = self::connect()->prepare("UPDATE respuestas SET Respuesta=:Respuesta, Adjuntos=:Adjuntos WHERE id=:id");
        $query->bindParam(":Respuesta", $data['Respuesta']);
        $query->bindParam(":Adjuntos", $data['Adjuntos']);
        $query->bindParam(":id", $data['id']);
        $query->execute();
        return $query;
    }

    /*----------  Get User Response by Class and Code ----------*/
protected function get_user_response_model($clase_id, $codigo)
{
    $query = self::connect()->prepare("SELECT id, Respuesta, Adjuntos, Nota FROM respuestas WHERE clase_id = :clase_id AND Codigo = :codigo LIMIT 1");
    $query->bindParam(':clase_id', $clase_id, PDO::PARAM_INT);
    $query->bindParam(':codigo', $codigo, PDO::PARAM_STR);
    $query->execute();
    return $query->fetch(PDO::FETCH_ASSOC);
}
}
