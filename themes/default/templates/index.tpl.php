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
 * @param: title: page Title
 * @param: special: Special line to add in header
 * 
 * @param: last_comments Last comments poted to display in the sidebar
 */

global $lang, $config, $args, $nextargs, $types_menu;

$langpath = ROOT_PATH . $config['lang_dir'] . $config['opt_user_lang'] . "/";

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

<?php 
if (isset($args['link']))
{
  gui_button("Redirecting...", 100);
}
else
{
?>

<div id="speech">
  <?php
  	$f = new FileManager($langpath . "speech.txt");
    if ($f->Exists())
      echo  $f->ReadString();
  ?>
<br/>
</div>
<div id="announce">
  <?php
    $f = new FileManager($langpath . "announce.txt");
    if ($f->Exists())
      echo  $f->ReadString();
  ?>
<br/>
</div>

<div class="generic_form" style="width: 800px;">
<?php  $this->SubTemplate('_type.form'); ?>
</div>

<?php
    //Calcul de l'index du set pour la liste
    $i = 1;
    foreach ($this->table->db->helper->SelectSets() as $id => $name)
    {
      $ind_set_arr[$id] = $i;
      $i++;
    }
?>
<script type="text/javascript">
change_form_select('type',"<?php echo array_search($args['type'], $types_menu) ?>.");
change_form_select('folder',"<?php echo $args['folder'] ?>");
<?php if ($args['levelset_f'] != 0) { // Tout afficher, premire option ?>
change_form_select('levelset_f','<?php echo $ind_set_arr[$args['levelset_f']] ?>');
<?php } ?>
change_form_select('level_f','<?php echo $args['level_f'] ?>');
change_form_checkbox('diffview','<?php echo $args['diffview']?>');
change_form_select('newonly','<?php echo $args['newonly']?>');
</script>
<?php
}
?>

<div class="results">
<?php $this->SubTemplate('_table.list');?>
<center><?php echo $lang['TABLE_RESULTS_LIST']?><a href="?to=showlinklist" target="_blank">Link_List.lst</a></center>
<div class="navbar">
<?php $this->SubTemplate('_navbar', array('callback' => $nextargs)); ?>
</div>
</div>
<div id="sidebar">
<?php $this->SubTemplate('_sidebar');?>
</div>

</div><!--  main end -->
<div id="stats">
<?php $this->SubTemplate('_stats', array('stats' => $this->table->GetStats()) );?>
</div>
<div id="footer">
<?php $this->SubTemplate('_footer');?>
</div> 
<script src="./includes/js/wz_tooltip/wz_tooltip.js" type="text/javascript"></script>
</div><!-- page end -->
</body>
</html>