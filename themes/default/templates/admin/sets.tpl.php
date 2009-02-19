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
 * @param: 
 */

global $lang, $config;

$res = $this->table->db->helper->SelectSetsRes();
$sets = $this->table->db->helper->SelectSets();
$maps = $this->table->db->helper->SelectMapsName();


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<?php $this->SubTemplate('_head'); ?>
<script type="text/javascript" src="../includes/js/jquery-1.3.min.js" charset="utf-8"></script>
<script type="text/javascript" src="../includes/js/jquery.jeditable.mini.js" charset="utf-8"></script>
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


<br/>
<div style="margin-left: auto; margin-right: auto; width: 550px;">
<table>
<caption>Sets</caption>


<?php
$i = 0;
while ($val = $this->table->db->FetchArray($res))
{
   $rowclass=($i%2)?"row1":"row2"; $i++;
?>
   <tr class="<?php echo $rowclass ?>">
   <td>#<?php echo$val['id']?></td>
   <td><?php echo $val['set_shortname']?></td>
   <td><?php echo $val['set_path']?></td>
   <td style="text-align: right;">
      <form name="setform_<?php echo $i?>" id="setform_<?php echo $i?>" action="sets.php?setaction" method="post">
      <input type="text" name="newname" value="<?php echo $val['set_name']?>" size="30" />
      <input type="hidden" name="set_id" value="<?php echo $val['id']?>" />
      <input type="submit" value="rename" name="rename"  />
      <input type="submit" value="delete" name="delete" />
      </form>
   </td>
   </tr>
<?php 
} ?>
</table>
</div>

<div  class="generic_form" style="width: 400px;">
<form method="post" action="?setadd" name="setadd_form" id="setadd_form"  enctype="multipart/form-data">
<table><tr>
<th colspan="2" align="center">Add a new set from set file</th></tr>
<tr>
<td><label for="setfile">set file : </label></td>
<td colspan="1"><input type="file" id="setfile" name="setfile"  size="25" /></td>
<td colspan="2"><input type="hidden" id="size_max" name="MAX_FILE_SIZE"  value="50000" /></td>
</tr><tr>
<td colspan="2"><center><input type="submit"  /></center></td>
</tr></table></form>
</div>


<div style="margin-left: auto; margin-right: auto; width: 300px;">
<table>
<caption>Maps</caption>

<?php
$i = 0;
foreach ($sets as $id => $name)
{
?>
    <tr style="background: white;">
    <td colspan="4">
    <br/>
    <b><a name="s<?php echo $id ?>">
    <?php echo $name ?>
    </a>
    </b>
    <br/>
    <hr/>
    </td>
    </tr>
    <tr>
    <td colspan="4" style="background: #fff; height: 2px;">
    </td>
    </tr>

<?php
    $set = new Set($this->table->db);
    $set->LoadFromId($id);
    $res = $set->GetMapsRes();
    while ($val = $this->table->db->FetchArray($res))
    {
       $rowclass=($i%2)?"row1":"row2"; $i++;
?>
       <tr class="<?php echo $rowclass?>">
       <td>#<?php echo $val['level_num']?></td>
       <td><?php echo $val['map_solfile']?></td>
       <td class="map_name_edit" id="map_<?php echo $val['id'] ?>"><?php echo $maps[$id][$val['level_num']] ?></td>
       </tr>
<?php
    }
}
?>
</table>
</div>

<script type="text/javascript">
 $(document).ready(function() {
  $(".map_name_edit").editable("../ajax/map_name_edit.php", { 
      tooltip   : "Double click to edit...",
      type      : "textarea",
      style  	: "inherit",
      event     : "dblclick",
      submit    : "Edit!"
  });
 });
</script>

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