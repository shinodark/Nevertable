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
define('NVRTBL', 1);
include_once ROOT_PATH ."config.inc.php";
include_once ROOT_PATH ."includes/common.php";
include_once ROOT_PATH ."includes/classes.php";

if(empty($_GET['css']))
      $css = "popup.css";
    else
      $css = $_GET['css'];

/* nécessaire pour le chargement de la conf */
$table = new Nvrtbl();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>


<head>
<title>Smilies</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<link rel="stylesheet" href="<?php echo $css ?>" type="text/css" />
<script type="text/javascript" src="jsutil.js"></script>
</head>
<body>
 <table><tr>
 <td colspan="5" class="smiliesHeader">
   Smilies
 </td></tr><tr>

<?php

$Smilies = new Smilies();
if ($Smilies->IsLoad()) {

 $list = $Smilies->GetList();

 $i=0;
 foreach($list as $code => $icon)
 {
  if (!($i % 5))  
    echo "</tr><tr>\n";
  echo "<td>\n";

  $mes  = "<a href=\"javascript:opener.document.forms['".$_GET['referer_form']."'].content.value+=' ";
  $mes .= $code . " ';window.focus();\">";
  $mes .= $icon;
  $mes .= "</a>\n";
  echo $mes;
  $i++;
 }
}
else
{
  echo "<tr><td colspan=\"5\">No Smilies defined !</td></tr>";
}
?>
  
</tr>
<tr><td colspan="5" class="smiliesHeader">
    <a href="javascript:window.close()">Close</a>
</td></tr>
</table>
<?php $table->Close(); ?>
</body>
</html>
