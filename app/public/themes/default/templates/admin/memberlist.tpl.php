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
/*TODO: HTML Valid */

global $lang, $config, $userlevel, $lang;


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

<center>
<div class="results" style="width:100%; float: none;">
<table style="text-align: center;">
<caption><?php echo  $lang['ADMIN_MEMBERS_TITLE']?></caption>
<tr>
<th style="text-align: center;"><a href="memberlist.php?sort=5">#</a></th>
<th style="text-align: center;"><a href="memberlist.php?sort=0"><?php echo $lang['MEMBER_HEADER_NAME']?></a></th>
<th style="text-align: center;"><a href="memberlist.php?sort=1"><?php echo $lang['MEMBER_HEADER_RECORDS']?></a></th>
<th style="text-align: center;"><a href="memberlist.php?sort=2"><?php echo $lang['MEMBER_HEADER_BEST_RECORDS']?></a></th>
<th style="text-align: center;"><a href="memberlist.php?sort=3"><?php echo $lang['MEMBER_COMMENTS']?></a></th>
<th style="text-align: center;"><?php echo $lang['MEMBER_MAIL']?></th>
<th style="text-align: center;"><a href="memberlist.php?sort=4"><?php echo $lang['MEMBER_CATEGORY']?></a></th>
<th></th>
<th></th>
</tr>
<?php
    $i=0;
    while ($fields = $this->table->db->FetchArray($mysql_results))
    {
    	$rowclass = ($i % 2) ? "row1" : "row2";
?>
<tr>
<td colspan="7" style="background: #fff; height: 1px;">
</td>
</tr>
<tr class="<?php echo $rowclass?>">
<td>
<a href="editprofile.php?id=<?php echo $fields['id']?>"><?php echo $fields['id']?></a>
</td>
<td>
<form id="memberform_name_<?php echo $fields['id']?>" method="post" action="memberlist.php?upmembername&amp;id=<?php echo $fields['id']?>">
<input type="text" name="pseudo" value="<?php echo $fields['pseudo']?>" size="15" />
<input type="submit" value="<?php echo $lang['ADMIN_MEMBERS_FORM_UPDATE']?>" />
</form>
</td>
<td>
<?php echo $fields['stat_total_records']; ?>
</td>
<td>
<?php echo $fields['stat_best_records']; ?>
</td>
<td>
<?php echo $fields['stat_comments'];?>
</td>
<td>
<a href="mailto:<?php echo $fields['email']?>"><?php echo $fields['email']?></a>
</td>
<td>
<form id="memberform_auth_<?php echo $fields['id']?>" method="post" action="memberlist.php?upmemberauth&amp;id=<?php echo $fields['id']?>">
<select name="authlevel" id="authlevel_<?php echo $fields['id']?>">
<?php 
    $level_index = 0;
    foreach ($userlevel as $nb => $value)
    { ?>
    <option value="<?php echo $nb ?>"><?php echo $userlevel[$nb]?></option>
<?php
      if ($nb < $fields['level'])
         $level_index++;
    } ?>
</select>
<input type="submit" value="<?php echo $lang['ADMIN_MEMBERS_FORM_UPDATE']?>" />
</form>
<script type="text/javascript">
  change_form_select('authlevel_<?php echo $fields['id']?>', '<?php echo $level_index ?>' );
</script>
</td>
<td>
</td>
<td>
<form name="memberdelete_del_<?php echo $fields['id']?>" method="post" action="memberlist.php?delmember&amp;id=<?php echo $fields['id']?>">
<input type="submit" value="<?php echo $lang['ADMIN_MEMBERS_FORM_DELETE']?>" />
</form>
</td>
</tr>
<?php
      $i++;
    }
?>
</table>
</div>
</center>

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
