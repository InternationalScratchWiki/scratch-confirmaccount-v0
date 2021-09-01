<?php
/*
Scratch Wiki account creation common functions and vars
Joren Lauwers - 2010
*/

function scratchr_api($api_function, $arg1 = NULL, $arg2 = NULL , $arg3 = NULL) {
	//general Scratch API function.
	$request = "http://scratch.mit.edu/api/$api_function";
	if (isset($arg1)) $request .= '/'.rawurlencode($arg1);
	if (isset($arg2)) $request .= '/'.rawurlencode($arg2);
	if (isset($arg3)) $request .= '/'.rawurlencode($arg3);
	if (isset($arg4)) $request .= '/'.rawurlencode($arg4);
	$reply = file_get_contents($request);
	$reply = trim($reply); //Bug on ScratchR side, ticket #422 on Assembla.
	$reply = ($reply!='false') ? explode(':', $reply) : false;
	return $reply;
}
function scratchr_auth_api($u, $p) {
	//uses a different requestform so needs a separate function
	$u = rawurlencode($u);
	$p = rawurlencode($p);
	$request = "http://scratch.mit.edu/api/authenticateuser?username=$u&password=$p";
	$reply = file_get_contents($request);
	$reply = trim($reply); //Bug on ScratchR side, ticket #422 on Assembla.
	$reply = ($reply!='false') ? explode(':', $reply) : false;
	$reply = (count($reply)==3 && $reply[2]=='unblocked') ? $reply : false;
	return $reply;
}

function parseuser($data) {
	$username = $data['username'];
	$description = $data['description'];
	$id = $data['id'];
	$state = $data['state'];
	$uadmin = $data['admin'];
	$html = "<b><a href=\"http://scratch.mit.edu/users/$username\">$username</a></b> | ";
	
	
	switch($state) {
		case 0:
			$html .= "<a href=\"approve.php?a=onhold&id=$id\">on hold</a> | <a href=\"javascript:approve('$username',$id);\">approve</a><br>";
			break;
		case 2:
			$html .= "<a href=\"approve.php?a=pending&id=$id\">pending</a> | <a href=\"javascript:approve('$username',$id);\">approve</a><br>";
			break;
	}
	//retrieve nr of projects from api
	$r = scratchr_api('getprojectsbyusername', $username);
	$noprojects = count($r);
	
	$html .= "projects: $noprojects | ";
	
	if (strlen($uadmin)>2) {
		switch($state) {
			case 0:
				$html .= "<b>set back to pending by $uadmin</b>";
				break;
			case 2:
				$html .= "<b>set on hold by $uadmin</b>";
				break;
		}
	}
	$html .= '<br>'.$description;
	
	$html .= "<br><br> \n";
	return $html;
}
session_start();
?>