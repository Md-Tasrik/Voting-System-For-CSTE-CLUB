<?php 
	include 'includes/session.php';

	if(isset($_POST['id'])){
		$id = $_POST['id'];
		// Ensure email column exists so fetch includes it
		$checkEmailCol = $conn->query("SHOW COLUMNS FROM voters LIKE 'email'");
		if($checkEmailCol && $checkEmailCol->num_rows === 0){
			$conn->query("ALTER TABLE voters ADD COLUMN email varchar(100) NULL AFTER lastname");
		}

		$sql = "SELECT * FROM voters WHERE id = '$id'";
		$query = $conn->query($sql);
		$row = $query->fetch_assoc();

		echo json_encode($row);
	}
?>