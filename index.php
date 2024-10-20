<?php 
require_once("include/initialize.php");
$msg="";
if(isset($_POST['submit'])){
	$user = $_POST['username'];
	$pass = $_POST['pass'];
	
	$userdata = check_login($user,$pass);
	if(mysqli_num_rows($userdata)>0){
		$res=mysqli_fetch_assoc($userdata);
		
		$_SESSION['logged_in'] = true;
		$_SESSION['name'] = $res['first_name'] ." ". $res['last_name'];
		$_SESSION['user'] = $res['user_name'];
		$_SESSION['user_id'] =$res['id'];
		$_SESSION['role_id'] = $res['user_role_id'];
		$_SESSION['company_id'] = $res['company_id'];
		

		redirect_to("dashboard.php");
	}


	else{

		$msg= " username / password worng !! try again !!";

	}



}
if (isset($_GET['logout']) && $_GET['logout']==true) {

	session_destroy ();
}



?>

<!DOCTYPE html>
<html lang="en">
<head>
	<title>Emon Dental</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
<!--===============================================================================================-->	
	<link rel="icon" type="image/png" href="assests_login/images/icons/favicon.ico"/>
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="assests_login/vendor/bootstrap/css/bootstrap.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="assests_login/fonts/font-awesome-4.7.0/css/font-awesome.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="assests_login/fonts/iconic/css/material-design-iconic-font.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="assests_login/vendor/animate/animate.css">
<!--===============================================================================================-->	
	<link rel="stylesheet" type="text/css" href="assests_login/vendor/css-hamburgers/hamburgers.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="assests_login/vendor/animsition/css/animsition.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="assests_login/vendor/select2/select2.min.css">
<!--===============================================================================================-->	
	<link rel="stylesheet" type="text/css" href="assests_login/vendor/daterangepicker/daterangepicker.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="assests_login/css/util.css">
	<link rel="stylesheet" type="text/css" href="assests_login/css/main.css">
<!--===============================================================================================-->
</head>
<body>
	
	
	<div class="container-login100" style="background-image: url('assests_login/images/bg-01.jpg');">
		<div class="wrap-login100 p-l-55 p-r-55 p-t-80 p-b-30">
			<form class="login100-form validate-form" method="post"  action="<?=$_SERVER['PHP_SELF'];?>">
				<span class="login100-form-title p-b-37">
					Sign In
				</span>

				<div class="wrap-input100 validate-input m-b-20" data-validate="Enter username">
					<input class="input100" type="text" name="username" placeholder="username ">
					<span class="focus-input100"></span>
				</div>

				<div class="wrap-input100 validate-input m-b-25" data-validate = "Enter password">
					<input class="input100" type="password" name="pass" placeholder="password">
					<span class="focus-input100"></span>
				</div>

				<div class="container-login100-form-btn">
					<button class="login100-form-btn" name="submit" type="submit">
						Sign In
					</button>
					<div class="text-center p-t-57 p-b-20">
					<span class="txt1">
						<?php
							if(isset($msg)){
								echo $msg;
							}


						?>
					</span>
				</div>




				</div>



			</form>

			
		</div>
	</div>
	
	

	<div id="dropDownSelect1"></div>
	
<!--===============================================================================================-->
	<script src="assests_login/vendor/jquery/jquery-3.2.1.min.js"></script>
<!--===============================================================================================-->
	<script src="assests_login/vendor/animsition/js/animsition.min.js"></script>
<!--===============================================================================================-->
	<script src="assests_login/vendor/bootstrap/js/popper.js"></script>
	<script src="assests_login/vendor/bootstrap/js/bootstrap.min.js"></script>
<!--===============================================================================================-->
	<script src="assests_login/vendor/select2/select2.min.js"></script>
<!--===============================================================================================-->
	<script src="assests_login/vendor/daterangepicker/moment.min.js"></script>
	<script src="assests_login/vendor/daterangepicker/daterangepicker.js"></script>
<!--===============================================================================================-->
	<script src="assests_login/vendor/countdowntime/countdowntime.js"></script>
<!--===============================================================================================-->
	<script src="assests_login/js/main.js"></script>

</body>
</html>