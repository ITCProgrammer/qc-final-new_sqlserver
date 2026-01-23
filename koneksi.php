<?php
//fungsi untuk print rapih
if (!function_exists('p')) {
  function p($array,$exit=false){
    echo "<pre>";
    if(is_array($array) || is_object($array)){
      print_r($array);
    }
    else{
      if($array=="post"){
        print_r($_POST);
      }else if($array=="get"){
        print_r($_GET);
      }else {
        echo $array;
      }
    }
    echo "</pre>";
    if($exit){
      exit;
    }
  }
}
?>
<?php
date_default_timezone_set('Asia/Jakarta');
$host="10.0.0.174";
$username="ditprogram";
$password="Xou@RUnivV!6";
$db_name="TM";
$connInfo = array( "Database"=>$db_name, "UID"=>$username, "PWD"=>$password);
// $conn     = sqlsrv_connect( $host, $connInfo);

// SQL Server: database db_qc (migrated from MySQL)
$db_qc_host = "10.0.0.221";
$db_qc_option = array(
    "Database" => "db_qc",
    "UID" => "sa",
    "PWD" => "Ind@taichen2024",
    "CharacterSet" => "UTF-8"
);
$con_db_qc_sqlsrv = sqlsrv_connect($db_qc_host, $db_qc_option);

// SQL Server: database db_laborat (migrated from MySQL)
$db_laborat_host = "10.0.0.221";
$db_laborat_option = array(
    "Database" => "db_laborat",
    "UID" => "sa",
    "PWD" => "Ind@taichen2024",
    "CharacterSet" => "UTF-8"
);
$con_db_laborat_sqlsrv = sqlsrv_connect($db_laborat_host, $db_laborat_option);

// SQL Server: database db_dying (migrated from MySQL)
$db_dying_host = "10.0.0.221";
$db_dying_option = array(
    "Database" => "db_dying",
    "UID" => "sa",
    "PWD" => "Ind@taichen2024",
    "CharacterSet" => "UTF-8"
);
$con_db_dying_sqlsrv = sqlsrv_connect($db_dying_host, $db_dying_option);

// SQL Server: database db_adm (migrated from MySQL)
$db_adm_host = "10.0.0.221";
$db_adm_option = array(
    "Database" => "db_adm",
    "UID" => "sa",
    "PWD" => "Ind@taichen2024",
    "CharacterSet" => "UTF-8"
);
$con_db_adm_sqlsrv = sqlsrv_connect($db_adm_host, $db_adm_option);

// SQL Server: database invqc (migrated from MySQL)
$invqc_host = "10.0.0.221";
$invqc_option = array(
    "Database" => "invqc",
    "UID" => "sa",
    "PWD" => "Ind@taichen2024",
    "CharacterSet" => "UTF-8"
);
$con_invqc_sqlsrv = sqlsrv_connect($invqc_host, $invqc_option);

$hostname="10.0.0.21";
$database = "NOWPRD";
$user = "db2admin";
$passworddb2 = "Sunkam@24809";
$port="25000";
$conn_string = "DRIVER={IBM ODBC DB2 DRIVER}; HOSTNAME=$hostname; PORT=$port; PROTOCOL=TCPIP; UID=$user; PWD=$passworddb2; DATABASE=$database;";
$conn1 = db2_connect($conn_string,'', '');

if($conn1) {
}
else{
    exit("DB2 Connection failed");
}

// $con=mysqli_connect("localhost","root","password","db_qc");
$cona=mysqli_connect("10.0.0.10","dit","4dm1n","db_adm");
// $con=mysqli_connect("localhost","root","","db_qc");
$condye=mysqli_connect("10.0.0.10","dit","4dm1n","db_dying");
$conlab=mysqli_connect("10.0.0.10","dit","4dm1n","db_laborat");
if (mysqli_connect_errno()) {
printf("Connect failed: %s\n", mysqli_connect_error());
exit();
} 
?>