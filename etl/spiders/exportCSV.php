<?php
include_once 'Sql.php';
$sql = Sql::getInstance();
$sql->selectDB('etl');

$csv_filename = 'jobs_export_'.date('Y-m-d').'.csv';
$csv_export = '';

$fetchOneRow = $sql->fetch("SELECT * FROM jobs");

while ( ($item = current($fetchOneRow)) !== FALSE ) {
	$csv_export.= key($fetchOneRow).',';
    next($fetchOneRow);
}

$csv_export.= '';

$fetchAll = $sql->fetchAll("SELECT * FROM jobs");
$allRowSize = count($fetchAll);

for($i=0;$i<$allRowSize;$i++) {
	while ( ($item = current($fetchAll[$i])) !== FALSE ) {
		$csv_export.= '"'.$item.'",';
		next($fetchAll[$i]);
	}
	$csv_export.= '';
}


header("Content-type: text/x-csv");
header("Content-Disposition: attachment; filename=".$csv_filename."");
echo($csv_export);
?>