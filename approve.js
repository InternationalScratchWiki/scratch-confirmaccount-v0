function generatePassword(pwdlength){
   	var validchars = "0123456789abcdfghjkmnpqrstvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
   	var password = ""
   	for (i=0;i<pwdlength;i++) {
	   	password += validchars.charAt( Math.floor(Math.random()*(validchars.length)) );
	}
	return password;
}
function approve(username, id) {
	var password = generatePassword(8);
	/*
	var x=document.getElementById("wikiform");
	var y=(x.contentWindow || x.contentDocument);
	if (y.document)y=y.document;
	
	y.getElementById('wpName2').value = username;
	y.getElementById('wpPassword2').value = password;
	y.getElementById('wpRetype').value = password;
	
	y.forms["userlogin2"].submit();*/
	document.location.href = "http://wiki.scratch.mit.edu/accounts/approve.php?a=approve&id="+id+"&p="+password+"&u="+username;
}