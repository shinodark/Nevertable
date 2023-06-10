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
 * @param: config_res  DB results of config tbale
 */

global $lang, $config;


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
<?php
  $form = new Form("post", "config.php?upconfig", "config_form", 700);
  $form->AddTitle($lang['ADMIN_CONFIG_FORM_TITLE']);
  while ($val = $this->table->db->FetchArray($config_res))
  {
     $option = 'onmouseover="Tip(\''.$val['conf_desc'].'\')" onmouseout="UnTip()" ';
     $form->AddInputText($val['conf_name'], $val['conf_name'], $val['conf_name'], 30, $val['conf_value'], $option);
     $form->Br();
  }
  $form->AddInputSubmit();
  echo $form->End();
?>


<div class="button" style="width:200px;">
<a href="../index.php"><?php echo $lang['GUI_BUTTON_MAINPAGE'] ?></a>
</div>

</div><!--  main end -->
<div id="footer">
<?php $this->SubTemplate('_footer');?>
</div> 
<script src="../public/js/wz_tooltip/wz_tooltip.js" type="text/javascript"></script>
</div><!-- page end -->
</body>
</html>