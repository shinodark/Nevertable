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
 * @param: stats: Statistics
 */
global $config, $lang;
?>
    
<span class="st_label"><?php echo $lang['STATS_NUMBERS_LABEL'] ?></span>
<span class="st_text"><?php echo sprintf($lang['STATS_NUMBERS_TEXT'], $stats['registered'], $stats['guests']) ?></span>
<br/>
<?php if ($stats['registered'] > 0) {  ?>
	<span class="st_label"><?php echo $lang['STATS_LIST'] ?></span>
	<span class="st_text">&nbsp;
	<?php
	    for ($i=0; $i<$stats['registered']; $i++)
	    {
		  echo $stats['registered_list'][$i];
		  if ($i != $stats['registered']-1) echo ', ';
	    }
	?>
<?php } ?>
</span>
