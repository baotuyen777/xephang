<?php
$servername = "42.112.18.106";
$username = "root";
$password = "6AFAgJc0n2106";
$dbname = "bhyt";

// Create connection
$conn = new mysqli($servername, $username, $password,$dbname);

//$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
// set the PDO error mode to exception
//$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

/*try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8mb4", $username, $password);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    include 'excel_reader.php';
	$excel = new PhpExcelReader;      // creates object instance of the class
	//$excel->setOutputEncoding('UTF-8');
	$excel->read('cdha_ud.xls');
	$count = 0;
	$sql = "";
	$count = 0;
	foreach($excel->sheets[0]['cells'] as $cell){

		//$sql = 'INSERT INTO THUOC (ma_thuoc, ten_thuoc, ma_tuongduong, so_dang_ky, duong_dung, ham_luong) VALUES ("'.$cell[1].'", "'.$cell[3].'", "'.$cell[2].'", "'.$cell[4].'", "'.$cell[5].'", "'.$cell[6].'");';
		//echo $sql;echo "<br>";
		//$conn->query("SET character_set_results = 'utf8', character_set_client = 'utf8', character_set_connection = 'utf8', character_set_database = 'utf8', character_set_server = 'utf8'");
		//$conn->query($sql);

		//$sql = "INSERT INTO MyGuests (ma_thuoc, ten_thuoc, ma_tuongduong, so_dang_ky, duong_dung, ham_luong) VALUES (?, ?, ?, ?, ?, ?)";

		$sql = 'INSERT INTO CDHA (ma_cdha, ma_tuongduong, ten_cdha) VALUES ("'.$cell[1].'", "'.$cell[3].'", "'.$cell[2].'");';
		$conn->query("SET character_set_results = 'utf8', character_set_client = 'utf8', character_set_connection = 'utf8', character_set_database = 'utf8', character_set_server = 'utf8'");
	    // use exec() because no results are returned
	    $conn->exec($sql);

		$count++;
		//echo $count;echo "<br>";
	}

}
catch(PDOException $e){
    echo $sql . "<br>" . $e->getMessage();
}
*/
include 'excel_reader.php';
$excel = new PhpExcelReader;      // creates object instance of the class
$excel->setOutputEncoding('UTF-8');
$excel->read('xn_ud.xls',true,"UTF-8");
$count = 0;
$sql = "";
foreach($excel->sheets[0]['cells'] as $cell){

	$sql = 'INSERT INTO XN (ma_xn, ma_tuongduong, ten_xn) VALUES ("'.$cell[1].'", "'.$cell[2].'", "'.$cell[3].'");';
	//echo $sql;echo "<br>";
	$conn->query("SET character_set_results = 'utf8', character_set_client = 'utf8', character_set_connection = 'utf8', character_set_database = 'utf8', character_set_server = 'utf8'");
	$conn->query($sql);
	$count++;
	echo $count;echo "<br>";
}

/*$conn->query("SET character_set_results = 'utf8', character_set_client = 'utf8', character_set_connection = 'utf8', character_set_database = 'utf8', character_set_server = 'utf8'");
if (mysqli_multi_query($conn, $sql)) {
    echo "New records created successfully";
} else {
    echo "Error: " . $sql . "<br>" . mysqli_error($conn);
}*/

/*$sql_query = "SELECT * FROM KHAM";

$result = $conn->query($sql_query);

if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        //echo "id: " . $row["ma_xn"]. " - Name: " . $row["ma_tuongduong"]. " " . $row["ten_xn"]. "<br>";
    }
} 
*/
$conn->close();