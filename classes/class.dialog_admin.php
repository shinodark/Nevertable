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
  var $output;
    
  /*__CONSTRUCTEUR__*/
  function DialogAdmin(&$db, $o_style)
  {
    $this->db = &$db;
    $this->smilies = new Smilies();
    $this->bbcode = new parse_bbcode();
    $this->style = $o_style;
    $this->output = "";
  }
  
  function Output($string="")
  {
      if (empty($string))
          echo $this->output;
      else
          echo $string;
  }

  function HtmlHead($title, $special="")
  {
    $this->output  =  "<head>\n";
    $this->output .=  "<title>$title</title>\n";
    $this->output .=  "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\"/>\n";
    $this->output .=  "<link rel=\"stylesheet\" href=\"".$this->style->GetCss()."\" type=\"text/css\" />\n";
   /* $this->output .=  "<link rel=\"shortcut icon\" href=\"".ROOT_PATH."favicon.ico\" />\n"; */
    $this->output .=  "<script type=\"text/javascript\" src=\"".ROOT_PATH."includes/js/jsutil.js\"></script>\n";
    $this->output .=  $special; 
    $this->output .=  "</head>\n\n";
  }

  function Top()
  {
    $this->output .=  "<div id=\"top\">\n";  
    $this->output .=  "<center>";
    $this->output .=   "<a href=\"http://www.nevercorner.net/table/\">".$this->style->GetImage('top')."</a>";
    $this->output .=  "</center>";
    $this->output .=   "</div>\n\n";

    $this->Output();
  }


  function Prelude()
  {
    $this->output  =   "<div id=\"prelude\">\n";
    $this->output .=   "<center>\n";
    $this->output .=   "<a href=\"?folder=".get_folder_by_name("incoming")."\">";
    $this->output .=   $this->db->RequestCountRecords(get_folder_by_name("incoming"), get_type_by_name("all"));
    $this->output .=   "&nbsp;records in incoming</a> &nbsp; | &nbsp; \n";
    $this->output .=   "<a href=\"?folder=".get_folder_by_name("trash")."\">";
    $this->output .=   $this->db->RequestCountRecords(get_folder_by_name("trash"), get_type_by_name("all"));
    $this->output .=   "&nbsp;records in trash</a> &nbsp; | &nbsp; \n";
    $this->output .=   "<a href=\"?folder=".get_folder_by_name("oldones")."\">";
    $this->output .=   $this->db->RequestCountRecords(get_folder_by_name("oldones"), get_type_by_name("all"));
    $this->output .=   "&nbsp;obsolete records</a> &nbsp; | &nbsp; \n";
    $this->output .=   "<a href=\"?folder=".get_folder_by_name("contest")."\">";
    $this->output .=   $this->db->RequestCountRecords(get_folder_by_name("contest"), get_type_by_name("all"));
    $this->output .=   "&nbsp;records in contest</a>\n";
    $this->output .=   "</center>\n";
    $this->output .=   "</div>\n";
    
    $this->Output();
  }
  
  function Speech()
  {
  }
  
  function NavBar($page, $limit, $total)
  {
    global $nextargs;

    if(isset($this->navbar_cache))
    {
        $this->output  =  $this->navbar_cache;
        $this->Output();
        return;
    }

    $page = (empty($page)) ? 1 : $page;
    $off = (empty($page)) ? 0 : ($page-1)*$limit;
  
    $this->output  = "<div class=\"navbar\">\n";
    $this->output .= "<center>\n";
  
    $pages  = ceil($total / $limit);
  
    if ($off > 0)
      $this->output .= "<a href=\"".$nextargs."&amp;page=".($page-1)."\">prev</a>\n";
    else
      $this->output .= "prev\n";

    $this->output .= "&nbsp;|&nbsp;";
 
    for ($i=1; $i<=$pages; $i++)
    {
      if ($i != $page)  
         $this->output .= "<a href=\"".$nextargs."&amp;page=".$i."\">".$i."</a>";
      else
         $this->output .= "<b>" . $i . "</b>";
      $this->output .= "&nbsp;\n";
    }

    $this->output .= "&nbsp;|&nbsp;";
    if ($off+$limit <= $total)
      $this->output .= "<a href=\"".$nextargs."&amp;page=".($page+1)."\">next</a>\n";
    else
      $this->output .= "next\n";
    
    $this->output .= "</center>\n";
    $this->output .= "</div>\n";
    $this->navbar_cache = $this->output;
    
    $this->Output();
  }

  function SideBar()
  {
    $bar = new SideBar();

    $menu_main = new Menu();
    $menu_main->AddItem("Check database", "admin.php?to=check");
    $menu_main->AddItem("Re-compute everything", "admin.php?to=recompute");
    $menu_main->AddItem("Configuration", "config.php");
    $menu_main->AddItem("Management", "management.php");
    $menu_main->AddItem("Members List", "memberlist.php");
    $menu_main->AddItem("File explorer", "filexplorer.php");
    $menu_main->AddItem("Purge trash", "purgetrash.php");
    $menu_main->AddItem("Tagboard moderation", "tag_moder.php");
    $menu_main->AddItem("Leave admin panel", "../index.php");

    $bar->AddBlock_MenuBar("Admin Menu", $menu_main);
    
    $this->output = $bar->End();
    $this->Output();
  }
  
  function Footer($version, $time="")
  {
    $this->output  =   "<div id=\"stats\">\n";
    if (!empty($time))
        $this->output .=   "Page generated in ".$time."s using ".$this->db->GetReqCount()." queries\n";
    else
        $this->output .=   "Page generated using ".$this->db->GetReqCount()." queries\n";
    $this->output .=   "</div>\n\n";
 
    $this->output .=   "<div id=\"footer\">\n";
    $this->output .=   "nevertable ".$version." powered by <a href=\"http://shinobufan.free.fr/dotclear\">shino</a>\n";
    $this->output .=   "</div>\n\n";
    
    /* wz_tooltip */
    $this->output .=  "<script type=\"text/javascript\" src=\"".ROOT_PATH."includes/js/wz_tooltip.js\"></script>\n";
    
    $this->Output();
  }


  function Table($mysql_results, $args)
  {
    global $nextargs;
    
    $this->output  =  "<div class=\"results-prelude\">\n";
    $this->output .=  "active folder: ".get_folder_by_number($args['folder'])."&nbsp;(".GetFolderDescription($args['folder']).")\n";
    $this->output .=  "</div>\n";
    $this->output .=  "<div class=\"results\">\n";
  
    $this->output .=  "<table>\n";
    $this->output .=  "<tr><th style=\"width: 16px;\"></th>\n"; // select
    $this->output .=  "<th><a href=\"".$nextargs."&amp;sort=id\">id</a></th>\n"; //id
    $this->output .=  "<th style=\"width: 28px;\"></th>\n";     //days
    $this->output .=  "<th style=\"width: 28px;\"></th>\n";     //best
    $this->output .=  "<th style=\"width: 28px;\"></th>\n";     //type
    $this->output .=  "<th><a href=\"".$nextargs."&amp;sort=user\">player</a></th>\n";
    $this->output .=  "<th><a href=\"".$nextargs."&amp;sort=level\">set</a></th>\n";
    $this->output .=  "<th><a href=\"".$nextargs."&amp;sort=level\">lvl</a></th>\n";
    $this->output .=  "<th><a href=\"".$nextargs."&amp;sort=time\">time</a></th>\n";
    $this->output .=  "<th><a href=\"".$nextargs."&amp;sort=coins\">cns</a></th>\n";
    $this->output .=  "<th>replay</th>\n";
    $this->output .=  "<th style=\"width: 16px;\"></th>\n";    // delete icon
    if ($args['folder'] != get_folder_by_name("contest"))
      $this->output .=  "<th style=\"width: 16px;\"></th>\n";    // undo icon
    if ($args['folder'] == get_folder_by_name("incoming"))
      $this->output .=  "<th style=\"width: 16px;\"></th>\n";    // undo icon avec overwrite
    $this->output .=  "<th></th>\n"; // goto same records
    $this->output .=  "</tr>\n";

    $i=0;
    while ($fields = $this->db->FetchArray($mysql_results))
    {
      /*$this->output .=  "<tr><td colspan=\"12\" style=\"background: #fff; height: 1px;\"></td></tr>\n";*/
      $this->_RecordLine($i, $fields);
      $this->_JumpLine(14);
      $i++;
    }
    $this->output .=  "</table>\n";
    $this->output .=  "</div>\n\n";
    
    $this->Output();
  }

  function Level($results_contest, $results_oldones, $args, $total1="", $total2="")
  {
  }

  function Record($fields)
  {
    $this->output  =  "<div class=\"oneresult\">\n";
    $this->output .=  "<table><tr>\n";
    $this->output .=  "<tr><th style=\"width: 16px;\"></th>\n"; // select
    $this->output .=  "<th><a href=\"".$nextargs."&amp;sort=id\">id</a></th>\n"; //id
    $this->output .=  "<th style=\"width: 28px;\"></th>\n"; // days
    $this->output .=  "<th style=\"width: 28px;\"></th>\n"; // best
    $this->output .=  "<th style=\"width: 32px;\"></th>\n"; // type
    $this->output .=  "<th style=\"width: 100px;\">player</th>\n"; // player
    $this->output .=  "<th>set</th>\n";
    $this->output .=  "<th>lvl</th>\n";
    $this->output .=  "<th>time</th>\n";
    $this->output .=  "<th>cns</th>\n";
    $this->output .=  "<th></th>\n"; // replay
    $this->output .=  "<th style=\"width: 16px;\"></th>\n";    // delete icon
    if ($fields['folder'] != get_folder_by_name("contest"))
      $this->output .=  "<th style=\"width: 16px;\"></th>\n";    // undo icon
    $this->output .=  "<th></th>\n"; // goto same records
    $this->output .=  "</tr>\n";

    $u = new User($this->db);
    $s = new Set($this->db);
    if(!$u->LoadFromId($fields['user_id']))
        button_error($u->GetError(), 400);
    if(!$s->LoadFromId($fields['levelset']))
        button_error($u->GetError(), 400);
    $fields['pseudo'] = $u->GetPseudo();
    $fields['set_name'] = $s->GetName();
    $this->_RecordLine(0, $fields, false);
    
    $this->output .=  "</table>\n";
    $this->output .=  "</div>\n";
    
    $this->Output();
  }

  function RecordLink($replay_id)
  {
    
  }

  function Comments($mysql_results, $date_format)
  {
  }

  function MemberList($mysql_results)
  {
    $this->output  =  "<center>\n";
    $this->output .=  "<div class=\"results\" style=\"width:100%; float: none;\">\n";
    $this->output .=  "<table style=\"text-align: center;\"><tr>\n";
    $this->output .=  "<th style=\"text-align: center;\"><a href=memberlist.php?sort=id>#</a></th>\n";
    $this->output .=  "<th style=\"text-align: center;\"><a href=memberlist.php?sort=pseudo>Name</a></th>\n";
    $this->output .=  "<th style=\"text-align: center;\"><a href=memberlist.php?sort=records>Records number</a></th>\n";
    $this->output .=  "<th style=\"text-align: center;\"><a href=memberlist.php?sort=best>Best records</a></th>\n";
    $this->output .=  "<th style=\"text-align: center;\"><a href=memberlist.php?sort=comments>Comments</a></th>\n";
    $this->output .=  "<th style=\"text-align: center;\">Mail</th>\n";
    $this->output .=  "<th style=\"text-align: center;\"><a href=memberlist.php?sort=cat>Auth Level</a></th>\n";
    $this->output .=  "<th></th>\n"; // update
    $this->output .=  "<th></th>\n"; // delete
    $this->output .=  "</tr>\n";
    $i=0;
    while ($fields = $this->db->FetchArray($mysql_results))
    {
      $this->output .=  "<tr><td colspan=\"7\" style=\"background: #fff; height: 1px;\"></td></tr>\n";
      $this->_MemberLine($i, $fields);
      $i++;
    }
    $this->output .=  "</table>\n";
    $this->output .=  "</div>\n";
    $this->output .=  "</center>\n";
    
    $this->Output();
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

    
    $this->output  =  "<div class=\"nvform\" style=\"width: 700px;\">\n";
    $this->output .=  "<table><th colspan=\"2\">Profile ".$pseudo."</th>\n";
    $this->output .=  "<tr><td>\n";

    /* table interne de pagination */
    $this->output .=  "<div class=\"embedded\">\n";
    $this->output .=  "<table>\n";
    $this->output .=  "<tr><td width=130px valign=\"top\">\n";
    $this->output .=  "<center>".$avatar."</center>\n";
    $this->output .=  "<br/>";
    $this->output .=  "<center>".get_userlevel_by_number($level)."</center>\n";
    $this->output .=  "<br/>\n";
    $this->output .=  "<center>";
    for($i=0; $i<CalculRank($records); $i++)
      $this->output .=  $this->style->GetImage('rank');
    $this->output .=  "</center>\n";
    $this->output .=  "</td><td>\n";
    
    /* infos */
    $this->output .=  "<table>\n";
    $this->output .=  "<tr>\n";
    $this->output .=  "<td class=\"row2\" width=\"150px\">Localisation  </td>\n";
    $this->output .=  "<td class=\"row1\">".$location."</td>\n";
    $this->output .=  "</tr>\n";
    $this->output .=  "<tr>\n";
    $this->output .=  "<td class=\"row2\">Web </td>\n";
    $this->output .=  "<td class=\"row1\">".$web."</td>\n";
    $this->output .=  "</tr>\n";
    $this->output .=  "<tr>\n";
    $this->output .=  "<td class=\"row2\">Quote </td>\n";
    $this->output .=  "<td class=\"row1\">".$speech."</td>\n";
    $this->output .=  "</tr>\n";
    $this->output .=  "</table>\n";
    /* fin infos */
    
    $this->output .=  "<br/>\n";

    /* stats */
    $this->output .=  "<table>\n";
    $this->output .=  "<tr>\n";
    $this->output .=  "<td class=\"row2\" width=\"150px\">Total Records </td>\n";
    $this->output .=  "<td class=\"row1\">".$records."</td>\n";
    $this->output .=  "</tr>\n";
    $this->output .=  "<tr>\n";
    $this->output .=  "<td class=\"row2\">Best Records </td>\n";
    $this->output .=  "<td class=\"row1\">".$records_best."</td>\n";
    $this->output .=  "</tr>\n";
    $this->output .=  "<tr>\n";
    $this->output .=  "<td class=\"row2\">Best Time</td>\n";
    $this->output .=  "<td class=\"row1\">";
    for ($i=0; $i<$records_besttime; $i++)
      $this->output .=  $this->style->GetImage('best time');
    $this->output .=  "</td>\n";
    $this->output .=  "</tr>\n";
    $this->output .=  "<tr>\n";
    $this->output .=  "<td class=\"row2\">Most coins</td>\n";
    $this->output .=  "<td class=\"row1\">";
    for ($i=0; $i<$records_mostcoins; $i++)
      $this->output .=  $this->style->GetImage('most coins');
    $this->output .=  "</td>\n";
    $this->output .=  "</tr>\n";
    $this->output .=  "<tr>\n";
    $this->output .=  "<td class=\"row2\">Freestyle</td>\n";
    $this->output .=  "<td class=\"row1\">";
    for ($i=0; $i<$records_freestyle; $i++)
      $this->output .=  $this->style->GetImage('freestyle');
    $this->output .=  "</td>\n";
    $this->output .=  "</tr>\n";
    $this->output .=  "<tr>\n";
    $this->output .=  "<td class=\"row2\">Comments </td>\n";
    $this->output .=  "<td class=\"row1\">". $comments . "</td>\n";
    $this->output .=  "</tr>\n";
    $this->output .=  "<tr>\n";
    $this->output .=  "<td colspan=\"2\">\n";
    $this->output .=  "</td>\n";
    $this->output .=  "</tr>\n";
    $this->output .=  "</table>\n";
    /* fin stats */

    $this->output .=  "</td></tr>\n";
    $this->output .=  "<tr><td colspan=\"2\">\n";

    $this->output .=  "<center>";
    $this->output .=  "<a href=\"".ROOT_PATH."index.php?folder=-1&filter=pseudo&filterval=".$pseudo." \">View all records of ".$pseudo."</a>\n";
    $this->output .=  "<br/><a href=\"".ROOT_PATH."index.php?folder=".get_folder_by_name("contest")."&filter=pseudo&filterval=".$pseudo." \">View all records (contest only)</a>\n";
    $this->output .=  "</center>\n";
    
    $this->output .=  "</td></tr>\n";
    $this->output .=  "</table>\n";
    $this->output .=  "</div>\n";
    /* fin table interne pagination */

    $this->output .=  "</td></tr>\n";
    $this->output .=  "</table>\n";
    $this->output .=   "</div>\n";
    
    $this->Output();
  }

  function CommentForm($replay_id, $content_memory, $user_id="")
  {
  }
  
  function TypeForm($args)
  {
    global $types, $levels, $folders, $newonly;
  
    $this->output  =   "<div class=\"nvform\" style=\"width: 700px;\">\n";
    $this->output .=   "<form method=\"post\" action=\"?\" name=\"typeform_admin\">\n";
    $this->output .=   "<table><tr>\n";
    $this->output .=   "<td><label for=\"table\">table select : </label>\n";
    $this->output .=   "<select name=\"type\" id=\"table\">\n";
  
    foreach ($types as $nb => $value)
      $this->output .=   "<option value=\"".$nb."\">".$types[$nb]["name"]."</option>\n";
  
    $this->output .=   "</select></td>\n";
  
    $this->output .=   "<td><label for=\"folder\">folder select : </label>\n";
    $this->output .=   "<select name=\"folder\" id=\"folder\">\n";
  
    foreach ($folders as $nb => $value)
      $this->output .=   "<option value=\"".$nb."\">".$folders[$nb]["name"]."</option>\n";
  
    $this->output .=   "</select></td>\n";
    $this->output .=   "</tr><tr><td></td></tr><tr>\n";
  
    $this->output .=   "<td><label for=\"bestonly\">Print only best records </label>\n";
    $this->output .=   "<input type=\"checkbox\" name=\"bestonly\" id=\"bestonly\" value=\"on\" /></td>\n";
    $this->output .=   "<td><label for=\"bestonly\">Print only new records </label>\n";
    $this->output .=   "<select name=\"newonly\" id=\"newonly\">\n";
  
    foreach ($newonly as $nb => $name)
      $this->output .=   "<option value=\"".$nb."\">".$name."</option>\n";
  
    $this->output .=   "</select></td>\n";
    $this->output .=   "</tr><tr><td></td></tr><tr>\n";

    $this->output .=   "<td><label for=\"levelset_f\">levelset: </label>\n";
    $this->output .=   "<select name=\"levelset_f\" id=\"levelset_f\">\n";
    $this->output .=   "<option value=\"0\">all</option>\n";
    foreach ($this->db->GetSets() as $id => $name)
    {
      $this->output .=   "<option value=\"".$id."\">".$name."</option>\n";
    }
    $this->output .=   "</select></td>\n";
    $this->output .=   "<td><label for=\"level_f\">level: </label>\n";
    $this->output .=   "<select name=\"level_f\" id=\"level_f\">\n";
    $this->output .=   "<option value=\"0\">all</option>\n";
    foreach ($levels as $name => $value)
    {
      $this->output .=   "<option value=\"".$value."\">".$name."</option>\n";
    }
    $this->output .=   "</select></td>\n";
    $this->output .=   "</tr><tr><td></td></tr><tr>\n";
  
    $this->output .=   "<td colspan=\"4\"><center><input type=\"submit\" value=\"Change\" /></center>\n";
    $this->output .=   "</td></tr></table>\n";
    $this->output .=   "</form>\n";
    $this->output .=   "</div>\n\n";
    $this->output .=  "<script type=\"text/javascript\">update_typeform_fields_admin(".$args['type'].",".$args['folder'].",\"".$args['bestonly']."\",".$args['newonly'].",".$args['levelset_f'].",".$args['level_f'].")</script>\n";
    
    $this->Output();
  }
  
  function EditForm()
  {
    global $types, $levels, $nextargs;
    
    $this->output  =   "<div class=\"nvform\" style=\"width: 600px;\">\n";
    $this->output .=   "<form method=\"post\" action=\"".$nextargs."\" name=\"editform\">\n";
    $this->output .=   "<table><tr>\n";
    $this->output .=   "<th colspan=\"6\">Record Editor</th></tr><tr>\n";
    $this->output .=   "<td><label for=\"id\">id:</label></td>\n";
    $this->output .=   "<td><input type=\"text\" id=\"id\" name=\"id\" size=\"3\" readonly />\n";
    $this->output .=   "</td></tr><tr>\n";
    /* pseudo est donné à titre indicatif, non utilisé */
    $this->output .=   "<td><label for=\"pseudo\">pseudo: </label></td>\n";
    $this->output .=   "<td><input type=\"text\" id=\"pseudo\" name=\"pseudo_info\" size=\"15\" readonly /></td>\n";
    $this->output .=   "<td><label for=\"user_id\"> # </label></td>\n";
    $this->output .=   "<td><input type=\"text\" id=\"user_id\" name=\"user_id\" size=\"5\" readonly /></td><td colspan=\"2\"></td></tr>\n<tr>";
    $this->output .=   "<td><label for=\"levelset\">levelset: </label></td>\n";
    $this->output .=   "<td><select name=\"levelset\" id=\"levelset\">\n";
  
    foreach ($this->db->GetSets() as $id => $name)
    {
      $this->output .=   "<option value=\"".$id."\">".$name."</option>\n";
    }
  
    $this->output .=   "</select></td>\n";
    $this->output .=   "<td><label for=\"level\">level: </label></td>\n";
    $this->output .=   "<td><select name=\"level\" id=\"level\">\n";
  
    foreach ($levels as $name => $value)
    {
      $this->output .=   "<option value=\"".$value."\">".$name."</option>\n";
    }
  
    $this->output .=   "</select>\n</td>\n";
    $this->output .=   "<td><label for=\"type\">type: </label></td>\n";
    $this->output .=   "<td><select id=\"type\" name=\"type\">\n";
  
    foreach ($types as $nb => $value)
    {
      if ($types[$nb]["name"] != "all")
          $this->output .=   "<option value=\"".$nb."\">".$types[$nb]["name"]."</option>\n";
    }
  
    $this->output .=   "</select>\n</td></tr><tr>\n";

    $this->output .=   "<td colspan=\"3\"><label for=\"time\">time(s), >=9999 if goal not reached: </label></td>\n";
    $this->output .=   "<td><input type=\"text\" id=\"time\" name=\"time\" size=\"7\" /></td>\n";
    $this->output .=   "<td><label for=\"coins\">coins: </label></td>\n";
    $this->output .=   "<td><input type=\"text\" id=\"coins\" name=\"coins\" size=\"5\"/></td></tr>\n<tr>";
    $this->output .=   "<td><label for=\"replay\">replay file: </label></td>\n";
    $this->output .=   "<td colspan=\"5\"><input type=\"text\" id=\"replay\" name=\"replay\" size=\"50\" />\n";
  
    $this->output .=   "</td></tr><tr>\n";

    $this->output .=   "<td colspan=\"2\"><center><input type=\"submit\" value=\"Modify !\" /></center></td>\n";
    $this->output .=   "<td colspan=\"2\"><center><input type=\"reset\" value=\"Clear form\" /></center></td>\n";
    $this->output .=   "<td><input type=\"hidden\" id=\"to\" name=\"to\" value=\"edit\" /></td>\n";
    $this->output .=   "</tr>\n";
  
    $this->output .=   "</table>\n";
    $this->output .=   "</form>\n";
    $this->output .=   "</div>\n\n";
    
    $this->Output();
  }
  
  function AddFormAuto()
  {
  }

  function UploadForm($size_limit)
  {
    global $nextargs;
  
    $this->output  =   "<div class=\"nvform\"  style=\"width: 600px;\" >\n";
    $this->output .=   "<form enctype=\"multipart/form-data\" action=\"".$nextargs."&amp;to=upload2\" method=\"POST\">\n";
    $this->output .=   "<table><tr>\n";
    $this->output .=   "<td><input type=\"hidden\" name=\"MAX_FILE_SIZE\" value=\"".$size_limit."\" />\n";
    $this->output .=   "<td><label for=\"uploadfile\">Upload a replay file : </label></td>\n";
    $this->output .=   "<td><input name=\"uploadfile\" type=\"file\" /></td>\n";
    $this->output .=   "<td><input type=\"submit\" value=\"Send File\" /></td>\n";
    $this->output .=   "</tr></table>\n";
    $this->output .=   "</form>\n";
    $this->output .=   "</div>\n\n";
    
    $this->Output();
  }
  
  /*__METHODES PRIVESS__*/

  function _RecordLine($i, $fields, $display_shot=true)
  {
    global $nextargs;

    if ($display_shot)
    {
      $tooltip=Javascriptize(GetShotMini($fields['set_path'], $fields['map_solfile'], 128));
      $onmouseover = "onmouseover=\"return escape('".$tooltip."')\"";
    }

    $rowclass = ($i % 2) ? "row1" : "row2";
    $this->output .=   "<tr class=\"".$rowclass."\" ".$onmouseover.">\n";

    // select
    $jsargs  = "'".$fields['id']."',";
    $jsargs .= "'".$fields['user_id']."',";
    $jsargs .= "'".$fields['pseudo']."',";
    $jsargs .= "'".$fields['levelset']."',";
    $jsargs .= "'".get_level_by_name($fields['level'])."',";
    $jsargs .= "'".$fields['time']."',";
    $jsargs .= "'".$fields['coins']."',";
    $jsargs .= "'".$fields['replay']."',";
    $jsargs .= "'".$fields['type']."'";
    $this->output .=   "<td style=\"width:16px;\">".$this->style->GetImage('arrow', "", "", "onclick=\"change_editform(".$jsargs.")\"")."</td>\n";

    //id
    $this->output .=   "<td><a href=\"admin.php?link=".$fields['id']."\">".$fields['id']."</a></td>";
       
    /* old */
    $days=timestamp_diff_in_days(time(), GetDateFromTimestamp($fields['timestamp']));
    $this->output .=  "<td>".$days."&nbsp;d</td>\n";

    /* best ? */
    if($fields['isbest']==1)
      $this->output .=  "<td>".$this->style->GetImage('best')."</td>\n";
    else
        $this->output .=  "<td></td>\n";
      
    /* type image */
    $this->output .=   "<td>".$this->style->GetImage(get_type_by_number($fields['type']))."</td>\n";
  
    /* pseudo */
    if ($fields['folder'] == get_folder_by_name("incoming"))
    { /* on affiche l'adresse mail */
      $this->db->MatchUserById($fields['user_id']);
      $this->db->Query();
      $val = $this->db->FetchArray();
      $this->output .=   "<td><a href=\"mailto:".$val['email']." \">";
      $this->output .=   $fields['pseudo'] ."</a></td>\n" ;
    }
    else
    {
      if (!empty($fields['pseudo'])) /* utilisateur non inscrit */
        $this->output .=  "<td>".$fields['pseudo'] ."</td>\n" ;
      else
      {
        $this->output .=   "<td><a href=\"edit_profile.php?id=".$fields['user_id']."\">";
        $this->output .=   $fields['pseudo'] ."</a></td>\n" ;
      }

    }
    /* levelset */
    $this->output .=   "<td>". $fields['set_name'] ."</td>\n" ;
    /* level */
    $this->output .=   "<td>" . $fields['level'] . "</td>\n" ;
    /* time */
    $this->output .=   "<td>" . sec_to_friendly_display($fields['time']) . "</td>\n" ;
    /* coins */
    $this->output .=   "<td>" . $fields['coins'] . "</td>\n" ;

    /* replay */
    if(empty($fields["replay"]))
    {
      $this->output .=   "<td><a href=\"upload.php?id=".$fields['id']."\">upload file</a></td>\n" ;
    }
    else 
    {
      $this->output .=   "<td><a href=\"upload.php?id=".$fields['id']."\" title=\"".$fields["replay"]."\">replace file</a>\n" ;
      $replay  = replay_link($fields['folder'], $fields['replay']);
      $this->output .=   "&nbsp;|&nbsp; <a href=" . $replay . " type=\"application/octet-stream\">replay</a></td>\n" ;
    }

    /* trash */
    $this->output .=  "<td style=\"width: 16px;\">";
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
    $this->output .=  "<a href=\"?to=".$action."&amp;id=".$fields['id']."\">";
    $this->output .=  $this->style->GetImage($img, $bull);
    $this->output .=  "</a></td>\n";

    /* to contest */
    if ($fields['folder'] != get_folder_by_name("contest") && $fields['folder'] != get_folder_by_name("incoming"))
    {
      $this->output .=  "<td style=\"width: 16px;\">";
      $this->output .=  "<a href=\"?to=repcontest&amp;id=".$fields['id']."\">";
      $this->output .=  $this->style->GetImage('undo', "Reinject in contest");
      $this->output .=  "</a></td>\n";
    }
    else if ($fields['folder'] == get_folder_by_name("incoming"))
    {
      $this->output .=  "<td style=\"width: 16px;\">";
      $this->output .=  "<a href=\"?to=repcontest&amp;id=".$fields['id']."\">";
      $this->output .=  $this->style->GetImage('tocontest+', "Reinject in contest, always add");
      $this->output .=  "</a></td>\n";
      $this->output .=  "<td style=\"width: 16px;\">";
      $this->output .=  "<a href=\"?to=repcontest&amp;overwrite=on&amp;id=".$fields['id']."\">";
      $this->output .=  $this->style->GetImage('tocontestx', "Reinject in contest, update an existing record");
      $this->output .=  "</a></td>\n";
    }
  
    /* "attach" */
    $this->output .=  "<td><a href=\"?levelset_f=".$fields['levelset']."&amp;level_f=".$fields['level']."&amp;folder=-1\" title=\"Show all records for this level.\">";
    $this->output .=  $this->style->GetImage('attach', "Show all records for this level.");
    $this->output .=  "</a></td>";
     
    $this->output .=   "</tr>\n";
  }

  function _MemberLine($i, $fields)
  {
    global $nextargs, $userlevel;

    $readonly = Auth::Check(get_userlevel_by_name("root")) ? "" : "readonly" ;
    
    $rowclass = ($i % 2) ? "row1" : "row2";
    $this->output .=   "<tr class=\"".$rowclass."\">\n";
    
    $this->output .=  "<form name=\"memberform_".$fields['id']."\" method=\"post\" action=\"memberlist.php?upmember&amp;id=".$fields['id']."\" >\n";

    /* id */
    $this->output .=  "<td>";
    $this->output .=  "<a href=\"edit_profile.php?id=".$fields['id']."\" >".$fields['id']."</a>";
    $this->output .=  "</td>\n";

    /* Name */
    $this->output .=  "<td>";
    $this->output .=  "<input type=\"text\" name=\"pseudo\" value=\"".$fields['pseudo']."\" size=\"15\" ".$readonly." />\n";
    $this->output .=  "</td>\n";

    /* Record number */
    $this->output .=  "<td>";
    $this->output .=  $fields['stat_total_records'];
    $this->output .=  "</td>\n";

    /* Best records */
    $this->output .=  "<td>";
    $this->output .=  $fields['stat_best_records'];
    $this->output .=  "</td>\n";

    /* Comments */
    $this->output .=  "<td>";
    $this->output .=  $fields['stat_comments'];
    $this->output .=  "</td>\n";
    
    /* Mail */
    $this->output .=  "<td>";
    $this->output .=  "<a href=\"mailto:".$fields['email']."\">".$fields['email']."<mailto/>";
    $this->output .=  "</td>\n";

    /* Auth level  */
    $this->output .=  "<td>\n";
  
    $i=0;

    if (Auth::Check(get_userlevel_by_name("root")))
    {
      $this->output .=   "<select name=\"authlevel\" readonly>\n";
      foreach ($userlevel as $nb => $value)
      {
        $this->output .=   "<option value=\"".$nb."\">".$userlevel[$nb]."</option>\n";
        if ($nb < $fields['level'])
          $i++;
      }
      $this->output .=   "</select>\n";
    }
    else
      $this->output .=  get_userlevel_by_number($fields['level']);
  
    
    /* Update */
    $this->output .=  "</td>\n";
    $this->output .=  "<td>";
    $this->output .=  "<input type=\"submit\" value=\"Update\" />";
    $this->output .=  "</td>\n";
    $this->output .=  "</form>\n";

    /* Delete */
    $this->output .=  "<form name=\"memberdelete_".$fields['id']."\" method=\"post\" action=\"memberlist.php?delmember&amp;id=".$fields['id']."\" >\n";
    $this->output .=  "<td>";
    $this->output .=  "<input type=\"submit\" value=\"Delete\" />";
    $this->output .=  "</td>\n";
    $this->output .=  "</form>\n";

    $this->output .=  "</tr>\n";
    $this->output .=  "<script type=\"text/javascript\">update_memberform_fields(\"".$fields['id']."\",".$i.")</script>\n";
  }
  
  function _JumpLine($colspan)
  {
    $this->output .=  "<tr><td colspan=\"".$colspan."\" style=\"background: #fff; height: 2px;\"></td></tr>\n";
  }
}
