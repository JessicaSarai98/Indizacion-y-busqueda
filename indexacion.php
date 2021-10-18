<?php
$directorio = './archivos/'; //Declaramos un  variable con la ruta donde guardaremos los archivos
if(!file_exists($directorio)) mkdir($directorio);// Si no existe la carpeta de destino la creamos
$dir=opendir($directorio); //Abrimos el directorio de destino

//Como el elemento es un arreglo utilizamos foreach para extraer todos los valores
foreach($_FILES["archivos"]['tmp_name'] as $key => $tmp_name)
{
  //Validamos que el archivo exista
  if($_FILES["archivos"]["name"][$key]) {
    $filename = $_FILES["archivos"]["name"][$key]; //Obtenemos el nombre original del archivo
    $source = $_FILES["archivos"]["tmp_name"][$key]; //Obtenemos un nombre temporal del archivo  
    $target_path = $directorio.'/'.$filename; //Indicamos la ruta de destino, así como el nombre del archivo      
    //Movemos y validamos que el archivo se haya cargado correctamente. El primer campo es el origen y el segundo el destino
    if(move_uploaded_file($source, $target_path)) { 
      echo "El archivo $filename se ha almacenado en forma exitosa.<br>";
      } else {  
      echo "Ha ocurrido un error, por favor inténtelo de nuevo.<br>";
    }
  }
};

$cont = 0;
$elemento = scandir($directorio);
var_dump($elemento);
//Se leen todos los archivos que tiene la carpeta archivos
for ($i=0; $i < count($elemento)  ; $i++) { 
  if( $elemento[$i] != "." && $elemento[$i] != ".."){
    $nombresArchivos[$cont] = $elemento[$i];
    $GLOBALS['cont'] = $GLOBALS['cont'] + 1;
  }
}
closedir($dir); //Cerramos el directorio de destino

//codigo de la indezación 
function buildInvertedIndex($filenames){
  $invertedIndex = []; 
  foreach($filenames as $filename) {
    $fln = "./archivos/".$filename;
    //var_dump($fln);
    $data = file_get_contents( $fln); 
    //obtenemos una descripcion del texto de 40 caracteres 
    $descripcion = str_split($data, 40);
    //inicializamos el contador de las frecuencias
    $contadorFrecuencias = 0;
    if($data === false) die('Unable to read file: ' . $filename); 
    preg_match_all('/(\w+)/', $data, $matches, PREG_SET_ORDER); 
    foreach($matches as $match){
      $word = strtolower($match[0]); 
      if(!array_key_exists($word, $invertedIndex)) {
        //inicializamos los arreglos sino existe el termino indice en el arreglo
        $invertedIndex[$word] = [];
        $invertedIndex[$word]["nombresArchivos"] = [];
      } 
      if(!in_array($filename, $invertedIndex[$word], true)){
        if (!in_array($filename, $invertedIndex[$word]["nombresArchivos"], true)) {
          //si el nombre del archivo no se escuentra en el arreglo [nombresArchivos] lo agregamos junto con las demás descripciones
          $invertedIndex[$word]["nombresArchivos"][] = $filename; 
          $invertedIndex[$word][$filename]["frecuencia"] = 1;
          $invertedIndex[$word][$filename]["fraseDescrip"] = $descripcion[0];
        }  else{
          //pero si ya existe, aumentamos el contador y lo actualizamos en el arreglo de las frecuencias
          $contadorFrecuencias += 1;
          $invertedIndex[$word][$filename]["frecuencia"] = $invertedIndex[$word][$filename]["frecuencia"] + 1;  
        }           
      } 
    }
  } 
  return $invertedIndex;
};
 
$invertedIndex = buildInvertedIndex($nombresArchivos);

