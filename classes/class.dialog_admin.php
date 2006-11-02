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

class DialogAdmin
{
  var $db;
  var $navbar_cache;
  var $smilies;
  var $bbcode;
  var $style;
    
  /*__CONSTRUCTEUR__*/
  function DialogAdmin(&$db, $o_style)
  {
    $this->db = &$db;
    $this->smilies = new Smilies();
    $this->bbcode = new parse_bbcode();
    $this->style = $o_style;
  }

  function HtmlHead($title, $special="")
  {
    echo "<head>\n";
    echo "<title>$title</title>\n";
    echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\"/>\n";
    echo "<link rel=\"stylesheet\" href=\"".$this->style->GetCss()."\" type=\"text/css\" />\n";
   /* echo "<link rel=\"shortcut icon\" href=\"".ROOT_PATH."favicon.ico\" />\n"; */
    echo "<script type=\"text/javascript\" src=\"".ROOT_PATH."includes/js/jsutil.js\"></script>\n";
    echo $special; 
    echo "</head>\n\n";
  }

  function Top()
  {
    echo "<div id=\"top\">\n";  
    echo "<center>";
    echo  "<a href=\"http://www.nevercorner.net/table/\">".$this->style->GetImage('top')."</a>";
    echo "</center>";
    echo  "</div>\n\n";
  }


  function Prelude()
  {
    echo  "<div id=\"prelude\">\n";
    echo  "<center>\n";
    echo  "<a href=\"?folder=".get_folder_by_name("incoming")."\">";
    echo  $this->db->RequestCountRecords(get_folder_by_name("incoming"), get_type_by_name("all"));
    echo  "&nbsp;records in incoming</a> &nbsp; | &nbsp; \n";
    echo  "<a href=\"?folder=".get_folder_by_name("trash")."\">";
    echo  $this->db->RequestCountRecords(get_folder_by_name("trash"), get_type_by_name("all"));
    echo  "&nbsp;records in trash</a> &nbsp; | &nbsp; \n";
    echo  "<a href=\"?folder=".get_folder_by_name("oldones")."\">";
    echo  $this->db->RequestCountRecords(get_folder_by_name("oldones"), get_type_by_name("all"));
    echo  "&nbsp;obsolete records</a> &nbsp; | &nbsp; \n";
    echo  "<a href=\"?folder=".get_folder_by_name("contest")."\">";
    echo  $this->db->RequestCountRecords(get_folder_by_name("contest"), get_type_by_name("all"));
    echo  "&nbsp;records in contest</a>\n";
    echo  "</center>\n";
    echo  "</div>\n";
  }
  
  function Speech()
  {
  }
  
  function NavBar($page, $limit, $total)
  {
    global $nextargs;

    if(isset($this->navbar_cache))
    {
        echo $this->navbar_cache;
        return;
    }

    $page = (empty($page)) ? 1 : $page;
    $off = (empty($page)) ? 0 : ($page-1)*$limit;
  
    $str  = "<div class=\"navbar\">\n";
    $str .= "<center>\n";
  
    $pages  = ceil($total / $limit);
  
    if ($off > 0)
      $str .= "<a href=\"".$nextargs."&amp;page=".($page-1)."\">prev</a>\n";
    else
      $str .= "prev\n";

    $str .= "&nbsp;|&nbsp;";
 
    for ($i=1; $i<=$pages; $i++)
    {
      if ($i != $page)  
         $str .= "<a href=\"".$nextargs."&amp;page=".$i."\">".$i."</a>";
      else
         $str .= "<b>" . $i . "</b>";
      $str .= "&nbsp;\n";
    }

    $str .= "&nbsp;|&nbsp;";
    if ($off+$limit <= $total)
      $str .= "<a href=\"".$nextargs."&amp;page=".($page+1)."\">next</a>\n";
    else
      $str .= "next\n";
    
    $str .= "</center>\n";
    $str .= "</div>\n";
    $this->navbar_cache = $str;
    echo $str;
  }

