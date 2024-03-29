<?php
session_start();
//	echo "<pre>";
//		print_r($_POST);
//	echo "</pre>";

foreach ($_POST as $value){
	if (empty($value)){
		$_SESSION["error"] = "Wypełnij wszystkie pola!";
		echo "<script>history.back();</script>";
		exit();
	}
}

$error = 0;
if (!isset($_POST["terms"])){
	$error = 1;
	$_SESSION["error"] = "Zatwierdź regulamin!";
}

if ($_POST["pass1"] != $_POST["pass2"]){
	$error = 1;
	$_SESSION["error"] = "Hasła są różne!";
}

if ($_POST["email1"] != $_POST["email2"]){
	$error = 1;
	$_SESSION["error"] = "Adresy poczty elektronicznej są różne!";
}

//if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^\w\d\s])\S{2,}$/', $_POST["pass1"])){
//	$error = 1;
//	$_SESSION["error"] = "Hasło nie spełnia wymagań co do złożoności!";
//}

if ($error != 0){
	echo "<script>history.back();</script>";
	exit();
}

require_once "./connect.php";

try {
    $role_id =1; // Wartość "role_id" dla roli, którą chcesz przypisać użytkownikowi

	$stmt = $conn->prepare("INSERT INTO `users` (`firstName`, `lastName`,`email`, `password`,  `phoneNumber`, `role_id`, `created_at`) VALUES (?, ?, ?, ?, ?, ?, current_timestamp());");

	$hash = password_hash($_POST["pass1"], PASSWORD_ARGON2ID);

	$stmt->bind_param("sssssi", $_POST["firstName"], $_POST["lastName"], $_POST["email1"], $hash, $_POST["phoneNumber"], $role_id);

	$stmt->execute();

	if ($stmt->affected_rows == 1){
		$_SESSION["success"] = "Dodano użytkownika $_POST[firstName] $_POST[lastName]";
		header("location: ../pages");
		exit();
	}else{
		$_SESSION["error"] = "Nie dodano użytkownika";
		echo "<script>history.back();</script>";
		exit();
	}
} catch (mysqli_sql_exception $e) {
		$_SESSION["error"] = $e->getMessage();
		echo "<script>history.back();</script>";
}