//aquí empieza la conexión de la base de datos, y el llenado de las tablas
$servername = "localhost";
$operador = "";
$conn = new mysqli($servername, "root", "", "practica");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);
//verifica que por cada nombre de los archivos exista una columna llamada de la misma manera en la tabla tablafrecuencias2, sino existe la crea
foreach ($nombresArchivos as $key => $value) {
  $columnas = explode(".", $value); 
  $result = $conn->query("SHOW COLUMNS FROM tablafrecuencias2 like  '".$columnas[0]."'"); 
  if (!$result || $result ->num_rows == 0 ) {
    $conn->query("ALTER TABLE tablafrecuencias2 ADD ".$columnas[0]." int");
    $nuevasTablas[] = $columnas[0];
  }
}
//Aquí se hace el llenado de las columnas correspondientes, se recorre el arreglo $nombresArchivos
$sqlInsertFrec = "insert into tablafrecuencias2 (termino, documentos, frecuenciaTotal, ";
$sqlInsertDesc = "insert into descripciones (termino, NomDoc, descripcion) values  ";
$sqlUpdate = "update tablafrecuencias2 set ";
//este recorrido es para poner en la consulta el nombre de las columnas/archivos existentes
foreach ($nombresArchivos as $key => $value) {
  $columnas = explode(".", $value); 
  $frecIndividual[] = 0;
  if ($key == count($nombresArchivos)-1) $sqlInsertFrec = $sqlInsertFrec.$columnas[0]." ) values ";
    else  $sqlInsertFrec = $sqlInsertFrec.$columnas[0].", ";
}
//aquí se estan agregando los valores del termino, #doc donde aparece, frecuencia total y las separadas
foreach ($invertedIndex as $key => $value) {
//************* lo primero es preguntar si existe ese termino en la base de datos
  $result = $conn->query("SELECT * FROM tablafrecuencias2 WHERE termino = '".$key."'");
  if (!$result || $result ->num_rows == 0 ) {
    //no existe, entonces hace el insert
    $sqlInsertFrec = $sqlInsertFrec."('".$key."', ".count($value['nombresArchivos']);
    $FrecTotal = 0; 
    foreach ($nombresArchivos as $clave => $archivo) {
      $frecIndividual[$clave] = 0;
    }
    foreach ($value['nombresArchivos'] as $key2 => $archivo) {
      $FrecTotal += $value[$archivo]['frecuencia']; 
      $columnas = explode(".", $archivo);
      foreach ($nombresArchivos as $key3 => $value3) {
        $columnas2 = explode(".", $value3); 
        if ($columnas[0] == $columnas2[0]) $frecIndividual[$key3] = $value[$archivo]['frecuencia'];
      }
      $filasDescripciones[] = "('".$key."', '".$columnas[0]."', '".$value[$archivo]['fraseDescrip']."')";
    }
    $sqlInsertFrec = $sqlInsertFrec.", ".$FrecTotal.", ".implode(', ', $frecIndividual)."),";
    $existe = 0;
  } else{
    //si existe, actualiza los datos de la tabla
    $existe = 1;
    $row = $result->fetch_assoc();
    $sqlUpdate = $sqlUpdate." documentos = '".count($value['nombresArchivos'])."', ";
    //echo "id: " . $row["id"]. " - Name: " . $row["product_name"]. " - category: " . $row["category"]. " - quantity_per_unit: " .$row["quantity_per_unit"]. " <br>";
    if (isset($nuevasTablas)) {
      foreach ($nuevasTablas as $key2 => $tablas) {
        $DatosUpdate[] = $tablas." = '".$value[$tablas]['frecuencia']."'";
      }
      $sqlUpdate = $sqlUpdate.implode(', ', $DatosUpdate).";";
    }
  }  
}
//$response = null;
//$response2 = null;
if ($existe == 0) {
  $sqlInsertDesc = $sqlInsertDesc.implode(', ', $filasDescripciones).";";
  $sqlInsertFrec = substr($sqlInsertFrec, 0, -1).";"; 
  var_dump($sqlInsertFrec);
  var_dump($sqlInsertDesc);
  $response = $conn->query($sqlInsertFrec);
  $response2 = $conn->query($sqlInsertDesc);
} else {
  var_dump($sqlUpdate);
  $response = $conn->query($sqlUpdate);
}
//var_dump($response);
//var_dump($response2);
$conn->close();
?>
