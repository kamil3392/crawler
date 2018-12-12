<?php
 include_once 'Sql.php';
 include_once 'ExportCSV.php';
 include_once 'Load.php';
 include_once 'Extract.php';
 include_once 'Transform.php';
 include_once 'Data.php';
 
 $exportToCSV = new ExportCSV();
 $load = new Load();
 $extract = new Extract();
 $transform = new Transform('../spiders/data.json');
 $data = new Data();
 
 if(array_key_exists('export',$_POST)){
   $exportToCSV->export();
 }
 
 if(array_key_exists('load',$_POST)){
   $load->load();
 }
 
 if(array_key_exists('extract',$_POST)){
   $extract->extract();
 }
 
 if(array_key_exists('transform',$_POST)){
   $transform->transform();
 }
 
 if(array_key_exists('etl',$_POST)){
   $extract->extract();
   $transform->transform();
   $load->load();
 }
 
 $rows = $data->getAll();
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
  <input class="btn btn-success" type="submit" name="etl" id="etl" value="PROCESS ETL" />
  <input class="btn btn-primary" type="submit" name="extract" id="extract" value="EXTRACT" />
  <input class="btn btn-primary" type="submit" name="transform" id="transform" value="TRANSFORM" />
  <input class="btn btn-primary" type="submit" name="load" id="load" value="LOAD" />
  <input class="btn btn-warning pull-right" type="submit" name="export" id="export" value="EXPORT TO CSV" />
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
 <?php for($i=0;$i<count($rows);$i++) { ?>
 <tr>
 
 <?php while ( ($item = current($rows[$i])) !== FALSE ) { ?>
 <td>
   <?php echo $item; ?>
 </td>
 <?php next($rows[$i]); }?>
 
</tr>
 <?php } ?>   
</tbody>
</table>

</body>
</html>