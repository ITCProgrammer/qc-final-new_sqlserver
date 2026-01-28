<?php 
$nodemand_qn =  isset($_POST['nodemand']) ? $_POST['nodemand'] : '';
$nodemand_fl =  isset($_POST['nodemand_fl']) ? $_POST['nodemand_fl'] : ''
?>
<form  action="" method="POST">
  <div class="box box-info">
      <div class="box-header with-border">
        <h3 class="box-title">Testing Operan QN Ke FL</h3>
     
      </div>
<div class="box-body" style="background-color:#fff">
	<div class="form-group">
		
		<label for="exampleInputEmail1">No Demand QN</label>
		<input value="<?=$nodemand_qn?>" required type="text" name="nodemand" id="input1" class="form-control">
	</div>
	<div class="form-group">
		<label for="exampleInputPassword1">No Demand FL</label>
		<input value="<?=$nodemand_fl?>" required type="text" name="nodemand_fl" id="input2" class="form-control">
	</div>
	<div class="box-footer">
		<button type="submit" class="btn btn-primary" name="syncron">Preview</button>
	</div>	
</div>

</div>
</form>
	




<script>
  // Get references to the input elements
  var input1 = document.getElementById("input1");
  var input2 = document.getElementById("input2");

  // Add an event listener to input1 for the "input" event
  input1.addEventListener("input", function () {
    // Set the value of input2 equal to input1 whenever input1 changes
    input2.value = input1.value;
  });
</script>
<?php
include "koneksi.php"; 
$array_multiple = array();
if (isset($_POST['syncron'])) { ?>
	<?php 
		
		function demand_multiple($id_nokk,$nodemand) {
			global $con_db_qc_sqlsrv ; 
			$sql = "  SELECT sort_by, nodemand
				FROM db_qc.tbl_tq_nokk_demand
				WHERE id_nokk = ?
				ORDER BY sort_by
			";

			$stmt = sqlsrv_query($con_db_qc_sqlsrv, $sql, [$id_nokk]);
			if ($stmt === false) return [];

			$array = [];
			$array[1] = $nodemand;

			while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
				$array[(int)$row['sort_by']] = $row['nodemand'];
			}
		}
		
		function cek_demand($nodemand, $nodemandfl) {
			global $con_db_qc_sqlsrv;

			$sql = " SELECT TOP (1)
					a.id,
					b.id_nokk as id_nokk,
					a.nodemand as nodemand,
					b.nodemand as nodemand_right,
					a.no_item,
					a.no_hanger,
					c.id as id_tq_test,
					(
						SELECT MAX(d2.id)
						FROM db_qc.tbl_tq_first_lot d2
						WHERE d2.nodemand = ?
					) as id_fl
				FROM db_qc.tbl_tq_nokk a
				LEFT JOIN db_qc.tbl_tq_nokk_demand b ON a.id = b.id_nokk
				LEFT JOIN db_qc.tbl_tq_test c ON a.id = c.id_nokk
				WHERE a.nodemand = ? OR b.nodemand = ?
				ORDER BY a.id DESC, b.id DESC
			";
			$params = [$nodemandfl, $nodemand, $nodemand];
			return sqlsrv_query($con_db_qc_sqlsrv, $sql, $params);
		}
	
		$nodemand = $_POST['nodemand'];
		$nodemandfl = $_POST['nodemand_fl'];
		/*echo '<pre>';
			print_r($nodemandfl);
		echo '</pre>';
		*/
		$cek_demand = cek_demand($nodemand, $nodemandfl);
		$data   = sqlsrv_fetch_array($cek_demand);
		// echo '<pre>';
		// 	print_r($sql);
		// echo '</pre>';
		
		if ($data) {
				if ($data['id_nokk']) { // multiple exists					
					$multiple = demand_multiple($data['id_nokk'],$data['nodemand']);
					
					$array_multiple = $multiple ;
					
					
				} else {
					//echo 11 ; 
				}
		} else {
			//echo 2 ; 
		}
		
		/*
		echo '<pre>';
			print_r($data);
		echo '</pre>';
		*/
		
		
	?>
<?php }
?>
<?php ?>
<?php 
	if (count($array_multiple)>0) { ?>
	<div class="box-body" style="background-color:#fff">
	<?php 
	foreach ($array_multiple as $multiple) {
		echo '<div style="border:solid thin #ddd;float:left;margin-right:10px;padding:2px">';
		echo $multiple ;
		echo '</div>';
		echo '&nbsp;&nbsp;';
	} ?>
	</div>
	<?php }?>
<?php if ($data and $data['id_tq_test'] ) { ?>
<br><br>
<div class="col-sm-7">
	<b>1) QUALITY NEW</b><br>
	<a target = "_BLANK" href="TestingNew-<?=$nodemand_qn?>">Link QN <?=$nodemand_qn?></a>
	<iframe width=550px height=400px src = "pages/cetak/cetak_result.php?idkk=<?=$data['id']?>&noitem=<?=$data['no_item']?>&nohanger=<?=$data['no_hanger']?>">
	</iframe>
