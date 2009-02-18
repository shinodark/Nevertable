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
 * @param: members DB results with member list
 */

global $lang, $config;
$langpath = ROOT_PATH . $config['lang_dir'] . $config['opt_user_lang'] . "/";

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

<center>
<div class="results" style="width:650px; float: none; padding: 5 0 5 0;">
<table style="text-align: center;"><tr>
<th style="text-align: center;"><a href="memberlist.php?sort=0"><?php echo $lang['MEMBER_HEADER_NAME'] ?></a></th>
<th style="text-align: center;"><a href="memberlist.php?sort=1"><?php echo $lang['MEMBER_HEADER_RECORDS'] ?></a></th>
<th style="text-align: center;"><a href="memberlist.php?sort=2"><?php echo $lang['MEMBER_HEADER_BEST_RECORDS'] ?></a></th>
<th style="text-align: center;"><a href="memberlist.php?sort=2"><?php echo $lang['MEMBER_HEADER_RANK'] ?></a></th>
<th style="text-align: center;"><a href="memberlist.php?sort=3"><?php echo $lang['MEMBER_COMMENTS'] ?></a></th>
<th style="text-align: center;"><a href="memberlist.php?sort=4"><?php echo $lang['MEMBER_CATEGORY'] ?></a></th>
</tr>

<?php
    $i = 0;
    while ($fields = $this->table->db->FetchArray($members))
    {
    	$rowclass = ($i % 2) ? "row1" : "row2";
?>
   

<tr class="<?php echo $rowclass ?>">
<td height="20">
 <?php if (!empty($fields['user_avatar']))  {  ?>
<a href="profile.php?id=<?php echo $fields['id'] ?>"
     onmouseover="Tip('<?php echo Javascriptize("<center><img src=\"".ROOT_PATH.$config['avatar_dir']."/".$fields['user_avatar']."\" alt=\"\" /></center>") ?>')"
     onmouseout="UnTip()">    
 <?php echo $fields['pseudo'] ?>
</a>
 <?php } else { ?>
<a href="profile.php?id=<?php echo $fields['id'] ?>"
     onmouseover="Tip('<?php echo Javascriptize("<center><i>".$lang['MEMBER_NO_AVATAR']."</i></center>") ?>')"
     onmouseout="UnTip()">
     <?php echo $fields['pseudo'] ?>
 </a>
 <?php } ?>
</td>
<td>
<?php echo  $fields['stat_total_records'] ?>
</td>
<td>
<?php echo  $fields['stat_best_records'] ?>
</td>
<td>
<?php echo $this->table->style->GetImage('rank_'.CalculRank($fields['stat_best_records']));?>
</td>
<td>
<?php echo  $fields['stat_comments'] ?>
</td>
<td>
<?php echo  get_userlevel_by_number($fields['level']) ?>
</td>
</tr>   
<tr><td colspan="5" style="background: #fff; height: 2px;"></td></tr>
      
<?php
	  $i++;
    }
?>

</table>
</div>
</center>


<div class="button" style="width:200px;">
<a href="index.php"><?php echo $lang['GUI_BUTTON_MAINPAGE'] ?></a>
</div>

</div>

<div id="footer">
<?php $this->SubTemplate('_footer');?>
</div>

<script src="./includes/js/wz_tooltip/wz_tooltip.js" type="text/javascript"></script>
</div><!-- page end -->
</body>
</html>