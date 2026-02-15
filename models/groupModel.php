<?php
if ($actionsRequired) {
	require_once "../core/mainModel.php";
} else {
	require_once "./core/mainModel.php";
}

class groupModel extends mainModel
{


	public function add_group_model($data)
	{
		$query = self::connect()->prepare("INSERT INTO grupos(Nombre,Recompensa,categoria_id) VALUES(:Nombre,:Recompensa,:categoria_id)");
		$query->bindParam(":Nombre", $data['Nombre']);
		$query->bindParam(":Recompensa", $data['Recompensa']);
		$query->bindParam(":categoria_id", $data['categoria_id']);
		$query->execute();
		return $query;
	}

	protected function get_categories_model()
	{
		$query = "SELECT id, Nombre FROM categoria ORDER BY id";
		return self::execute_single_query($query);
	}

	public function delete_group_model($code)
	{
		$code = (int)$code;
		if ($code <= 0) {
			return false;
		}

		$conn = self::connect();

		try {
			$conn->beginTransaction();

			// Borrar asignaciones de alumnos (tabla intermedia)
			$sql1 = "DELETE FROM estudiante_grupo WHERE grupo_id = :id";
			$stmt1 = $conn->prepare($sql1);
			$stmt1->bindParam(':id', $code, PDO::PARAM_INT);
			$stmt1->execute();

			// Borrar asignaciones de clases (tabla intermedia grupo_clase)
			$sql2 = "DELETE FROM grupo_clase WHERE grupo_id = :id";
			$stmt2 = $conn->prepare($sql2);
			$stmt2->bindParam(':id', $code, PDO::PARAM_INT);
			$stmt2->execute();

			// Finalmente borrar el grupo
			$sql3 = "DELETE FROM grupos WHERE id = :id";
			$stmt3 = $conn->prepare($sql3);
			$stmt3->bindParam(':id', $code, PDO::PARAM_INT);
			$stmt3->execute();

			$conn->commit();
			return true;
		} catch (Exception $e) {
			$conn->rollBack();
			return false;
		}
	}

	public function update_group_model($data)
	{
		$query = self::connect()->prepare("UPDATE grupos SET Nombre=:Nombre,Recompensa=:Recompensa,categoria_id=:categoria_id WHERE id=:id");
		$query->bindParam(":Nombre", $data['Nombre']);
		$query->bindParam(":Recompensa", $data['Recompensa']);
		$query->bindParam(":categoria_id", $data['categoria_id']);
		$query->bindParam(":id", $data['id']);
		$query->execute();
		return $query;
	}

	public function data_group_model($data)
	{
		if ($data['Tipo'] == "Count") {
			$query = self::connect()->prepare("SELECT id FROM grupos");
		} elseif ($data['Tipo'] == "Only") {
			$query = self::connect()->prepare("SELECT * FROM grupos WHERE id=:Id");
			$query->bindParam(":Id", $data['Id']);
		}
		$query->execute();
		return $query;
	}

	public function get_students_by_dont_group_model()
	{
		$query = "SELECT e.Codigo, e.Nombres, e.Apellidos, e.Email FROM estudiante e 
		WHERE NOT EXISTS (
		 SELECT 1 FROM estudiante_grupo eg 
		 WHERE eg.codigo COLLATE utf8mb3_spanish_ci = e.Codigo 
		 ) ORDER BY e.Apellidos, e.Nombres;";
		return self::execute_single_query($query);
	}

	public function get_students_by_group_model($id)
	{
		$query = self::connect()->prepare("SELECT e.Codigo, e.Nombres, e.Apellidos, e.Email 
            FROM estudiante e 
            INNER JOIN estudiante_grupo eg 
                ON eg.codigo COLLATE utf8mb3_spanish_ci = e.Codigo 
            WHERE eg.grupo_id = :id
            ORDER BY e.Apellidos, e.Nombres;");
		$query->bindParam(":id", $id);
		$query->execute();

		return $query;
	}

	public function update_student_group_model($grupo_id, $students)
	{
		if (empty($students) || !is_array($students)) {
			return false;
		}

		$grupo_id = (int)$grupo_id;

		$conn = self::connect();

		try {
			$conn->beginTransaction();

			$sql = "INSERT INTO estudiante_grupo (grupo_id, codigo) VALUES (:grupo_id, :codigo)";
			$stmt = $conn->prepare($sql);

			foreach ($students as $codigo) {
				$codigo = trim($codigo); // por seguridad extra

				$stmt->bindParam(':grupo_id', $grupo_id, PDO::PARAM_INT);
				$stmt->bindParam(':codigo', $codigo, PDO::PARAM_STR);
				$stmt->execute();
			}

			$conn->commit();
			return true;
		} catch (Exception $e) {
			$conn->rollBack();
			return false;
		}
	}
}