</div>
<div class="col-sm-3" >
	<b>2) FIRST LOT</b> <br>
	
	<?php if ($data['id_fl']) {?>
	<a target = "_BLANK" href="TestingNewFL-<?=$nodemand_fl?>">Link FL <?=$nodemand_fl?></a>
		
	<?php $id_fl = $data['id_fl'];} else {
		 echo '<div style="color:red">';
		 echo 'No Demand First LOT Not Found';
		 echo '</div>';
	}
		 
	?>

</div>

<div class="col-sm-2" >
	<?php if ($data and $data['id_tq_test']  and $data['id_fl']) {  ?>
		<form action="" method="POST" id="myForm" onsubmit="return confirmSubmit();">
			
			<input value="<?=$nodemand_qn?>" required type="hidden" name="nodemand" >
			<input value="<?=$nodemand_fl?>" required type="hidden" name="nodemand_fl" >
	
			<input type="hidden" name="synkron_id_qn" value=<?=$data['id']?>>
		
			<button name="update_first_lot" type="submit" class="btn btn-info pull-right">3) Syncronize</button>
		</form>
	<?php } ?>
</div>



<?php } ?>


    <script>
        function confirmSubmit() {
            // Display a confirmation dialog
            return confirm("Are you sure you want to update to First Lot?");
        }
    </script>

<?php if (isset($_POST['update_first_lot'])) {
$synkron_id_qn = $_POST['synkron_id_qn'];
// echo "Synkron ID QN : ".$synkron_id_qn ;
?>


<?php
	function insert_fl($nodemand, $tabel_sumber, $tabel_tujuan) {
		global $con_db_qc_sqlsrv, $synkron_id_qn;

		$sqlCekNew = " SELECT TOP (1) a.*
			FROM $tabel_sumber a
			INNER JOIN db_qc.tbl_tq_nokk b ON a.id_nokk = b.id
			WHERE b.id = ?
			ORDER BY a.id DESC
		";

		$stmtNew = sqlsrv_query($con_db_qc_sqlsrv, $sqlCekNew, [$synkron_id_qn]);
		if ($stmtNew === false) return false;

		$rcekNew = sqlsrv_fetch_array($stmtNew, SQLSRV_FETCH_ASSOC);
		if (!$rcekNew) return false;

		$sqlFlPk = " SELECT TOP (1) a.id AS pk
			FROM db_qc.tbl_tq_first_lot a
			WHERE a.nodemand = ?
			ORDER BY a.id DESC
		";
		$stmtPk = sqlsrv_query($con_db_qc_sqlsrv, $sqlFlPk, [$nodemand]);
		if ($stmtPk === false) return false;

		$rowPk = sqlsrv_fetch_array($stmtPk, SQLSRV_FETCH_ASSOC);
		if (!$rowPk || empty($rowPk['pk'])) return false;

		$id_fl = $rowPk['pk'];

		$sqlCekTujuan = " SELECT TOP (1) id
			FROM $tabel_tujuan
			WHERE id_nokk = ?
			ORDER BY id DESC
		";
		$stmtT = sqlsrv_query($con_db_qc_sqlsrv, $sqlCekTujuan, [$id_fl]);
		if ($stmtT === false) return false;

		$rowT = sqlsrv_fetch_array($stmtT, SQLSRV_FETCH_ASSOC);

		$skip = ['id', 'id_nokk'];
		$cols = [];
		$vals = [];

		foreach ($rcekNew as $k => $v) {
			if (!is_string($k)) continue;
			if (in_array(strtolower($k), $skip, true)) continue;

			$cols[] = $k;
			$vals[] = $v;
		}

		if ($rowT && !empty($rowT['id'])) {
			$setParts = [];
			$params = [];

			foreach ($cols as $i => $colName) {
				$setParts[] = "$colName = ?";
				$params[] = $vals[$i];
			}
			$params[] = $rowT['id'];

			$sqlUpdate = " UPDATE $tabel_tujuan
				SET " . implode(", ", $setParts) . "
				WHERE id = ?
			";

			$ok = sqlsrv_query($con_db_qc_sqlsrv, $sqlUpdate, $params);
			return $ok !== false;
		} else {
			$placeholders = implode(", ", array_fill(0, count($cols) + 1, "?")); // +1 untuk id_nokk
			$params = array_merge([$id_fl], $vals);

			$sqlInsert = " INSERT INTO $tabel_tujuan (id_nokk, " . implode(", ", $cols) . ")
				VALUES ($placeholders)
			";

			$ok = sqlsrv_query($con_db_qc_sqlsrv, $sqlInsert, $params);
			return $ok !== false;
		}
	}

	$nodemand = $_POST['nodemand_fl'];

	$tq_test     = insert_fl($nodemand, 'db_qc.tbl_tq_test',     'db_qc.tbl_tq_test_fl');
	$tq_marginal = insert_fl($nodemand, 'db_qc.tbl_tq_marginal', 'db_qc.tbl_tq_marginal_fl');
	$disptest    = insert_fl($nodemand, 'db_qc.tbl_tq_disptest', 'db_qc.tbl_tq_disptest_fl');

	if ($tq_test || $tq_marginal || $disptest) {
		echo "<script>alert('The data has been successfully updated to $nodemand');</script>";
	} else {
		echo "<script>alert('Data Update Failed');</script>";
	}
?>

<?php } ?>