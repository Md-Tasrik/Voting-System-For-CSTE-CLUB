<?php
	include 'includes/session.php';

	if(isset($_POST['add'])){
		$firstname = $_POST['firstname'];
		$lastname = $_POST['lastname'];
		$email = isset($_POST['email']) ? $_POST['email'] : '';
                $idnos = $_POST['idnos'];
		$vpass = $_POST['password'];
		$password = password_hash($_POST['password'], PASSWORD_DEFAULT);
		$filename = $_FILES['photo']['name'];
		if(!empty($filename)){
			move_uploaded_file($_FILES['photo']['tmp_name'], '../images/'.$filename);	
		}
		//generate voters id
		//$set = '123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		//$voter = substr(str_shuffle($set), 0, 5);
                //this should do
                $voter = $idnos;

		// Ensure email column exists
		$checkEmailCol = $conn->query("SHOW COLUMNS FROM voters LIKE 'email'");
		if($checkEmailCol && $checkEmailCol->num_rows === 0){
			$conn->query("ALTER TABLE voters ADD COLUMN email varchar(100) NULL AFTER lastname");
		}

		$sql = "INSERT INTO voters (voters_id, password, firstname, lastname, email, photo, vpass) VALUES ('$voter', '$password', '$firstname', '$lastname', '".$conn->real_escape_string($email)."', '$filename', '$vpass')";
		if($conn->query($sql)){
			$_SESSION['success'] = 'Voter added successfully';
		}
		else{
			$_SESSION['error'] = $conn->error;
		}

	}
	else{
		$_SESSION['error'] = 'Fill up add form first';
	}

	header('location: voters.php');
?>