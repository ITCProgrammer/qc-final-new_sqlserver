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

// SQL Server: database db_qc (migrated from MySQL)
$db_laborat_host = "10.0.0.221";
$db_laborat_option = array(
    "Database" => "db_laborat",
    "UID" => "sa",
    "PWD" => "Ind@taichen2024",
    "CharacterSet" => "UTF-8"
);
$con_db_laborat_sqlsrv = sqlsrv_connect($db_laborat_host, $db_laborat_option);



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

// $con=mysqli_connect("10.0.0.10","dit","4dm1n","db_qc");
$cona=mysqli_connect("10.0.0.10","dit","4dm1n","db_adm");
// $con=mysqli_connect("localhost","root","","db_qc");
$condye=mysqli_connect("10.0.0.10","dit","4dm1n","db_dying");
$conlab=mysqli_connect("10.0.0.10","dit","4dm1n","db_laborat");
if (mysqli_connect_errno()) {
printf("Connect failed: %s\n", mysqli_connect_error());
exit();
} 
?>