  function SideBar()
  {
    $bar = new SideBar();

    $menu_main = new Menu();
    $menu_main->AddItem("Check database", "admin.php?to=check");
    $menu_main->AddItem("Re-compute everything", "admin.php?to=recompute");
    $menu_main->AddItem("Configuration", "config.php");
    $menu_main->AddItem("Management", "management.php");
    $menu_main->AddItem("Members List", "admin.php?to=memberlist");
    $menu_main->AddItem("File explorer", "filexplorer.php");
    $menu_main->AddItem("Purge trash", "purgetrash.php");
    $menu_main->AddItem("Tagboard moderation", "javascript:child=window.open('/shinotag/moder2.php', 'Tag moderation', 'fullscreen=no,toolbar=no,status=no,menubar=no,scrollbars=no,resizable=yes,directories=no,location=no,width=270,height=200,left='+(Math.floor(screen.width/2)-140));child.focus()");
    $menu_main->AddItem("Leave admin panel", "../index.php");

    $bar->AddBlock_MenuBar("Admin Menu", $menu_main);
    
    $bar->End();
  }
  
  function Footer($version, $time="")
  {
    echo  "<div id=\"stats\">\n";
    if (!empty($time))
        echo  "Page generated in ".$time."s using ".$this->db->GetReqCount()." queries\n";
    else
        echo  "Page generated using ".$this->db->GetReqCount()." queries\n";
    echo  "</div>\n\n";
 
    echo  "<div id=\"footer\">\n";
    echo  "nevertable ".$version." powered by <a href=\"http://shinobufan.free.fr/dotclear\">shino</a>\n";
    echo  "</div>\n\n";
    
    /* wz_tooltip */
    echo "<script type=\"text/javascript\" src=\"".ROOT_PATH."includes/js/wz_tooltip.js\"></script>\n";
  }


  function Table($mysql_results, $args)
  {
    global $nextargs;
    
    echo "<div class=\"results-prelude\">\n";
    echo "active folder: ".get_folder_by_number($args['folder'])."&nbsp;(".GetFolderDescription($args['folder']).")\n";
    echo "</div>\n";
    echo "<div class=\"results\">\n";
  
    echo "<table>\n";
    echo "<tr><th style=\"width: 16px;\"></th>\n"; // select
    echo "<th><a href=\"".$nextargs."&amp;sort=id\">id</a></th>\n"; //id
    echo "<th style=\"width: 28px;\"></th>\n";     //days
    echo "<th style=\"width: 28px;\"></th>\n";     //best
    echo "<th style=\"width: 28px;\"></th>\n";     //type
    echo "<th><a href=\"".$nextargs."&amp;sort=pseudo\">player</a></th>\n";
    echo "<th><a href=\"".$nextargs."&amp;sort=level\">set</a></th>\n";
    echo "<th><a href=\"".$nextargs."&amp;sort=level\">lvl</a></th>\n";
    echo "<th><a href=\"".$nextargs."&amp;sort=time\">time</a></th>\n";
    echo "<th><a href=\"".$nextargs."&amp;sort=coins\">cns</a></th>\n";
    echo "<th>replay</th>\n";
    echo "<th style=\"width: 16px;\"></th>\n";    // delete icon
    if ($args['folder'] != get_folder_by_name("contest"))
      echo "<th style=\"width: 16px;\"></th>\n";    // undo icon
    if ($args['folder'] == get_folder_by_name("incoming"))
      echo "<th style=\"width: 16px;\"></th>\n";    // undo icon avec overwrite
    echo "<th></th>\n"; // goto same records
    echo "</tr>\n";

    $i=0;
    while ($fields = $this->db->FetchArray($mysql_results))
    {
      /*echo "<tr><td colspan=\"12\" style=\"background: #fff; height: 1px;\"></td></tr>\n";*/
      $this->_RecordLine($i, $fields);
      $this->_JumpLine(14);
      $i++;
    }
    echo "</table>\n";
    echo "</div>\n\n";
  }

