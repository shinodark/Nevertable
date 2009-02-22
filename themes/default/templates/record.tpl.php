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
	
/* Template record diplay */
/**
 * @param: record_fields Info of record 
 * @param: comments_res DB results of comments
 */

global $lang, $config, $args;

$enable_post = Auth::Check(get_userlevel_by_name("member"));

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $lang['code'] ?>" lang="<?php echo $lang['code'] ?>">
<head>
<?php $this->SubTemplate('_head'); ?>
<script type="text/javascript" src="includes/js/jquery-1.3.min.js" charset="utf-8"></script>
<script type="text/javascript" src="includes/js/jquery.jeditable.mini.js" charset="utf-8"></script>
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

<div class="oneresult">
<?php $this->SubTemplate('_record', array('fields' => $record_fields)); ?>
</div>
      
<div class="embedded">
<br/>
<?php $bbcode =  "[url=http://".$_SERVER['SERVER_NAME'] ."/".$config['nvtbl_path'] . "?link=".$record_fields['id']."]" .
             $lang[get_type_by_number($record_fields['type'])] ." by ".$record_fields['pseudo']." on ". $record_fields['set_name'] . " " . $record_fields['level_name'].
     "[/url]";
?>
<center>
<input type="text" size="<?php echo strlen($bbcode) ?>" value="<?php echo $bbcode ?>" readonly="readonly" />
</center>
<br/>
</div>

<div id="comments">
<?php $this->SubTemplate('_comments'); ?>
</div>

<div class="navbar">
<?php $this->SubTemplate('_navbar', array('callback' => 'record.php?id='.$record_fields['id']));	?>
</div>

<?php if ($enable_post) { ?>
<div class="generic_form" style="width: 650px;">
<?php $this->SubTemplate('_comment.form') ?>
</div>
<?php
} 
else
{ ?>
	<div class="button" style="width:300px;">
	<?php echo $lang['LOGIN_TO_POST'] ?>
	</div>
<?php
} ?>


<div class="button" style="width:200px;">
<a href="index.php"><?php echo $lang['GUI_BUTTON_MAINPAGE'] ?></a>
</div>

</div><!--  main end -->
<div id="footer">
<?php $this->SubTemplate('_footer');?>
</div> 
<script src="./includes/js/wz_tooltip/wz_tooltip.js" type="text/javascript"></script>
</div><!-- page end -->
</body>
</html>