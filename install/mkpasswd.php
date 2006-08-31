<?php
if (!isset($_POST['to']))
{
    $str ="<form action=\"mkpasswd.php\" method=\"POST\" target=\"_self\">";
    $str.="Desired password: <input type=\"password\" name=\"strPasswd\" size=\"20\" maxlength=\"20\"/><br/>";
	$str.="<input type=\"submit\" value=\"Generate password...\">";
	$str.="<input type=\"hidden\" name=\"to\"value=\"go\">";
	$str.="</form>";
    echo $str;
}
else if ($_POST['to']=="go")
{
//	header('Content-type: text/plain;');
//	header('Content-Disposition: attachment; filename="passwd"');
    echo "password clear : ". $strPasswd;
	echo "<br>encrypted : ".crypt($_POST['strPasswd'], "nevertable");
}
?>