  function Level($results_contest, $results_oldones, $args, $total1="", $total2="")
  {
  }

  function Record($fields)
  {
    echo "<div class=\"oneresult\">\n";
    echo "<table><tr>\n";
    echo "<tr><th style=\"width: 16px;\"></th>\n"; // select
    echo "<th><a href=\"".$nextargs."&amp;sort=id\">id</a></th>\n"; //id
    echo "<th style=\"width: 28px;\"></th>\n"; // days
    echo "<th style=\"width: 28px;\"></th>\n"; // best
    echo "<th style=\"width: 32px;\"></th>\n"; // type
    echo "<th style=\"width: 100px;\">player</th>\n"; // player
    echo "<th>set</th>\n";
    echo "<th>lvl</th>\n";
    echo "<th>time</th>\n";
    echo "<th>cns</th>\n";
    echo "<th></th>\n"; // replay
    echo "<th style=\"width: 16px;\"></th>\n";    // delete icon
    if ($fields['folder'] != get_folder_by_name("contest"))
      echo "<th style=\"width: 16px;\"></th>\n";    // undo icon
    echo "<th></th>\n"; // goto same records
    echo "</tr>\n";

    $this->_RecordLine(0, $fields);
    
    echo "</table>\n";
    echo "</div>\n";
  }

  function RecordLink($replay_id)
  {
    
  }

  function Comments($mysql_results, $date_format)
  {
  }

  function MemberList($mysql_results)
  {
    echo "<center>\n";
    echo "<div class=\"results\" style=\"width:100%; float: none;\">\n";
    echo "<table style=\"text-align: center;\"><tr>\n";
    echo "<th style=\"text-align: center;\"><a href=?to=memberlist&sort=id>#</a></th>\n";
    echo "<th style=\"text-align: center;\"><a href=?to=memberlist&sort=pseudo>Name</a></th>\n";
    echo "<th style=\"text-align: center;\"><a href=?to=memberlist&sort=records>Records number</a></th>\n";
    echo "<th style=\"text-align: center;\"><a href=?to=memberlist&sort=best>Best records</a></th>\n";
    echo "<th style=\"text-align: center;\"><a href=?to=memberlist&sort=comments>Comments</a></th>\n";
    echo "<th style=\"text-align: center;\">Mail</th>\n";
    echo "<th style=\"text-align: center;\"><a href=?to=memberlist&sort=cat>Auth Level</a></th>\n";
    echo "<th></th>\n"; // update
    echo "<th></th>\n"; // delete
    echo "</tr>\n";
    $i=0;
    while ($fields = $this->db->FetchArray($mysql_results))
    {
      echo "<tr><td colspan=\"7\" style=\"background: #fff; height: 1px;\"></td></tr>\n";
      $this->_MemberLine($i, $fields);
      $i++;
    }
    echo "</table>\n";
    echo "</div>\n";
    echo "</center>\n";
  }
  
