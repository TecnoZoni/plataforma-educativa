<?php if ($_SESSION['userType'] === "Administrador"): ?>
    <div class="container-fluid">
        <div class="page-header">
            <h1 class="text-titles"><i class="zmdi zmdi-accounts-outline zmdi-hc-fw"></i> Grupos <small>(Estudiantes)</small></h1>
        </div>
        <p class="lead">
            En esta sección puede ver el listado de todos los grupos registrados en el sistema, puede actualizar datos o eliminar un grupo cuando lo desee.
        </p>
    </div>

    <div class="container-fluid">
        <ul class="breadcrumb breadcrumb-tabs">
            <li>
                <a href="<?php echo SERVERURL; ?>group/" class="btn btn-info">
                    <i class="zmdi zmdi-plus"></i> Nuevo
                </a>
            </li>
            <li class="active">
                <a href="<?php echo SERVERURL; ?>grouplist/" class="btn btn-success">
                    <i class="zmdi zmdi-format-list-bulleted"></i> Lista
                </a>
            </li>
        </ul>
    </div>
    <?php
    require_once "./controllers/groupController.php";

    $insGroup = new groupController();

    if (isset($_POST['groupCode'])) {
        echo $insGroup->delete_group_controller($_POST['groupCode']);
    }
    ?>
    <div class="container-fluid">
        <div class="row">
            <div class="col-xs-12">
                <div class="panel panel-success">
                    <div class="panel-heading">
                        <h3 class="panel-title"><i class="zmdi zmdi-format-list-bulleted"></i> Lista de Grupos</h3>
                    </div>
                    <div class="panel-body">
                        <div class="table-responsive">
                            <?php
                            $page = explode("/", $_GET['views']);
                            echo $insGroup->pagination_group_controller($page[1], 10);
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php
else:
    $logout2 = new loginController();
    echo $logout2->login_session_force_destroy_controller();
endif;
?>