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
	
	/*
	 * Tags template
	 * @param: tags db array with tags to display
	 * @param: errors Array of errors to display
	 * @param: moder_enable Enable moderation mode
	 */
		
global $lang, $nextargs;

$enable_moder = Auth::Check(get_userlevel_by_name("moderator"));

?>

<table>
<?php 
$i = 0;
while ($fields = $this->table->db->FetchArray($tags))
{
  $class = ($i % 2) ? "tagmsg1" : "tagmsg2";
  $tag = $fields['content'];
  $tag = $this->bbcode->parse($tag, "all", false);
  $tag = $this->smilies->Apply($tag);
  
  while (($e = array_pop($errors)) != null)
  {
  	?><span class="tag_error"><?php echo $e ?></span> <?php
  }
  ?>
  <tr class="tagheader">
  <td>
  <span class="tag_pseudo"><?php echo $fields['pseudo'] ?></span>
  <span class="tag_date"><?php echo GetDateLang_mini(GetDateFromTimestamp($fields['timestamp'])) ?>
   <?php if ($enable_moder) { ?>
    <a href="<?php echo $nextargs ?>&amp;tagid=<?php echo $fields['id']?>&amp;tagdel">
	  <?php echo $this->table->style->GetImage('del', 'del')?>
	</a>
  <?php } ?> 
  </span>

  </td>
  </tr>
  <tr class="<?php echo $class ?>">
  <td>
  <div class="tagedit" id="<?php echo $fields['id'] ?>">
  <?php echo $tag ?>
  </div>
  </td>
  </tr>
	
  <?php
}
?>
</table>