  function UserProfile($o_user)
  {
    $pseudo = $o_user->GetPseudo();
    $level  = $o_user->GetLevel();
    $avatar = $o_user->GetAvatarHtml();
    $speech = $o_user->GetSpeech();
    $speech = $this->bbcode->parse($speech, "all", false);
    $speech = $this->smilies->Apply($speech);

    $records = $o_user->GetTotalRecords();
    $records_best = $o_user->GetBestRecords();
    $records_besttime = $o_user->CountBestRecords_WithType(get_type_by_name("best time"));
    $records_mostcoins = $o_user->CountBestRecords_WithType(get_type_by_name("most coins"));
    $records_freestyle = $o_user->CountBestRecords_WithType(get_type_by_name("freestyle"));
    $comments = $o_user->GetComments();

    $location = $o_user->GetLocalisation();
    $web = $o_user->GetWebHtml();

    
    echo "<div class=\"nvform\" style=\"width: 700px;\">\n";
    echo "<table><th colspan=\"2\">Profile ".$pseudo."</th>\n";
    echo "<tr><td>\n";

    /* table interne de pagination */
    echo "<div class=\"embedded\">\n";
    echo "<table>\n";
    echo "<tr><td width=130px valign=\"top\">\n";
    echo "<center>".$avatar."</center>\n";
    echo "<br/>";
    echo "<center>".get_userlevel_by_number($level)."</center>\n";
    echo "<br/>\n";
    echo "<center>";
    for($i=0; $i<CalculRank($records); $i++)
      echo $this->style->GetImage('rank');
    echo "</center>\n";
    echo "</td><td>\n";
    
    /* infos */
    echo "<table>\n";
    echo "<tr>\n";
    echo "<td class=\"row2\" width=\"150px\">Localisation  </td>\n";
    echo "<td class=\"row1\">".$location."</td>\n";
    echo "</tr>\n";
    echo "<tr>\n";
    echo "<td class=\"row2\">Web </td>\n";
    echo "<td class=\"row1\">".$web."</td>\n";
    echo "</tr>\n";
    echo "<tr>\n";
    echo "<td class=\"row2\">Quote </td>\n";
    echo "<td class=\"row1\">".$speech."</td>\n";
    echo "</tr>\n";
    echo "</table>\n";
    /* fin infos */
    
    echo "<br/>\n";

    /* stats */
    echo "<table>\n";
    echo "<tr>\n";
    echo "<td class=\"row2\" width=\"150px\">Total Records </td>\n";
    echo "<td class=\"row1\">".$records."</td>\n";
    echo "</tr>\n";
    echo "<tr>\n";
    echo "<td class=\"row2\">Best Records </td>\n";
    echo "<td class=\"row1\">".$records_best."</td>\n";
    echo "</tr>\n";
    echo "<tr>\n";
    echo "<td class=\"row2\">Best Time</td>\n";
    echo "<td class=\"row1\">";
    for ($i=0; $i<$records_besttime; $i++)
      echo $this->style->GetImage('best time');
    echo "</td>\n";
    echo "</tr>\n";
    echo "<tr>\n";
    echo "<td class=\"row2\">Most coins</td>\n";
    echo "<td class=\"row1\">";
    for ($i=0; $i<$records_mostcoins; $i++)
      echo $this->style->GetImage('most coins');
    echo "</td>\n";
    echo "</tr>\n";
    echo "<tr>\n";
    echo "<td class=\"row2\">Freestyle</td>\n";
    echo "<td class=\"row1\">";
    for ($i=0; $i<$records_freestyle; $i++)
      echo $this->style->GetImage('freestyle');
    echo "</td>\n";
    echo "</tr>\n";
    echo "<tr>\n";
    echo "<td class=\"row2\">Comments </td>\n";
    echo "<td class=\"row1\">". $comments . "</td>\n";
    echo "</tr>\n";
    echo "<tr>\n";
    echo "<td colspan=\"2\">\n";
    echo "</td>\n";
    echo "</tr>\n";
    echo "</table>\n";
    /* fin stats */

    echo "</td></tr>\n";
    echo "<tr><td colspan=\"2\">\n";

    echo "<center>";
    echo "<a href=\"".ROOT_PATH."index.php?folder=-1&filter=pseudo&filterval=".$pseudo." \">View all records of ".$pseudo."</a>\n";
    echo "<br/><a href=\"".ROOT_PATH."index.php?folder=".get_folder_by_name("contest")."&filter=pseudo&filterval=".$pseudo." \">View all records (contest only)</a>\n";
    echo "</center>\n";
    
    echo "</td></tr>\n";
    echo "</table>\n";
    echo "</div>\n";
    /* fin table interne pagination */

    echo "</td></tr>\n";
    echo "</table>\n";
    echo  "</div>\n";
  }

