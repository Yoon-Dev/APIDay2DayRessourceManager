<?php
require '../utils/con.php';
require '../class/ManagerRessource.php';

$manager = new ManagerRessource($conn);

$nom = $_GET['nom'];
$url = $_GET['url'];

var_dump($_GET);
$manager->OnlineAdd($nom, $url);