<html>

<head>
	<title>Scratch Wiki Accounts</title>
	<link href="style.css" rel="stylesheet" type="text/css">
</head>

<body>
<div id="maincontainer">
<img src="http://wiki.scratch.mit.edu/skins/scratch/logo.png"></img><br>
<div class="box_fullwidth">

<h1>Scratch Wiki Accounts</h1>

<?php
$showform = false;
$errormsg = '';

if (isset($_POST['user'])) {
	//the post request has been sent
	$user = $_POST['user'];
	$pass = $_POST['pass'];
	
	//call ScratchR API to authenticate user
	$reply = file_get_contents("http://scratch.mit.edu/api/authenticateuser?username=" . rawurlencode($user) . "&password=" . rawurlencode($pass) );
	$reply = trim($reply);
	$reply = ($reply!='false') ? explode(':', $reply) : 'notfound';
	
	$authorized = ($reply!='notfound');

	if ($authorized) {
		//connect to MySQL
		mysql_connect('localhost', 'scratchwiki', 'scratchw1k1!');
		mysql_select_db('scratchwiki');
		
		//get user info from db
		$q = "SELECT * FROM account_requests WHERE username='$user'";
		$r = mysql_query($q);
		
		if (mysql_num_rows($r)==0) {
			//the user is not in the db, return an error
			$showform = true;
			$errormsg = 'You have not yet <a href="request.php">requested</a> an account.';
		} else {
			//check if the user is approved.
			$row=mysql_fetch_array($r);
			if ($row['state']=='1') {
				$wikipass=$row['password'];
?>
	Your Scratch Wiki account has been approved.<br>
	<br>
	You can go to <a href="http://wiki.scratch.mit.edu/index.php?title=Special:UserLogin" target="_NEW">the Login page</a> and log in with:<br>
	<br>
	
	<?php
	echo "<table> \n".
		 "<tr> <td> <b>Username:</b> </td> <td>$user</td> </tr> \n".
		 "<tr> <td> <b>Inital password:</b> </td> <td>$wikipass</td> </tr> \n".
		 "</table>\n";
	?>
	<br>
	<br>
	Please change this initial password as soon as possible on your preferences page.<br>
	(After login, click the [+] next to your username, then 'My Preferences')<br>
	<br>
	Please read the <a href="http://wiki.scratch.mit.edu/wiki/Scratch_Wiki:Welcome" target="_NEW">Welcome Page</a> on the wiki to get started.<br>
	<.br>
	Thank you for willing to help with the Scratch Wiki, and Scratch On!
	
<?php
			} else if ($row['state']=='2') {
				$showform = false;
				$errormsg = "Thank you for your interest in becoming an editor! At this time, we are looking for Wiki editors with a longer history of positive interactions in the Scratch community. Please continue contributing to the community as described in the Scratch Terms of Use and check again later.<br><br>Thank you, and <br> Scratch On!";
			} else {
				$showform = false;
				$errormsg = 'Your account has not yet been approved. Please check again later.';
			}			
		}
	} else {
		$showform = true;
		$errormsg = 'Invalid username/password combination.';
	}		
} else {
	$showform = true;
}
if ($errormsg!='') {
		echo "<b>Oops...</b><br> $errormsg <br><br>";
}
if ($showform) {
?>

	Please fill in your Scratch Login and Password.<br>
	You will then receive your Scratchers Wiki Login if your account has been approved.<br>
	<br>
	<form action="mypassword.php" method="POST">
	scratch.mit.edu Username:<br>
	<input type="text" name="user"></input><br>
	Password:<br>
	<input type="password" name="pass"></input><br>
	<br>
	<input type="submit" value="continue"></input>
	</form>

<?php
}
echo mysql_error();
?>

</div>
</div>
</body>

</html>
