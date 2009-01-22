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
 * Template of the navigation bar
 * @param: page current page
 * @param: nb_pages number of total pages
 * @param: callback to callback next page / prev page events
 */
?>

<?php if ($nb_pages > 1) { ?>
	<?php if ($page > 1) { ?>
			<a href="<?php echo  $callback ?>&amp;page=<?php echo ($page-1).$nextargs?>"><?php echo $this->table->style->GetImage("navbar_l") ?></a>
	<?php } else { ?>
			<?php echo $this->table->style->GetImage("navbar_l") ?>
	<?php } ?>
	
	<?php
	    for ($i=1; $i<=$nb_pages; $i++)
	    {
		      if ($i != $page) { ?>
		         &nbsp;<a href="<?php echo $callback ?>?page=<?php echo $i.$nextargs?>"><?php echo $i ?></a>&nbsp;
		      <?php } else { ?>
		         &nbsp;<b><?php echo $i ?></b>&nbsp;
		      <?php } ?>
		      &nbsp;
	    <?php } ?>
	
	<?php if ($page < $nb_pages) { ?>
			<a href="<?php echo  $callback ?>&amp;page=<?php echo ($page+1).$nextargs?>"><?php echo $this->table->style->GetImage("navbar_r") ?></a>
	<?php } else { ?>
			<?php echo $this->table->style->GetImage("navbar_r") ?>
	<?php } ?>
<?php } ?>