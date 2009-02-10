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
		
/* Template html header, nevertbale standard */
/**
 * @param: $fields Replay file info
 */
	
global $lang;
$super_op_enable = false;

$replay = empty($fields['replay']) ? $this->table->style->Getimage("no_replay", "no replay") : $this->table->style->Getimage("replay", "replay") ;

?>

<table>
<tr>
<th style="width: 28px;"></th>
<th style="width: 32px;"></th>
<th style="width: 100px;"><?php echo $lang['TABLE_HEADER_PLAYER'] ?></th>
<th><?php echo $lang['TABLE_HEADER_SET'] ?></th>
<th><?php echo $lang['TABLE_HEADER_LEVEL'] ?></th>
<th><?php echo $lang['TABLE_HEADER_TIME'] ?></th>
<th><?php echo $lang['TABLE_HEADER_COINS'] ?></th>
<th></th>
<th></th>
</tr>
<tr class="row1" onmouseover="return escape('<?php echo Javascriptize(GetShotMini($fields['set_path'], $fields['map_solfile'], 128)) ?>')">
<td><?php if ($fields['isbest']) echo $this->table->style->GetImage('best') ?></td>
<td><?php echo $this->table->style->GetImage(get_type_by_number($fields['type']))?></td>
<td><a href="profile.php?id=<?php echo $fields['user_id'] ?>"><?php echo $fields['pseudo']?></a></td>
<td><?php echo $fields['set_name'] ?></td>
<td><a href="index.php?levelset_f=<?php echo $fields['levelset']."&amp;level_f=".$fields['level']?>"><?php echo $fields['level_name'] ?></a></td>
<td><?php echo sec_to_friendly_display($fields['time'], $sign_display) ?></td>
<td><?php echo $fields['coins'] ?></td>
<td>
<a href="<?php echo replay_link($fields['replay']) ?>" type="application/octet-stream"><?php echo $replay ?></a>
</td>
<td>
<a href="index.php?levelset_f=<?php echo  $fields['levelset']."&amp;level_f=".$fields['level']."&amp;folder=-1" ?>" title="<?php echo $lang['TABLE_ATTACH'] ?>">
  <?php echo $this->table->style->GetImage('attach', $lang['TABLE_ATTACH']) ?>
</a>
</td> 
</tr>
</table>

