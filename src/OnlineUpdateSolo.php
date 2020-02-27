<?php
require '../utils/con.php';
require '../class/ManagerRessource.php';

$manager = new ManagerRessource($conn);

$type = $_GET['type'];
$value = $_GET['value'];
$id = (int)$_GET['id'];

echo $manager->OnlineUpdateSolo($type, $value, $id);
