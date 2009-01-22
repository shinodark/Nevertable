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
 * @param: $replay_struct Replay file info
 */
?>


<table>
<caption>Replay file info</caption>
<tr>
<th>timer</th>
<th>coins</th>
<th>state</th>
<th>mode</th>
<th>player</th>
<th>date</th>
<th>solfile</th>
</tr>
<tr>
<td><?php echo $replay_struct["timer"] ?></td>
<td><?php echo $replay_struct["coins"] ?></td>
<td><?php echo get_replay_state_by_number($replay_struct["state"]) ?></td>
<td><?php echo get_replay_mode_by_number($replay_struct["mode"]) ?></td>
<td><?php echo $replay_struct["player"] ?></td>
<td><?php echo $replay_struct["date"] ?></td>
<td><?php echo $replay_struct["solfile"] ?></td>
</tr>
</table>
