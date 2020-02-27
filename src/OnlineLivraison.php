<?php
require '../utils/con.php';
require '../class/ManagerRessource.php';

$manager = new ManagerRessource($conn);

$id = (int)$_GET['id'];
$type = $_GET['type'];

echo $manager->OnlineAddLivraison($type, $id);