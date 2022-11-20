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
	
/**
 * Print table in list mode
 * @param: comments_res DB results of comments
 */
global $config, $lang, $args;

$enable_moder = Auth::Check(get_userlevel_by_name("moderator"));
$i=0;

?>

<?php
while ($val = $this->table->db->FetchArray($comments_res))
{ 	
	$enable_edit  = Auth::CheckUser($val['user_id']);
	
	if (!empty($val['user_avatar']))
	    $avatar_html = '<img src="'.ROOT_PATH.$config['avatar_dir'].'/'.$val['user_avatar'].'" alt="" />';
	else
		$avatar_html = "&nbsp;";
		
?>
<!-- anchor -->
<div class="comment">
<a name="c<?php echo $val['id'] ?>" id="c<?php echo $val['id'] ?>"></a>
<table>
<tr><td>
<!-- comment header -->
<div class="embedded">
<table class="com_header">
<tr>
<td>
<a href="profile.php?id=<?php echo $val['user_id']?>"><?php echo $val['user_pseudo']?></a>
</td>
<td style="text-align: right;"><?php echo GetDateLang(GetDateFromTimestamp($val['timestamp']))?></td>

 <?php if ($enable_moder) { ?>
	<td style="width: 16px;">
	<a href="record.php?id=<?php echo $args['id']?>&amp;comdel&amp;com_id=<?php echo $val['id']?>">
	  <?php echo $this->table->style->GetImage('del', $lang['COMMENTS_POPUP_DELETE'])?>
	</a></td>
<?php } ?>
      
</tr>
</table>
</div>
<!--  end header -->  
</td>
</tr>
<tr>
<td>
<!-- contents -->
<div class="embedded">
<table>
<tr>
<td class="com_avatar"><center><?php echo $avatar_html ?></center></td>
<td class="com_content <?php if ($enable_edit || $enable_moder){echo 'comedit';}?>" id="com_<?php echo $val['id'] ?>">
<?php echo $this->RenderText($val['content']) ?>
</td>
</tr>
</table>
</div>
<!--  end contents -->

</td></tr>
</table>
</div>
<?php
$i++;
} ?>
<?php if ($i == 0)
{
?>
	<div class="button" style="width:200px;">
	<?php echo $lang['COMMENTS_NOCOMMENT'] ?>
	</div>
<?php
}

if ($enable_edit || $enable_moder) { ?>
<script type="text/javascript">
//<![CDATA[
 $(document).ready(function() {
  $(".comedit").editable("ajax/com_edit.php", { 
      indicator : "<img src='themes/default/images/indicator.gif' alt='loading...' />",
      loadurl   : "ajax/com_load.php",
      tooltip   : "Double click to edit...",
      type      : "textarea",
      style  	: "inherit",
      event     : "dblclick",
      submit    : "Edit!"
  });
 });
//]]>
</script>
<?php } ?>