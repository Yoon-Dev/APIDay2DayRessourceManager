<?php
require '../utils/con.php';
require '../class/ManagerRessource.php';

$manager = new ManagerRessource($conn);

$nom = $_GET['nom'];

$nom = explode ( "," , $nom );

$manager->RealAdd($nom);
