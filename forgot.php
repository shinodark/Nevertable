<?php
# ***** BEGIN LICENSE BLOCK *****
# This file is part of Nevertable .
# Copyright (c) 2004 Francois Guillet and contributors. All rights
# reserved.
#
# Nevertable is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
# 
# Nevertable is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
# 
# You should have received a copy of the GNU General Public License
# along with Nevertable; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
#
# ***** END LICENSE BLOCK *****

define('ROOT_PATH', "./");
include_once ROOT_PATH ."config.inc.php";
include_once ROOT_PATH ."includes/common.php";
include_once ROOT_PATH ."includes/classes.php";

//args process
$args = get_arguments($_POST, $_GET);

$table = new Nvrtbl("DialogStandard");
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<? $table->PrintHtmlHead("Nevertable - Neverball Hall of Fame"); ?>

<body>
<div id="page">
<?php   $table->PrintTop();  ?>
<div id="main">
<?php

function closepage()
{  global $table;
    echo "</div><!-- fin \"main\" -->\n";
    $table->Close();
    $table->PrintFooter();
    echo "</div><!-- fin \"page\" -->\n";
    echo "</body>\n";
    echo "</html>\n";
}


if(isset($args['run']))
{
  if (empty($args['email']))
  {
    button_error("Empty field. Email is needed !", 400);
    button_back();
    closepage();
    exit;
  }
  
  $res = $table->db->MatchUserByMail($args['email']);
  if ($table->db->NumRows() == 0) // pas trouvé
  {
    button_error("Sorry, this mail address doesn't exist in the table...", 500);
    button_back();
    closepage();
    exit;
  }
  else
    $val = $table->db->FetchArray($res);

  $keychars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
  $length = 8;
  $newpass = "";
  $max=strlen($keychars)-1;
  for ($i=0;$i<$length;$i++)
    $newpass .= substr($keychars, rand(0, $max), 1);

  //mise à jour du nouveau mot de passe
  if (!isset($val['id']))
    exit;
  $table->db->RequestInit("UPDATE",  "users");
  $table->db->RequestUpdateSet(array("passwd" => md5($newpass)));
  $table->db->RequestGenericFilter("id", $val['id']);
  $table->db->RequestLimit(1);
  if(!$table->db->Query()) {
    button_error($table->db->GetError(),500);
    button_back();
    closepage();
    exit;
  }

  //envoie du mail
  $m= new Mail; // create the mail
  $m->From( $config['admin_mail']);
  $m->To($args['email']);
  $m->Subject( "Nevertable : password recovery" );	
  $message  = "Try this... \n\n";
  $message .= "   login    : ".$val['pseudo']."\n";
  $message .= "   password : ".$newpass."\n\n";
  $message .= "See you soon !";
  $m->Body( $message);	// set the body
  $m->Send();	// send the mail
  
  
  button("Email sent.", 400);
  button("<a href=\"./\">Return to main page</a>", 200);
    
  echo "</div>\n";
  $table->Close();
  $table->PrintFooter();
  exit;
}
else
{
?>
      <div class="nvform" style="width: 400px;">
      <form class="nvform" action="forgot.php?run" method="post">
      <table><tr>
      <th colspan="2" align="center"> I've forgot my password ! </th></tr>
      <td><label for=\"email\">email : </label></td>
      <td><input type="text" id="email" name="email" size="40" /></td>
      </tr><tr>
      <td colspan="2"><center><input type="submit" value="Send" /></center></td>
      </form>
      </tr></table>
      </div>
<?php
}
button("<a href=\"index.php\">Return to table</a>", 300);
?>
</div> <!-- fin main-->
<?php
$table->Close();
$table->PrintFooter();
?>

</div><!-- fin "page" -->
</body>
</html>
