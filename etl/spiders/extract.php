<?php

//include_once '../Sql.php';
//
//$sql = Sql::getInstance();
//
//$sql->selectDB('etl');
//
////sample:
//var_dump($sql->fetchAll('Select * from jobs'));
//die;
//proces extract
$pyscript = 'C:\Users\jakub.koziera\Documents\Projects\Scrapy\crawler\etl\spiders\works.py';
$python = 'C:\Users\jakub.koziera\AppData\Local\Continuum\anaconda3\python.exe';

$cmd = "$python $pyscript";
exec($cmd, $output);
//koniec procesu extract
