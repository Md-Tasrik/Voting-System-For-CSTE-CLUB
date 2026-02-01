<?php
	include 'includes/session.php';

	if(isset($_POST['edit'])){
		$id = $_POST['id'];
		$firstname = $_POST['firstname'];
		$lastname = $_POST['lastname'];
		$email = isset($_POST['email']) ? $_POST['email'] : '';
		$password = $_POST['password'];
		$vpass = $password;

		$sql = "SELECT * FROM voters WHERE id = $id";
		$query = $conn->query($sql);
		$row = $query->fetch_assoc();

		if($password == $row['password']){
			$password = $row['password'];
		}
		else{
			$password = password_hash($password, PASSWORD_DEFAULT);
		}

		// Ensure email column exists
		$checkEmailCol = $conn->query("SHOW COLUMNS FROM voters LIKE 'email'");
		if($checkEmailCol && $checkEmailCol->num_rows === 0){
			$conn->query("ALTER TABLE voters ADD COLUMN email varchar(100) NULL AFTER lastname");
		}

		$sql = "UPDATE voters SET firstname = '$firstname', lastname = '$lastname', email='".$conn->real_escape_string($email)."', password = '$password', vpass='$vpass' WHERE id = '$id'";
		if($conn->query($sql)){
			$_SESSION['success'] = 'Voter updated successfully';
		}
		else{
			$_SESSION['error'] = $conn->error;
		}
	}
	else{
		$_SESSION['error'] = 'Fill up edit form first';
	}

	header('location: voters.php');

?>