  function CommentForm($replay_id, $content_memory, $user_id="")
  {
  }
  
  function TypeForm($args)
  {
    global $types, $levelsets, $levels, $folders, $newonly;
  
    echo  "<div class=\"nvform\" style=\"width: 700px;\">\n";
    echo  "<form method=\"post\" action=\"?\" name=\"typeform_admin\">\n";
    echo  "<table><tr>\n";
    echo  "<td><label for=\"table\">table select : </label>\n";
    echo  "<select name=\"type\" id=\"table\">\n";
  
    foreach ($types as $nb => $value)
      echo  "<option value=\"".$nb."\">".$types[$nb]["name"]."</option>\n";
  
    echo  "</select></td>\n";
  
    echo  "<td><label for=\"folder\">folder select : </label>\n";
    echo  "<select name=\"folder\" id=\"folder\">\n";
  
    foreach ($folders as $nb => $value)
      echo  "<option value=\"".$nb."\">".$folders[$nb]["name"]."</option>\n";
  
    echo  "</select></td>\n";
    echo  "</tr><tr><td></td></tr><tr>\n";
  
    echo  "<td><label for=\"bestonly\">Print only best records </label>\n";
    echo  "<input type=\"checkbox\" name=\"bestonly\" id=\"bestonly\" value=\"on\" /></td>\n";
    echo  "<td><label for=\"bestonly\">Print only new records </label>\n";
    echo  "<select name=\"newonly\" id=\"newonly\">\n";
  
    foreach ($newonly as $nb => $name)
      echo  "<option value=\"".$nb."\">".$name."</option>\n";
  
    echo  "</select></td>\n";
    echo  "</tr><tr><td></td></tr><tr>\n";

    echo  "<td><label for=\"levelset_f\">levelset: </label>\n";
    echo  "<select name=\"levelset_f\" id=\"levelset_f\">\n";
    echo  "<option value=\"0\">all</option>\n";
    foreach ($levelsets as $set => $value)
    {
      echo  "<option value=\"".$set."\">".$levelsets[$set]["name"]."</option>\n";
    }
    echo  "</select></td>\n";
    echo  "<td><label for=\"level_f\">level: </label>\n";
    echo  "<select name=\"level_f\" id=\"level_f\">\n";
    echo  "<option value=\"0\">all</option>\n";
    foreach ($levels as $name => $value)
    {
      echo  "<option value=\"".$value."\">".$name."</option>\n";
    }
    echo  "</select></td>\n";
    echo  "</tr><tr><td></td></tr><tr>\n";
  
    echo  "<td colspan=\"4\"><center><input type=\"submit\" value=\"Change\" /></center>\n";
    echo  "</td></tr></table>\n";
    echo  "</form>\n";
    echo  "</div>\n\n";
    echo "<script type=\"text/javascript\">update_typeform_fields_admin(".$args['type'].",".$args['folder'].",\"".$args['bestonly']."\",".$args['newonly'].",".$args['levelset_f'].",".$args['level_f'].")</script>\n";

  }
  
