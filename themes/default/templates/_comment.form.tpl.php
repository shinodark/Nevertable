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
/**
 * @param: fields Fields of a comment if editing
 * 
 */
if (!defined('NVRTBL'))
	exit;
		
global $lang, $toolbar_el, $args;
 
$pseudo = isset($fields['pseudo']) ?  $fields['pseudo'] :  $_SESSION['user_pseudo'] ;

?>
<div id="comment_preview">
</div>

<form method="post" action="record.php?id=<?php echo $args['id']?>&amp;post" name="commentform" id="commentform">
<table><tr>
<td colspan="2"><label for="pseudo"><?php echo $lang['COMMENTS_FORM_PSEUDO']?></label>
<input type="text" id="pseudo" name="pseudo" size="20" value="<?php echo $pseudo ?>" readonly="readonly" /></td>
</tr><tr>
<td colspan="2">
<center>
<textarea id="content" name="content" rows="12" cols="">
</textarea>
</center>
</td>
</tr>
<tr>  
<td colspan="2">
<center>
<input type="button" name="preview" id="preview" value="<?php echo $lang['COMMENTS_FORM_PREVIEW']?>" onclick="$('commentform#submit').css('visiblity', 'visible');" />
<input type="submit" name="submit" id="submit" /></center>
<input type="hidden" name="com_id" id="com_id" value="<?php echo $fields['com_id']?>" />
<input type="hidden" name="id" id="id" value="<?php echo $args['id']?>" /><!--  replay -->
</td>
</tr><tr>
<td>
<script type="text/javascript" src="<?php echo ROOT_PATH."includes/js/toolbar.js"?>"></script>
<script type="text/javascript">setTextArea(document.forms['commentform'].content)</script>
<?php foreach($toolbar_el as $key => $func) { ?>
    <a href="javascript:<?php echo $func?>()"><?php echo $this->table->style->GetIcon($key)?></a>
<?php } ?>
</td>
<td style="text-align: right">
<a href="javascript:child=window.open('./popup_smilies.php?referer_form=commentform', 'Smiles', 'fullscreen=no,toolbar=no,status=no,menubar=no,scrollbars=no,resizable=yes,directories=no,location=no,width=270,height=220,left='+(Math.floor(screen.width/2)-140));child.focus()">
<?php echo $this->table->style->GetImage('smilies') ?>
</a>
</td></tr>
</table>
</form>

<script>

$(document).ready(function() {
  $("#preview").bind("click",
  function() {
    var yurl = "content="+encodeURIComponent($("#content").val());
	$("#comment_preview").load("ajax/comment_preview.php", yurl);
	return false;
  });
});
</script>

<?php if (!empty($fields['content']))  { ?>
 <script type="text/javascript">
   change_form_textarea('content',  '".$content."')
 </script>
<?php
}





