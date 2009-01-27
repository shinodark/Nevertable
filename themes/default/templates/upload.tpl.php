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
if (!defined('NVRTBL'))
	exit;
	
/* Template upload page 
 *
 */

global $lang, $config, $types;


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $lang['code'] ?>" lang="<?php echo $lang['code'] ?>">
<head>
<?php $this->SubTemplate('_head'); ?>
</head>
<body>
<div id="page">
<div id="top">
<?php $this->SubTemplate('_top');?>
</div> 
<div id="main">
<div id="prelude">
<?php $this->SubTemplate('_menu');?>
</div>
  
<div  class="generic_form" style="width: 500px;">
<form method="post" action="upload.php?autoadd" name="upload_form" enctype="multipart/form-data">
<table>
<tr>
<th colspan="2" align="center"><?php echo $lang['UPLOAD_FORM_TITLE'] ?></th></tr><tr>
</tr><tr>
<td colspan="2"><center><?php echo sprintf($lang['UPLOAD_FORM_SIZEMAX'], floor($config['upload_size_max']/1024)) ?></center></td></tr><tr>
</tr><tr>
<td><label for="pseudo"><?php echo$lang['UPLOAD_FORM_PSEUDO'] ?></label></td>
<td colspan="1"><input type="text" id="pseudo" name="pseudo"  size="10" value="<?php echo $_SESSION['user_pseudo'] ?>" readonly="readonly"  /></td>
</tr><tr>
<td><label for="type"><?php echo $lang['UPLOAD_FORM_TYPE'] ?></label></td>
<td>
<select id="type" name="type">
<?php foreach($types as $nb => $value) { ?>
	<?php if ($nb > 0) { ?>
		<option value="<?php echo $nb ?>"><?php echo $lang[$types[$nb]] ?></option>
	<?php } ?>
<?php } ?>
</select>
</td>
</tr><tr>
<td><label for="replayfile"><?php echo  $lang['UPLOAD_FORM_REPLAYFILE'] ?></label></td>
<td colspan="1"><input type="file" id="replayfile" name="replayfile"  size="40" /></td>
<td colspan="2"><input type="hidden" id="user_id" name="user_id"  value="<?php echo $_SESSION['user_id'] ?>" /></td>
<td colspan="2"><input type="hidden" id="size_max" name="MAX_FILE_SIZE"  value="<?php echo $config['upload_size_max'] ?>" /></td>
</tr><tr>
<td colspan="2"><center><input type="submit"  /></center></td>
</tr>
</table>
</form>
</div>

<div class="button" style="width:200px;">
<a href="index.php"><?php echo $lang['GUI_BUTTON_MAINPAGE'] ?></a>
</div>
</div><!--  main end -->
<div id="footer">
<?php $this->SubTemplate('_footer');?>
</div> 
</div><!-- page end -->
</body>
</html>