  function EditForm()
  {
    global $types,$levelsets, $levels, $nextargs;
    
    echo  "<div class=\"nvform\" style=\"width: 600px;\">\n";
    echo  "<form method=\"post\" action=\"".$nextargs."\" name=\"editform\">\n";
    echo  "<table><tr>\n";
    echo  "<th colspan=\"6\">Record Editor</th></tr><tr>\n";
    echo  "<td><label for=\"id\">id:</label></td>\n";
    echo  "<td><input type=\"text\" id=\"id\" name=\"id\" size=\"3\" readonly />\n";
    echo  "</td></tr><tr>\n";
    /* pseudo est donné à titre indicatif, non utilisé */
    echo  "<td><label for=\"pseudo\">pseudo: </label></td>\n";
    echo  "<td><input type=\"text\" id=\"pseudo\" name=\"pseudo_info\" size=\"15\" readonly /></td>\n";
    echo  "<td><label for=\"user_id\"> # </label></td>\n";
    echo  "<td><input type=\"text\" id=\"user_id\" name=\"user_id\" size=\"5\" readonly /></td><td colspan=\"2\"></td></tr>\n<tr>";
    echo  "<td><label for=\"levelset\">levelset: </label></td>\n";
    echo  "<td><select name=\"levelset\" id=\"levelset\">\n";
  
    foreach ($levelsets as $set => $value)
    {
      echo  "<option value=\"".$set."\">".$levelsets[$set]["name"]."</option>\n";
    }
  
    echo  "</select></td>\n";
    echo  "<td><label for=\"level\">level: </label></td>\n";
    echo  "<td><select name=\"level\" id=\"level\">\n";
  
    foreach ($levels as $name => $value)
    {
      echo  "<option value=\"".$value."\">".$name."</option>\n";
    }
  
    echo  "</select>\n</td>\n";
    echo  "<td><label for=\"type\">type: </label></td>\n";
    echo  "<td><select id=\"type\" name=\"type\">\n";
  
    foreach ($types as $nb => $value)
    {
      if ($types[$nb]["name"] != "all")
          echo  "<option value=\"".$nb."\">".$types[$nb]["name"]."</option>\n";
    }
  
    echo  "</select>\n</td></tr><tr>\n";

    echo  "<td colspan=\"3\"><label for=\"time\">time(s), >=9999 if goal not reached: </label></td>\n";
    echo  "<td><input type=\"text\" id=\"time\" name=\"time\" size=\"7\" /></td>\n";
    echo  "<td><label for=\"coins\">coins: </label></td>\n";
    echo  "<td><input type=\"text\" id=\"coins\" name=\"coins\" size=\"5\"/></td></tr>\n<tr>";
    echo  "<td><label for=\"replay\">replay file: </label></td>\n";
    echo  "<td colspan=\"5\"><input type=\"text\" id=\"replay\" name=\"replay\" size=\"50\" />\n";
  
    echo  "</td></tr><tr>\n";

    echo  "<td colspan=\"2\"><center><input type=\"submit\" value=\"Modify !\" /></center></td>\n";
    echo  "<td colspan=\"2\"><center><input type=\"reset\" value=\"Clear form\" /></center></td>\n";
    echo  "<td><input type=\"hidden\" id=\"to\" name=\"to\" value=\"edit\" /></td>\n";
    echo  "</tr>\n";
  
    echo  "</table>\n";
    echo  "</form>\n";
    echo  "</div>\n\n";
  }
  
  function AddFormAuto()
  {
  }
  
  function UploadForm($size_limit)
  {
    global $nextargs;
  
    echo  "<div class=\"nvform\"  style=\"width: 600px;\" >\n";
    echo  "<form enctype=\"multipart/form-data\" action=\"".$nextargs."&amp;to=upload2\" method=\"POST\">\n";
    echo  "<table><tr>\n";
    echo  "<td><input type=\"hidden\" name=\"MAX_FILE_SIZE\" value=\"".$size_limit."\" />\n";
    echo  "<td><label for=\"uploadfile\">Upload a replay file : </label></td>\n";
    echo  "<td><input name=\"uploadfile\" type=\"file\" /></td>\n";
    echo  "<td><input type=\"submit\" value=\"Send File\" /></td>\n";
    echo  "</tr></table>\n";
    echo  "</form>\n";
    echo  "</div>\n\n";
  }


  
  /*__METHODES PRIVESS__*/

