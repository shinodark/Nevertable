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
 * @param: redirect URL of redirection
 * @param: delay Timer
 * @param: message_array  Some messages to display while redirecting
 * @param: subtemplates_array Some subtemplates to show
 * @param: subparams_array Parameters of sub templates in same order
 * @param: subdivclass_array Class of div for subtemplates
 */

global $lang, $config;

if (!isset($redirect))
  $redirect = "index.php";

if (!isset($delay))
  $delay=1;


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $lang['code'] ?>" lang="<?php echo $lang['code'] ?>">
<head>
<?php $this->SubTemplate('_head'); ?>
<meta http-equiv="refresh" content="<?php echo ($delay == 0) ? 500 : $delay ?>;URL=<?php echo $redirect ?>" />
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

<?php while ( (count($message_array) > 0) && ($mes = array_shift($message_array)) != null) { ?>
<div class="button" style="width:300px;">
<?php echo $mes ?>
</div>
<?php } ?>

<?php while ( (count($subtemplates_array) > 0) && ($stpl = array_shift($subtemplates_array)) != null)
	 { 
		?>
<div class="<?php echo array_pop($subdivclass_array) ?>">
		<?php $this->SubTemplate($stpl, array_pop($subparams_array)); ?>
</div>
<?php } ?>

<div class="button" style="width:200px;">
<a href="<?php echo $redirect ?>"><?php echo $lang['GUI_BUTTON_CONTINUE'] ?></a>
</div>

</div><!--  main end -->
<div id="footer">
<?php $this->SubTemplate('_footer');?>
</div> 
</div><!-- page end -->
</body>
</html>