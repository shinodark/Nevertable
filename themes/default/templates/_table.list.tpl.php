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
 * Print table in list mode
 * @param: records array of db query results
 * @param: diffview active diffview mode for time if true
 * @param: total total number of results
 */
global $nextargs, $args, $lang;
$super_op_enable = false;

$super_op_enable = Auth::Check(get_userlevel_by_name("admin"));
?>


<div class="results-prelude">
  <?php if (!empty($total)) echo sprintf($lang['RESULTS_PRELUDE'], $total) ?>
</div>
<table>
<tr>
<th style="width: 28px;"></th>
<th><a href="<?php echo $nextargs . "&amp;sort=".get_sort_by_name("old") ?>"><?php echo $lang['TABLE_HEADER_OLD'] ?></a></th>
<th style="width: 28px;"></th>
<th style="width: 32px;"></th>
<th style="width: 100px;">
<a href="<?php echo $nextargs."&amp;sort=".get_sort_by_name("pseudo") ?>"><?php echo $lang['TABLE_HEADER_PLAYER'] ?></a></th>
<th><a href="<?php echo $nextargs."&amp;sort=".get_sort_by_name("level") ?>"><?php echo $lang['TABLE_HEADER_SET'] ?></a></th>
<th><a href="<?php echo $nextargs."&amp;sort=".get_sort_by_name("level") ?>"><?php echo $lang['TABLE_HEADER_LEVEL'] ?></a></th>
<th><a href="<?php echo $nextargs."&amp;sort=".get_sort_by_name("time") ?>"><?php echo $lang['TABLE_HEADER_TIME'] ?></a></th>
<th><a href="<?php echo $nextargs."&amp;sort=".get_sort_by_name("coins") ?>"><?php echo $lang['TABLE_HEADER_COINS'] ?></a></th>
<th></th>
</tr>
<?php

   $i = 0;
   $_SESSION['download_list'] = "";
   while ($fields = $this->table->db->FetchArray($records))
   {
      /* keep first record to display diff later */
      if ($diffview && $i==0)
        $diffref = $fields;
        
      //$this->_RecordLine($i, $fields, true, $diffview, $diffref);
      
      $rowclass = ($i % 2) ? "row1" : "row2";
      $days=timestamp_diff_in_days(time(), GetDateFromTimestamp($fields['timestamp']));
      if ($diffview && !empty($diffref) && $i>0)
      {
      	$time = $fields['time'] - $diffref['time'];
      	$sign_display=true;
      }
      else
      {
      	$time = $fields['time'];
      	$sign_display=false;
      }
      if ($diffview && !empty($diffref) && $i>0)
      {
      	$coins = $fields['coins'] - $diffref['coins'];
       	/* ajout du signe + dans le cas du diff, c'est plus joli*/
      	$coins = ($coins<0) ? $coins : "+".$coins;
      }
      else
       	$coins = $fields['coins'];
       	
      $replay = empty($fields['replay']) ? $this->table->style->Getimage("no_replay", "no replay") : $this->table->style->Getimage("replay", "replay") ;
     
      
      if ($fields['comments_count']<1)
         $comments = $this->table->style->Getimage("comments_blank", $lang['TABLE_NO_COMMENT']);
      else if ($fields['comments_count']==1)
         $comments = $this->table->style->Getimage("comments", $lang['TABLE_ONE_COMMENT']);
      else
         $comments = $this->table->style->Getimage("comments", sprintf($lang['TABLE_COMMENTS'] ,$fields['comments_count']));
      ?>
      
      <tr class="<?php echo $rowclass ?>" <?php if ($diffview) { ?>style="font-weight: bold;"<?php } ?>
      		onmouseover="return escape('<?php echo Javascriptize(GetShotMini($fields['set_path'], $fields['map_solfile'], 128)) ?>')">
      <td><?php if ($days<=3) echo $this->table->style->GetImage('new') ?></td>
      <td><?php echo $days?>&nbsp;d</td>
      <td><?php if ($fields['isbest']) echo $this->table->style->GetImage('best') ?></td>
      <td><?php echo $this->table->style->GetImage(get_type_by_number($fields['type']))?></td>
      <td><a href="profile.php?id=<?php echo $fields['user_id'] ?>"><?php echo $fields['pseudo']?></a></td>
      <td><?php echo $fields['set_name'] ?></td>
      <td><a href="index.php?levelset_f=<?php echo $fields['levelset']."&amp;level_f=".$fields['level']?>&amp;folder=0&amp;type=<?php echo $fields['type'] ?>"><?php echo $fields['level'] ?></a></td>
      <td><?php echo sec_to_friendly_display($time, $sign_display) ?></td>
      <td><?php echo $coins ?></td>
      <td>
      <a href="<?php echo replay_link($fields['replay']) ?>" type="application/octet-stream"><?php echo $replay ?></a>
      <a href="record.php?id=<?php echo $fields['id']?>"><?php echo $comments ?></a>
      <a href="index.php?levelset_f=<?php echo  $fields['levelset']."&amp;level_f=".$fields['level']?>&amp;folder=0&amp;type=<?php echo $fields['type']?>" title="<?php echo $lang['TABLE_ATTACH'] ?>">
        <?php echo $this->table->style->GetImage('attach', $lang['TABLE_ATTACH']) ?>
      </a>
      <?php if ($super_op_enable) { ?>
	      <?php if (($fields['folder'] == get_folder_by_name("incoming")) || ($fields['folder'] == get_folder_by_name("trash"))) { ?>
		      <a href="index.php?rectocontest&amp;id=<?php echo $fields['id']?>&amp;folder=<?php echo $args['folder'] ?>">
		      	<?php echo $this->table->style->GetImage('tocontest+', "Reinject in contest, always add");?>
		      </a>
	      <?php } ?>
	      <?php if ($fields['folder'] != get_folder_by_name("trash")) { ?>
		      <a href="index.php?rectotrash&amp;id=<?php echo $fields['id']?>&amp;folder=<?php echo $args['folder'] ?>">
		      	<?php echo $this->table->style->GetImage('trash', "Send to trash");?>
		      </a>
	      <?php } ?>
	      <?php if ($fields['folder'] == get_folder_by_name("trash")) { ?>
		      <a href="index.php?recdelete&amp;id=<?php echo $fields['id']?>&amp;folder=<?php echo $args['folder'] ?>">
		      	<?php echo $this->table->style->GetImage('del', "Delete permanently");?>
		      </a>
	      <?php } ?>       
      <?php } ?>
      </td> 
      </tr>
      <tr><td colspan="12" style="background: #fff; height: 2px;"></td></tr>
      
      <?php
      $_SESSION['download_list'] .=  replay_link($fields['replay']) . "\n";
      $i++;
    }
    if ($i == 0) // No records displayed
    {
        ?>
        <td colspan="12">No results</td>
        <?php
    }
?>
</table>
<br/>