  function _RecordLine($i, $fields)
  {
    global $nextargs, $users_cache;

    $tooltip=Javascriptize(GetShotMini($fields['levelset'], $fields['level'], 128));

    $rowclass = ($i % 2) ? "row1" : "row2";
    echo  "<tr class=\"".$rowclass."\" onmouseover=\"return escape('".$tooltip."')\">\n";

    // select
    if (!empty($fields['user_id'])) $pseudo = $users_cache[$fields['user_id']];
    else $pseudo = $fields['pseudo'];

    $jsargs  = "'".$fields['id']."',";
    $jsargs .= "'".$fields['user_id']."',";
    $jsargs .= "'".$pseudo."',";
    $jsargs .= "'".$fields['levelset']."',";
    $jsargs .= "'".get_level_by_name($fields['level'])."',";
    $jsargs .= "'".$fields['time']."',";
    $jsargs .= "'".$fields['coins']."',";
    $jsargs .= "'".$fields['replay']."',";
    $jsargs .= "'".$fields['type']."'";
    echo  "<td style=\"width:16px;\">".$this->style->GetImage('arrow', "", "", "onclick=\"change_editform(".$jsargs.")\"")."</td>\n";

    //id
    echo  "<td><a href=\"admin.php?link=".$fields['id']."\">".$fields['id']."</a></td>";
       
    /* old */
    $days=timestamp_diff_in_days(time(), GetDateFromTimestamp($fields['timestamp']));
    echo "<td>".$days."&nbsp;d</td>\n";

    /* best ? */
    if($fields['isbest']==1)
      echo "<td>".$this->style->GetImage('best')."</td>\n";
    else
        echo "<td></td>\n";
      
    /* type image */
    echo  "<td>".$this->style->GetImage(get_type_by_number($fields['type']))."</td>\n";
  
    /* pseudo */
    $pseudo = $users_cache[$fields['user_id']];
    if ($fields['folder'] == get_folder_by_name("incoming"))
    { /* on affiche l'adresse mail */
      $this->db->MatchUserById($fields['user_id']);
      $this->db->Query();
      $val = $this->db->FetchArray();
      echo  "<td><a href=\"mailto:".$val['email']." \">";
      echo  $pseudo ."</a></td>\n" ;
    }
    else
    {
      if (!empty($fields['pseudo'])) /* utilisateur non inscrit */
        echo "<td>".$fields['pseudo'] ."</td>\n" ;
      else
      {
        echo  "<td><a href=\"?to=viewprofile&amp;id=".$fields['user_id']."\">";
        echo  $pseudo ."</a></td>\n" ;
      }

    }
    /* levelset */
    echo  "<td>". get_levelset_by_number($fields['levelset']) ."</td>\n" ;
    /* level */
    echo  "<td>" . $fields['level'] . "</td>\n" ;
    /* time */
    echo  "<td>" . sec_to_friendly_display($fields['time']) . "</td>\n" ;
    /* coins */
    echo  "<td>" . $fields['coins'] . "</td>\n" ;

    /* replay */
    if(empty($fields["replay"]))
    {
      echo  "<td><a href=\"?to=upload1&amp;id=".$fields['id']."\">upload file</a></td>\n" ;
    }
    else 
    {
      echo  "<td><a href=\"?to=upload1&amp;id=".$fields['id']."\" title=\"".$fields["replay"]."\">replace file</a>\n" ;
      $replay  = replay_link($fields['folder'], $fields['replay']);
      echo  "&nbsp;|&nbsp; <a href=" . $replay . " type=\"application/octet-stream\">replay</a></td>\n" ;
    }

    /* trash */
    echo "<td style=\"width: 16px;\">";
    if ($fields['folder'] == get_folder_by_name("trash")) {
      $action="recpurge";
      $img="del";
      $bull="Delete this record";
    }
    else {
      $img="trash";
      $action="rectrash";
      $bull="Send to trash";
    }
    echo "<a href=\"?to=".$action."&amp;id=".$fields['id']."\">";
    echo $this->style->GetImage($img, $bull);
    echo "</a></td>\n";

    /* to contest */
    if ($fields['folder'] != get_folder_by_name("contest") && $fields['folder'] != get_folder_by_name("incoming"))
    {
      echo "<td style=\"width: 16px;\">";
      echo "<a href=\"?to=repcontest&amp;id=".$fields['id']."\">";
      echo $this->style->GetImage('undo', "Reinject in contest");
      echo "</a></td>\n";
    }
    else if ($fields['folder'] == get_folder_by_name("incoming"))
    {
      echo "<td style=\"width: 16px;\">";
      echo "<a href=\"?to=repcontest&amp;id=".$fields['id']."\">";
      echo $this->style->GetImage('tocontest+', "Reinject in contest, always add");
      echo "</a></td>\n";
      echo "<td style=\"width: 16px;\">";
      echo "<a href=\"?to=repcontest&amp;overwrite=on&amp;id=".$fields['id']."\">";
      echo $this->style->GetImage('tocontestx', "Reinject in contest, update an existing record");
      echo "</a></td>\n";
    }
  
    /* "attach" */
    echo "<td><a href=\"?levelset_f=".$fields['levelset']."&amp;level_f=".$fields['level']."&amp;folder=-1\" title=\"Show all records for this level.\">";
    echo $this->style->GetImage('attach', "Show all records for this level.");
    echo "</a></td>";
     
    echo  "</tr>\n";
  }

