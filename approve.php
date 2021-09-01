<?php include 'common.php'; ?>

<html>

<head>
	<title>Scratch Wiki Accounts</title>
	<link href="style.css" rel="stylesheet" type="text/css">
	<script src="approve.js" type="text/javascript"></script>
</head>

<body>
<div id="maincontainer">
<img src="http://wiki.scratch.mit.edu/skins/scratch/logo.png"></img><br>
<div class="box_fullwidth">

<h1>Scratch Wiki Account Approval</h1>

<!-- <b><img src="attention.png">Make sure you are <a href="http://wiki.scratch.mit.edu/index.php?title=Special:UserLogin" target="_NEW">logged in</a> on the Scratch Wiki too before using this page!</b><br><br> -->

<?php
$showform = false;
$isadmin = false;
if (isset($_POST['user'])) {
	$user = $_POST['user'];
	$pass = $_POST['pass'];
	
	//call ScratchR API to authenticate user
	$r = scratchr_auth_api($user, $pass);
	
	$admins = 'JSO Lightnin andresmh Lucario621 Jonathanpb Chrischb WeirdF scimonster';
	$authorized = ($r!=false);
	$isadmin    = strpos("  $admins ", " $user ");
	if ($authorized&&$isadmin) {
		$_SESSION['isadmin'] = true;
		$_SESSION['admin'] = $user;
	} else {
		echo 'Invalid username/password or not an admin';
	}
}
if ($_SESSION['isadmin']==true) {
	//connect to MySQL
	mysql_connect('localhost', 'scratchwiki', '[removed]'); //used to be on ritalin.media.mit.edu
	mysql_select_db('scratchwiki');
	
	//read the current admin from session
	$admin = $_SESSION['admin'];

	if (isset($_POST['newuser'])) {
		// NOT USED ANYMORE //
		
		//the post request has been sent
		$newuser = $_POST['newuser'];
		$newpass = $_POST['newpass'];
	
		//check if new user is already in db
		$q = "SELECT * FROM account_requests WHERE username='$newuser'";
		$r = mysql_query($q);
		
		if (mysql_num_rows($r)==0) {
			//it is not; append new entry to table
			$q = "INSERT INTO account_requests ( username, password, state ) VALUES('$newuser', '$newpass', '1')";
			$r = mysql_query($q);
		} else {
			//update user password & state (set to approved)
			$q = "UPDATE account_requests SET password='$newpass', state='1' WHERE username='$newuser'";
			$r = mysql_query($q);
		}
	} else {
		if (isset($_GET['a'])) {
			$id = $_GET['id'];
			switch ($_GET['a']) {
				case 'pending':
					$q = "UPDATE account_requests SET state='0', admin='$admin' WHERE id='$id'";
					$r = mysql_query($q);
					break;
				case 'approve':
					$p = $_GET['p'];
					$u = $_GET['u'];
					$q = "UPDATE account_requests SET state='1', password='$p', admin='$admin' WHERE id='$id'";
					$r = mysql_query($q);
					echo "User created:<br> <table><tr> <td><b>username:</b></td> <td>$u</td> </tr> <tr> <td><b>password:</b></td> <td>$p</td> <tr></table> <br> You still have to <a href=\"http://wiki.scratch.mit.edu/index.php?title=Special:UserLogin&type=signup\" target=\"_NEW\">create this account</a> on the wiki. This is a temporary issue.<br><br>";
					break;
				case 'onhold':
					$q = "UPDATE account_requests SET state='2', admin='$admin' WHERE id='$id'";
					$r = mysql_query($q);
					break;
				case 'logout':
					session_destroy();
					echo 'You could <a href="approve.php">login again</a>.';
					die();
			}
		}
		
		//show approve page
?>
	<a href="approve.php?a=logout">[log out]</a><br>
	<br>
	<br>
<?php
		$showall = (isset($_GET['show']) && $_GET['show']=='all');
		
		//show unapproved accounts
		$q = "SELECT * FROM account_requests WHERE state='0'";
		$r = mysql_query($q);
		echo '<h3>Acounts pending approval (' . mysql_num_rows($r) . ') </h3>';
		while($row = mysql_fetch_array($r)) {
			echo parseuser($row);			
		}
		
		//show accounts on hold
		$q = ($showall) ? "SELECT * FROM account_requests WHERE state='2'" : "SELECT * FROM account_requests WHERE state='2' ORDER BY id DESC LIMIT 0, 20;";
		$r = mysql_query($q);
		if ($showall) {
			echo '<h3>Acounts on hold (' . mysql_num_rows($r) . ') </h3>';
		} else {
			echo '<h3>Accounts put on hold recently</h3><br><a href="approve.php?show=all">Show all...</a><br><br><br>';
		}
		while($row = mysql_fetch_array($r)) {
			echo parseuser($row);			
		}
	}		
} else {
	//show admin login form

?>
		Please log in with an Administrator account (scratch.mit.edu login)<br>
	<br>
	<form action="approve.php" method="POST">
		Username:<br>
		<input type="text" name="user"></input><br>
		
		Password:<br>
		<input type="password" name="pass"></input><br>
		<br>
		
		<input type="submit" value="login"></input>
	</form>
	
<?php
	
}
echo mysql_error();
?>

</div>
</div>
</body>

</html>
