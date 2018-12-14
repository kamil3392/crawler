<?php

include_once 'Data.php';

$data = new Data();

$rows = $data->getAll();
$rowsCount = count($rows);
?>
<html>
<head>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <!-- jQuery library -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <!-- Latest compiled JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>


</head>

<body>

<div>
    <form method="post">
        <input class="btn btn-success" type="button" name="etl" id="etl" value="PROCESS ETL"/>
        <input class="btn btn-primary" type="button" name="extract" id="extract" value="EXTRACT"/>
        <input class="btn btn-primary" type="button" name="transform" id="transform" value="TRANSFORM"/>
        <input class="btn btn-primary" type="button" name="load" id="load" value="LOAD"/>
        <input class="btn btn-warning pull-right" type="button" name="export" id="export" value="EXPORT TO CSV"/>
    </form>
</div>

<table class="table">
    <thead>
    <tr>
        <th>Id</th>
        <th>Title</th>
        <th>Location</th>
        <th>Price</th>
        <th>Company name</th>
        <th>Kind</th>
        <th>Number</th>
        <th>Position</th>
        <th>Position level</th>
    </tr>
    </thead>
    <tbody>
    <?php for ($i = 0; $i < $rowsCount; $i++) { ?>
        <tr>

            <?php while (($item = current($rows[$i])) !== FALSE) { ?>
                <td>
                    <?php echo $item; ?>
                </td>
                <?php next($rows[$i]);
            } ?>

        </tr>
    <?php } ?>
    </tbody>
</table>

</body>

<script type="text/javascript">
    $("#transform").attr("disabled", true);
    $("#load").attr("disabled", true);
    $("#extract").click(function () {
        $("#transform").attr("disabled", true);
        $("#load").attr("disabled", true);
        $("#etl").attr("disabled", true);
        $.ajax({
            cache: false,
            url: 'extract_exec.php',
            type: 'get',
            success: function () {
                $("#transform").attr("disabled", false);
                $("#etl").attr("disabled", false);
            }
        });
    });


    $("#transform").click(function () {
        $("#extract").attr("disabled", true);
        $("#etl").attr("disabled", true);
        $.ajax({
            cache: false,
            url: 'transform_exec.php',
            type: 'get',
            success: function () {
                $("#load").attr("disabled", false);
                $("#extract").attr("disabled", false);
                $("#etl").attr("disabled", false);
            }
        });
    });


    $("#load").click(function () {
        $("#extract").attr("disabled", true);
        $("#transform").attr("disabled", true);
        $("#etl").attr("disabled", true);
        $.ajax({
            cache: false,
            url: 'load_exec.php',
            type: 'get',
            success: function () {
                $("#extract").attr("disabled", false);
                $("#etl").attr("disabled", false);
            }
        });
    });


    $("#etl").click(function () {
        $("#extract").attr("disabled", true);
        $("#transform").attr("disabled", true);
        $("#load").attr("disabled", true);
        $.ajax({
            cache: false,
            url: 'etl_exec.php',
            type: 'get',
            success: function () {

                $("#extract").attr("disabled", false);
            }
        });
    });

    $("#export").click(function () {
        $("#extract").attr("disabled", true);
        $("#transform").attr("disabled", true);
        $("#load").attr("disabled", true);
        $("#etl").attr("disabled", true);
        $.ajax({
            cache: false,
            url: 'export_exec.php',
            type: 'get',
            success: function () {
                window.location.href = 'jobs_export.csv';
                $("#extract").attr("disabled", false);
                $("#transform").attr("disabled", false);
                $("#load").attr("disabled", false);
                $("#etl").attr("disabled", false);
            }
        });
    });

</script>
</html>