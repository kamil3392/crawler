<?php
include_once 'Load.php';
include_once 'Extract.php';
include_once 'Transform.php';

$load = new Load();
$extract = new Extract();
$transform = new Transform('data.json');

$extract->extract();
$transform->transform();
$load->load();