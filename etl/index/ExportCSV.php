<?php
include_once 'Sql.php';

class ExportCSV
{

    private $sql;

    public function __construct()
    {
        $this->sql = Sql::getInstance();
        $this->sql->selectDB('etl');
    }

    public function export()
    {
        file_put_contents('jobs_export.csv', '');
        $csv_export = '';

        $fetchOneRow = $this->sql->fetch("SELECT * FROM jobs");

        while (($item = current($fetchOneRow)) !== FALSE) {
            $csv_export .= key($fetchOneRow) . ',';
            next($fetchOneRow);
        }

        $csv_export .= '';

        $fetchAll = $this->sql->fetchAll("SELECT * FROM jobs");
        $allRowSize = count($fetchAll);

        for ($i = 0; $i < $allRowSize; $i++) {
            while (($item = current($fetchAll[$i])) !== FALSE) {
                $csv_export .= '"' . $item . '",';
                next($fetchAll[$i]);
            }
            $csv_export .= '';
        }

        file_put_contents('jobs_export.csv', $csv_export);
    }
}
