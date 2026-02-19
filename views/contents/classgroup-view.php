<?php if ($_SESSION['userType'] == "Administrador"): ?>
    <div class="container-fluid">
        <div class="page-header">
            <h1 class="text-titles"><i class="zmdi zmdi-assignment zmdi-hc-fw"></i> Asignar clase a grupos</h1>
        </div>
        <p class="lead">
            Selecciona los grupos donde habilitar esta clase.
        </p>
    </div>

    <?php
    require_once "./controllers/groupController.php";
    require_once "./controllers/videoController.php";

    $groupIns = new groupController();
    $videoIns = new videoController();

    // Procesar POST
    if (isset($_POST['groups']) && is_array($_POST['groups'])) {
        $code = explode("/", $_GET['views']);
        $class_id = $code[1] ?? null;
        if ($class_id) {
            echo $videoIns->set_class_group_controller($class_id, $_POST['groups']);
        }
    }

    $code = explode("/", $_GET['views']);
    $class_id = $code[1] ?? null;

    $data = $videoIns->data_video_controller("Only", $class_id);
    if ($data->rowCount() > 0):
        $clase = $data->fetch();

        $groups_data = $groupIns->get_groups_with_category_controller();
        $groups = $groups_data->fetchAll();

        $assigned_data = $videoIns->get_assigned_groups_to_class_controller($class_id);
        $assigned_groups = [];
        if ($assigned_data->rowCount() > 0) {
            while ($row = $assigned_data->fetch()) {
                $assigned_groups[] = $row['grupo_id'];
            }
        }
    ?>

        <p class="text-center">
            <a href="<?php echo SERVERURL; ?>classlist/" class="btn btn-info btn-raised btn-sm">
                <i class="zmdi zmdi-long-arrow-return"></i> Volver a lista de clases
            </a>
        </p>

        <div class="container-fluid">
            <div class="row">
                <div class="col-xs-12">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <h3 class="panel-title">
                                <i class="zmdi zmdi-video"></i>
                                Clase: <?php echo htmlspecialchars($clase['Nombre'] ?? $clase['nombre'] ?? 'Sin nombre'); ?>
                            </h3>
                        </div>
                        <div class="panel-body">
                            <form action="" method="POST" autocomplete="off">
                                <div class="table-responsive">
                                    <table class="table table-hover table-striped">
                                        <thead>
                                            <tr>
                                                <th width="50px">
                                                    <input type="checkbox" id="select-all">
                                                </th>
                                                <th>Categoría</th>
                                                <th>Grupo</th>
                                                <th>Recompensa</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($groups as $g): ?>
                                                <tr>
                                                    <td class="text-center">
                                                        <input type="checkbox"
                                                            name="groups[]"
                                                            value="<?php echo $g['id']; ?>"
                                                            class="group-check"
                                                            <?php echo in_array($g['id'], $assigned_groups) ? 'checked' : ''; ?>>
                                                    </td>
                                                    <td><strong><?php echo htmlspecialchars($g['CategoriaNombre']); ?></strong></td>
                                                    <td><?php echo htmlspecialchars($g['GrupoNombre']); ?></td>
                                                    <td><?php echo $g['Recompensa'] ?? '-'; ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>

                                <p class="text-center">
                                    <button type="submit" class="btn btn-success btn-raised btn-lg">
                                        <i class="zmdi zmdi-check"></i> Guardar asignación
                                    </button>
                                </p>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
            document.getElementById('select-all').addEventListener('change', function() {
                document.querySelectorAll('.group-check').forEach(cb => cb.checked = this.checked);
            });
        </script>

    <?php else: ?>
        <p class="lead text-center text-danger">Clase no encontrada.</p>
    <?php endif; ?>

<?php else: ?>
    <?php
    $logout2 = new loginController();
    echo $logout2->login_session_force_destroy_controller();
    ?>
<?php endif; ?>