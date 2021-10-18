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
//echo $sqlDatos;
$resultQuestion = $conn->query($sqlDatos);
//var_dump($resultQuestion);
while ($row = $resultQuestion->fetch_assoc()) {
	$cont = 0;
	$log = log(($resultQuestion->field_count-4)/$row['documentos']);
	foreach ($row as $key => $value) {
		$cont += 1;
		if ($cont >= 5) $frecuenciaConsulta[$key][] = $value*$log;
	}
}
//aquí hago la suma total de la frecuencia y lo almaceno en otro arreglo key = nombre de la columna. 
foreach ($frecuenciaConsulta as $NomTabla => $Valores) {
	$frecuenciatotal[$NomTabla] = 0 ;
	foreach ($Valores as $key => $value) {
		$frecuenciatotal[$NomTabla] += $value;
	}
}
//ordenamos el arreglo por el valor
arsort($frecuenciatotal);
//print_r($frecuenciatotal);
//recorremos el nuevo arreglo para saber que texto se tiene que devolver
$sqlDescripcion = " SELECT * from descripciones where";
foreach ($frecuenciatotal as $key => $value) {
	if ($value != 0) {
		$sqlDescripcionArray[] = " NomDoc like '".$key."' and termino like '%".$searchData."%'";
	} 
}
//echo $sqlDescripcion.' <br/>';
$sqlDescripcion = $sqlDescripcion.implode(' or ', $sqlDescripcionArray).";";
/*$sqlDescripcion = "SELECT * from descripciones WHERE termino like 'olympics' and NomDoc like 'archivo4'
union SELECT * from descripciones WHERE termino like 'olympics' and NomDoc like 'archivo6'
union SELECT * from descripciones WHERE termino like 'olympics' and NomDoc like 'archivo1'
union SELECT * from descripciones WHERE termino like 'olympics' and NomDoc like 'archivo5'
union SELECT * from descripciones WHERE termino like 'games' and NomDoc like 'archivo2'
union SELECT * from descripciones WHERE termino like 'olympics' and NomDoc like 'archivo3'
union SELECT * from descripciones WHERE termino like 'olympics' and NomDoc like 'archivo8'
union SELECT * from descripciones WHERE termino like 'games' and NomDoc like 'archivo9'
union SELECT * from descripciones WHERE termino like 'olympics' and NomDoc like 'archivo7'
union SELECT * from descripciones WHERE termino like 'games' and NomDoc like 'archivo10'";*/
//echo $sqlDescripcion.' <br/>';
$result = $conn->query($sqlDescripcion);
//var_dump($result);
if (!$result || $result ->num_rows == 0 ) {
	echo "No hay resultados";
} else if ($result ->num_rows > 0) {
	while($row = $result->fetch_assoc()) {
		foreach ($frecuenciatotal as $key => $value) {
			if($key == $row["NomDoc"]){
				echo "Archivo: ".$row["NomDoc"]. ".txt - Descripción: ".$row["descripcion"]." - Frecuencia: ".$frecuenciatotal[$row["NomDoc"]];
				echo " <br>";
			}
		}					
	}
}  
$conn->close();
?>