
<?php 
require 'conexion.php';


$sql = "SELECT * FROM medicos";

$query = $conexion->prepare($sql);

$query -> execute();

$results = $query -> fetchAll(PDO::FETCH_OBJ);


print_r($results);