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
		
global $types, $levels, $folders, $folders_user, $newonly, $lang, $args;

  	
/* Diffview allowed ? */
  if (isset($args['level_f']) && isset($args['levelset_f'])
     && ($args['level_f'] > 0) && ($args['levelset_f'] > 0)
     && isset($args['type']) && ($args['type'] > 0))
  {
    $mode_diffview = true;
  }
  else
  {
  	$mode_diffview = false;
  }
?>

<form method="post" action="?" name="typeform">
<table>
<tr>
<th colspan="6"><?php echo $lang['TYPE_FORM_TITLE'] ?></th>
</tr>
<tr>
<td>
<label for="table"><?php echo $lang['TYPE_FORM_TABLE_SELECT'] ?></label>
<select name="type" id="type">
<?php foreach($types as $nb => $value) { ?>
	<option value="<?php echo $nb ?>"><?php echo $lang[$types[$nb]] ?></option>
<?php } ?>
</select>
</td>
<td>
<label for="folder"><?php echo $lang['TYPE_FORM_FOLDER_SELECT'] ?></label>
<select name="folder" id="folder">
<?php 
    if (!Auth::Check(get_userlevel_by_name('admin')))
    	$folders_list = $folders_user;
    else
    	$folders_list = $folders;
    foreach ($folders_list as $nb => $name)
     {
?>
	<option value="<?php echo $nb ?>"><?php echo $lang[$name] ?></option>
<?php } ?>
</select>
</td> 
<td><label for="levelset_f"><?php echo $lang['TYPE_FORM_SET'] ?></label>
<select name="levelset_f" id="levelset_f">
<option value="0"><?php echo $lang['all'] ?></option>
   <?php  foreach ($this->table->db->helper->SelectSets() as $id => $name)
      { ?>
    <option value="<?php echo $id ?>"><?php echo $name?></option>
<?php } ?>
</select>
</td>
<td>
<label for="level_f"><?php echo $lang['TYPE_FORM_LEVEL'] ?></label>
<select name="level_f" id="level_f">
<option value="0"><?php echo $lang['all'] ?></option>
<?php  foreach ($levels as $name => $value)
        { ?>
          <option value="<?php echo $value ?>"><?php echo $name ?></option>
  <?php } ?>
</select>
</td>
<td><label for="newonly"><?php echo $lang['TYPE_FORM_NEWONLY'] ?></label>
<select name="newonly" id="newonly">
  
<?php foreach ($newonly as $nb => $name)
{ ?>
  <option value="<?php echo $nb ?>"><?php echo $lang[$name] ?></option>
<?php } ?>  
</select>
</td>
</tr>
</table>
<br />
  
<?php if ($mode_diffview) { ?>
<table><tr>
<th colspan="6"><?php echo $lang['TYPE_FORM_FILTERS'] ?></th>
</tr><tr>
<td>
<center>
<label for="diffview"><?php echo $lang['TYPE_FORM_DIFFVIEW'] ?></label>
<input type="checkbox" name="diffview" id="diffview" value="on" />
</center>
</td>
</tr>
</table>
<?php } ?>
<br />
<center>
 	<input type="submit" value="<?php echo $lang['GUI_BUTTON_APPLY'] ?>" />
</center>
 
</form>

