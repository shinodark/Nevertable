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
 * @param: content Comment content
 * 
 */
if (!defined('NVRTBL'))
	exit;
	
global $lang;

?>
<!-- 
<div class="button" style="width:200px;">
<a href="index.php"><?php echo $lang['COMMENTS_PREVIEW_WARNING'] ?></a>
</div>
<br/>
-->

<div class="comments">
<div class="comments_content">
<table>
<tr><th><?php echo $lang['COMMENTS_PREVIEW_TITLE']?></th></tr>
<tr><td>
<div class="embedded">
<table>
<tr>
<td class="com_content">
<?php echo $this->RenderText($content) ?>
</td>
</tr>
</table>
</div>
</table>
</div>