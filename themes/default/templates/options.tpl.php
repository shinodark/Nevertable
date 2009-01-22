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
	
/* Template for profile page */
/**
 * @param: user User object
 */

global $lang, $config, $sort_type, $themes, $langs;


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
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

<div  class="generic_form" style="width: 600px;">
<form method="post" action="options.php?upoptions" name="options_form" >
<table>
<tr>
<th colspan="2" align="center"><?php echo $lang['OPTIONS_FORM_TITLE'] ?></th></tr>
<tr>
<td><label for="sort"><?php echo $lang['OPTIONS_FORM_SORT'] ?></label></td>
<td>
<select id="sort" name="sort">
<?php
foreach ( $sort_type as $key => $value) { ?>
 <option value="<?php echo $key ?>"><?php echo $value ?></option>'
<?php } ?>
</select>
</td>
</tr>
<tr>
<td><label for="theme"><?php echo $lang['OPTIONS_FORM_THEME'] ?></label></td>
<td>
<select id="theme" name="theme">
<?php
foreach ( $themes as $key => $value) { ?>
 <option value="<?php echo $key ?>"><?php echo $value ?></option>'
<?php } ?>
</select>
</td>
</tr>
<tr>
<td><label for="lang"><?php echo $lang['OPTIONS_FORM_LANG'] ?></label></td>
<td><select id="lang" name="lang">
<?php
foreach ( $langs as $key => $value) { ?>
 <option value="<?php echo $key ?>"><?php echo $value ?></option>'
<?php } ?>
</select>
</td>
</tr>
<tr>
<td><label for="limit"><?php echo $lang['OPTIONS_FORM_LIMIT'] ?></label></td>
<td colspan="1"><input type="text" id="limit" name="limit"  size="5" value="<?php echo $user->GetCommentsLimit() ?>" /></td>
</tr><tr>
<td><label for="comments_limit"><?php echo $lang['OPTIONS_FORM_COMMENTS_LIMIT'] ?></label></td>
<td colspan="1"><input type="text" id="comments_limit" name="comments_limit"  size="5" value="<?php echo $user->GetCommentsLimit() ?>" /></td>
</tr><tr>
<td><label for="sidebar_comments"><?php echo $lang['OPTIONS_FORM_SIDEBAR_COMMENTS'] ?></label></td>
<td colspan="1"><input type="text" id="sidebar_comments" name="sidebar_comments"  size="5" value="<?php echo $user->GetSidebarComments() ?>" /></td>
</tr><tr>
<td><label for="sidebar_comlength"><?php echo $lang['OPTIONS_FORM_SIDEBAR_COMLENGTH'] ?></label></td>
<td colspan="1"><input type="text" id="sidebar_comlength" name="sidebar_comlength"  size="5" value="<?php echo $user->GetSidebarComLength() ?>" /></td>
</tr><tr>
<td colspan="2"><center><input type="submit"  /></center></td>
</tr></table></form>
</div>

<script type="text/javascript">
change_form_select('sort',  '<?php echo $user->GetSort() ?>');
change_form_select('theme',  '<?php echo $user->GetTheme() ?>');
change_form_select('lang',  '<?php echo get_lang_by_name($user->GetLang()) ?>');
</script>

<div class="button" style="width:200px;">
<a href="index.php"><?php echo $lang['GUI_BUTTON_MAINPAGE'] ?></a>
</div>

</div><!--  main end -->
<div id="footer">
<?php $this->SubTemplate('_footer');?>
</div> 
</div><!-- page end -->
</body>
</html>