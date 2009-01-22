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
	
/* Template html header */
/**
 * @param: manage_lang lang to edit
 * @param: announce Text to display as announce
 * @param: speech Text to display as speech
 * @param: conditions Text to display in conditions page 
 */

global $lang, $config;


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
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

<div  class="generic_form" style="width: 400px;">
<form method="post" action="management.php" name="lang_form" id="lang_form" >
<table><tr>
<th colspan="2" align="center"><?php echo $lang['ADMIN_MANAGEMENT_LANG_FORM_TITLE'] ?></th></tr><tr>
</tr><tr>
<td><label for="manage_lang"><?php echo $lang['ADMIN_MANAGEMENT_LANG_FORM_LANG'] ?></label></td>
<td>
<select id="manage_lang" name="manage_lang">
<option value="0">en</option>
<option value="1">fr</option>
</select>
</td>
</tr>
<tr>
<td colspan="2">
<center><input type="submit"  /></center>
</td>
</tr>
</table>
</form>
</div>

<script>change_form_select('manage_lang',  '<?php echo get_lang_by_name($manage_lang) ?>');</script>

<div  class="generic_form" style="width: 700px;">
<form method="post" action="management.php?upannounce&amp;manage_lang=<?php echo get_lang_by_name($manage_lang) ?>" name="announce_form" id="announce_form" >
<table>
<tr>
<th colspan="2" align="center"><?php echo $lang['ADMIN_MANAGEMENT_ANNOUNCE_FORM_TITLE'] ?></th>
</tr>
<tr>
<td colspan="2">
<textarea id="announce" name="announce" rows="5" style="width:100%;">
<?php echo $announce ?>
</textarea>
</td>
</tr>
<tr>
<td colspan="2">
<center><input type="submit"  /></center>
</td>
</tr>
</table>
</form>
</div>


<div  class="generic_form" style="width: 700px;">
<form method="post" action="management.php?upspeech&amp;manage_lang=<?php echo get_lang_by_name($manage_lang) ?>" name="speech_form" id="speech_form" >
<table>
<tr>
<th colspan="2" align="center"><?php echo $lang['ADMIN_MANAGEMENT_SPEECH_FORM_TITLE'] ?>
</th>
</tr>
<tr>
<td colspan="2">
<textarea id="speech" name="speech" rows="20" style="width:100%;">
<?php echo $speech ?>
</textarea>
</td>
</tr>
<tr>
<td colspan="2">
<center><input type="submit"  /></center>
</td>
</tr>
</table>
</form>
</div>

<div  class="generic_form" style="width: 700px;">
<form method="post" action="management.php?upconditions&amp;manage_lang=<?php echo get_lang_by_name($manage_lang) ?>" name="conditions_form" id="conditions_form" >

<table><tr>
<th colspan="2" align="center"><?php echo $lang['ADMIN_MANAGEMENT_CONDITIONS_FORM_TITLE'] ?>
</th>
</tr>
<tr>
<td colspan="2">
<textarea id="conditions" name="conditions" rows="60" style="width:100%;">
<?php echo $conditions ?> 
</textarea>
</td>
</tr>
<tr>
<td colspan="2">
<center><input type="submit"  /></center>
</td>
</tr>
</table>
</form>
</div>


<div class="button" style="width:200px;">
<a href="../index.php"><?php echo $lang['GUI_BUTTON_MAINPAGE'] ?></a>
</div>

</div><!--  main end -->
<div id="footer">
<?php $this->SubTemplate('_footer');?>
</div> 
</div><!-- page end -->
</body>
</html>