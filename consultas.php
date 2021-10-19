<?php

$searchData = $_GET["buscar"];
//	$searchData = "Olympics Games";
$servername = "localhost";
$operador = "";
$activadoPATRON = false;
$cadena = "";
$sqlDatos = "SELECT * FROM tablafrecuencias2 WHERE ";
$cont = 0;
//funcion para crear la sentencia 
function CrearQuery($palabra, $operador){
	$GLOBALS['sqlDatos'] = $GLOBALS['sqlDatos']."termino like '%".$palabra."%'";
};
// Create connection
$conn = new mysqli($servername, "root", "", "practica");
// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
//dividimos la cadena por espacios
$palabras = explode(" ", $searchData);
    //en este caso no existe y buscamos si tiene alguna palabra reservada
	foreach ($palabras as $clave => $valor) {
		if ($activadoPATRON) {
			if ($valor == ")") {
				$activadoPATRON = false;
			} else{
				CrearQuery($valor,$operador);
			}	
		} elseif ($valor == "and" || $valor == "AND") {
			$operador = $valor;
			$GLOBALS['sqlDatos'] = $GLOBALS['sqlDatos']." AND ";
		} elseif ($valor == "not" || $valor == "NOT") { 		
				$GLOBALS['sqlDatos'] = $GLOBALS['sqlDatos']." NOT ";
				$operador = $operador. " ".$valor;
		} elseif ($valor == "PATRON(") {
			$activadoPATRON = true;
		} 
		else{
			if ($valor == "or" || $valor == "OR") {	
				$operador = $valor;
				$GLOBALS['sqlDatos'] = $GLOBALS['sqlDatos']." OR ";
			} else { 
				if ($operador != "") {
					CrearQuery($valor,$operador);
				} else{
					if ($clave > 0 ) {
						$GLOBALS['sqlDatos'] = $GLOBALS['sqlDatos']." OR ";
					}
					CrearQuery($valor," OR ");					
				}
				$operador = "";
			}
		}
	};
//manipulo la info que me regresa la consulta sql, recorriendo las filas y las columnas para sacar la frecuencia invertida de cada una de las palabras de la consulta y lo almaceno en otro arreglo con key = nombre de la tabla

$resultQuestion = $conn->query($sqlDatos);
while ($row = $resultQuestion->fetch_assoc()) {
	$cont = 0;
	$log = log(($resultQuestion->field_count-4)/$row['documentos']);
	foreach ($row as $key => $value) {
		$cont += 1;
		if ($cont >= 5) $frecuenciaConsulta[$key][] = $value*$log;
	}
}
//aquí se hace la suma total de la frecuencia y se almacena en otro arreglo key = nombre de la columna. 
foreach ($frecuenciaConsulta as $NomTabla => $Valores) {
	$frecuenciatotal[$NomTabla] = 0 ;
	foreach ($Valores as $key => $value) {
		$frecuenciatotal[$NomTabla] += $value;
	}
}
//ordenamos el arreglo por el valor
arsort($frecuenciatotal);
//recorremos el nuevo arreglo para saber que texto se tiene que devolver
$sqlDescripcion = " SELECT * from descripciones where";
foreach ($frecuenciatotal as $key => $value) {
	if ($value != 0) {
		$sqlDescripcionArray[] = " NomDoc like '".$key."' and termino like '%".$searchData."%'";
	} 
}
$sqlDescripcion = $sqlDescripcion.implode(' or ', $sqlDescripcionArray).";";
$result = $conn->query($sqlDescripcion);
if (!$result || $result ->num_rows == 0 ) {
	echo "No hay resultados";
} else if ($result ->num_rows > 0) {
	while($row = $result->fetch_assoc()) {
		foreach ($frecuenciatotal as $key => $value) {
			if($key == $row["NomDoc"]){
				echo "Archivo: -".$row["NomDoc"]. ".txt - Descripción: ".$row["descripcion"]." - Frecuencia: "
				.$frecuenciatotal[$row["NomDoc"]];
				echo "|http://localhost/search_retrieve/Indizacion-y-busqueda/download.php?path=archivos/".$row["NomDoc"].".txt";
				echo " <br>";
			}
		}					
	}
}  
$conn->close();
?>