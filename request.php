<html>

<head>
	<title>Scratch Wiki Accounts</title>
	<link href="style.css" rel="stylesheet" type="text/css">
</head>

<body>
<div id="maincontainer">
<img src="http://wiki.scratch.mit.edu/skins/scratch/logo.png"></img><br>
<div class="box_fullwidth">

<h1>Scratch Wiki Account Request</h1>

<?php
$showform = false;

if (isset($_POST['user'])) {
	//the post request has been sent
	$user = $_POST['user'];
	$pass = $_POST['pass'];
	$desc = $_POST['description'];
	
	//call ScratchR API to authenticate user
	$reply = file_get_contents("http://scratch.mit.edu/api/authenticateuser?username=" . rawurlencode($user) . "&password=" . rawurlencode($pass) );
	$reply = trim($reply);
	$reply = ($reply!='false') ? explode(':', $reply) : 'notfound';
	
	$authorized = ($reply!='notfound');
	
	if ($authorized) {
		//connect to MySQL
		mysql_connect('localhost', 'scratchwiki', '[removed]');
		mysql_select_db('scratchwiki');
		
		//check if new user is already in db
		$q = "SELECT * FROM account_requests WHERE username='$user'";
		$r = mysql_query($q);
		
		$user = mysql_escape_string($user);
		$pass = mysql_escape_string($pass);
		$desc = mysql_escape_string($desc);
		
		if (mysql_num_rows($r)==0) {
			//it is not; append new entry to table
			$q = "INSERT INTO account_requests ( username, description, state ) VALUES('$user', '$desc', '0')";
			$r = mysql_query($q);
			echo 'Thank you.<br><br>Your account has been requested. Check <a href="mypassword.php">this page</a> every now and then to see if it has been approved.';
		} else {
			$row = mysql_fetch_array($r);
			if ($row['state']==1) {
				echo '<b>Something went wrong!</b><br>You have already requested an account and it has been approved. Please find your password <a href="mypassword.php">here</a> .<br><br>';
			} else {
				echo '<b>Something went wrong!</b><br>You have already requested an account and it is still pending approval. Check <a href="mypassword.php">this page</a> every now and then to see if it has been approved.<br><br>';
			}
		}
	} else {
		echo 'Invalid username/password<br><br>';
		$showform = true;
	}		
} else {
	$showform = true;
}

if ($showform) {
?>
	On this page, you can request to become an editor on the Scratch Wiki. When you are accepted as a wiki editor, your password will be available on <a href="mypassword.php">this page</a>.<br>
	<br>
	In order to request an account, please prove who you are with your scratch.mit.edu login.<br>
	<br>
	(this is not a phishing site, info from the scratch team is available <a href="http://scratch.mit.edu/forums/viewtopic.php?id=37179&p=1">here</a>)<br>
	<br>
	<form action="request.php" method="POST">
	Username:<br>
	<input type="text" name="user"></input><br>
	Password:<br>
	<input type="password" name="pass"></input><br>
	<br>
	Please explain in a few sentences (700 characters max) why you are interested in becoming a Scratch Wiki editor:<br>
	<textarea name="description" cols="50" rows="5"></textarea><br>
	<br>
	<input type="submit" value="request"></input>
	</form>
<?php
}
echo mysql_error();
?>

</div>
</div>
</body>

</html>
