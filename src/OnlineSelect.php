<?php
require '../utils/con.php';
require '../class/ManagerRessource.php';

$manager = new ManagerRessource($conn);

echo $manager->Select("online");