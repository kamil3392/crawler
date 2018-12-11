<?php
include_once 'Transform.php';

//header('Content-Type: text/plain');
//header('Content-Type: text/html');

$transform = new Transform('../spiders/data.json');

$transform->transform();