  function _MemberLine($i, $fields)
  {
    global $nextargs, $userlevel;

    $readonly = Auth::Check(get_userlevel_by_name("root")) ? "" : "readonly" ;
    
    $rowclass = ($i % 2) ? "row1" : "row2";
    echo  "<tr class=\"".$rowclass."\">\n";
    
    echo "<form name=\"memberform_".$fields['id']."\" method=\"post\" action=\"?upmember&amp;id=".$fields['id']."\" >\n";

    /* id */
    echo "<td>";
    echo "<a href=\"edit_profile.php?id=".$fields['id']."\" >".$fields['id']."</a>";
    echo "</td>\n";

    /* Name */
    echo "<td>";
    echo "<input type=\"text\" name=\"pseudo\" value=\"".$fields['pseudo']."\" size=\"15\" ".$readonly." />\n";
    echo "</td>\n";

    /* Record number */
    echo "<td>";
    echo $fields['stat_total_records'];
    echo "</td>\n";

    /* Best records */
    echo "<td>";
    echo $fields['stat_best_records'];
    echo "</td>\n";

    /* Comments */
    echo "<td>";
    echo $fields['stat_comments'];
    echo "</td>\n";
    
    /* Mail */
    echo "<td>";
    echo "<a href=\"mailto:".$fields['email']."\">".$fields['email']."<mailto/>";
    echo "</td>\n";

    /* Auth level  */
    echo "<td>\n";
  
    $i=0;

    if (Auth::Check(get_userlevel_by_name("root")))
    {
      echo  "<select name=\"authlevel\" readonly>\n";
      foreach ($userlevel as $nb => $value)
      {
        echo  "<option value=\"".$nb."\">".$userlevel[$nb]."</option>\n";
        if ($nb < $fields['level'])
          $i++;
      }
      echo  "</select>\n";
    }
    else
      echo get_userlevel_by_number($fields['level']);
  
    
    /* Update */
    echo "</td>\n";
    echo "<td>";
    echo "<input type=\"submit\" value=\"Update\" />";
    echo "</td>\n";
    echo "</form>\n";

    /* Delete */
    echo "<form name=\"memberdelete_".$fields['id']."\" method=\"post\" action=\"?delmember&amp;id=".$fields['id']."\" >\n";
    echo "<td>";
    echo "<input type=\"submit\" value=\"Delete\" />";
    echo "</td>\n";
    echo "</form>\n";

    echo "</tr>\n";
    echo "<script type=\"text/javascript\">update_memberform_fields(\"".$fields['id']."\",".$i.")</script>\n";
  }
  
  function _JumpLine($colspan)
  {
    echo "<tr><td colspan=\"".$colspan."\" style=\"background: #fff; height: 2px;\"></td></tr>\n";
  }
}
