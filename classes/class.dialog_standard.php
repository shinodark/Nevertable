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
#

include_once ROOT_PATH ."classes/class.sidebar.php";
include_once ROOT_PATH ."classes/class.menu.php";

class DialogStandard
{
  var $db;
  var $navbar_cache;
  var $smilies;
  var $bbcode;
  var $style;
   

  /*__CONSTRUCTEUR__*/
  function DialogStandard(&$db, &$o_style)
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
    echo "<link rel=\"alternate\" type=\"application/rss+xml\" title=\"Nevertable rss feed\" href=\"http://www.nevercorner.net/table/rss.php\" />\n";
    /*echo "<link rel=\"shortcut icon\" href=\"./favicon.ico\" />\n";*/
    echo "<script type=\"text/javascript\" src=\"".ROOT_PATH."includes/js/jsutil.js\"></script>\n";

    if(!empty($special)) echo $special; 
    echo "</head>\n\n";
  }

  function Top()
  {
    echo  "<div id=\"top\">\n"; 
    echo "<center>";
    echo  "<a href=\"http://www.nevercorner.net/table/\">".$this->style->GetImage('top')."</a>";
    echo "</center>";
    echo  "</div>\n\n";
  }


  function Prelude()
  {
    echo  "<div id=\"prelude\">\n";
    echo  "<center>\n";
    echo  "<a href=\"http://icculus.org/neverball/\">Neverball official site</a> &nbsp; | &nbsp;\n";
    echo  "<a href=\"http://www.happypenguin.org/show?Neverball\">Happypenguin page</a>&nbsp; | &nbsp;\n";
    echo  "<a href=\"http://www.nevercorner.net/forum/\">Neverforum</a>&nbsp; | &nbsp;\n";
    echo  "<a href=\"http://www.nevercorner.net/wiki/\">NeverWiki</a>\n";
    echo  "</center>\n";
    echo  "</div>\n\n";
  }
  
  function Speech()
  {
    echo  "<div id=\"speech\">\n";

    $speech  = ROOT_PATH . "speech.txt";
    if (file_exists($speech))
    {
      $line = file_get_contents($speech);
      echo $line . "<br />\n";
    }
    
    $announce = ROOT_PATH . "announce.txt";
    if (file_exists($announce))
    {
      $line = file($announce);
      foreach ($line as $nb => $value)
        echo $value;
    }
    echo  "</div>\n\n";
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

  function SideBar($online_stats=array())
  {
    global $config, $users_cache;

    $rec = new Record($this->db);
    $bar = new SideBar();

    $bar->AddBlock_Welcome();
    $bar->AddBlock_stats($online_stats);
    
    
    /* Menu */
    if (Auth::Check(get_userlevel_by_name("member")))
    {
      $menu_main = new Menu();

      if (Auth::Check(get_userlevel_by_name("admin")))
      {
        $menu_main->AddItem("Admin Panel", "admin/admin.php");
        $menu_sub = new Menu(1);
        $count_incoming =  $this->db->RequestCountRecords(get_folder_by_name("incoming"), get_type_by_name("all"));
        $menu_sub->AddItem($count_incoming." incoming records", "admin/admin.php?folder=".get_folder_by_name("incoming"));
        $menu_sub->AddItem("Management", "admin/management.php");
        $menu_sub->AddItem("Members mgmt", "admin/memberlist.php");
        $menu_sub->AddItem("File explorer", "admin/filexplorer.php");
        $menu_sub->AddItem("Tagboard moderation", "javascript:child=window.open('/shinotag/moder.php', 'Tag moderation', 'fullscreen=no,toolbar=no,status=no,menubar=no,scrollbars=no,resizable=yes,directories=no,location=no,width=270,height=200,left='+(Math.floor(screen.width/2)-140));child.focus()");
      
        $menu_main->AddSubMenu($menu_sub);
      }
      $menu_main->AddItem("Upload a record", "upload.php");
      $menu_main->AddItem("Member list", "memberlist.php");
      $menu_main->AddItem("Your profile", "profile.php");
      $menu_main->AddItem("Logout", "login.php?out");
      
      $bar->AddBlock_MenuBar("Member Menu", $menu_main);
    }
    else
    {
      $bar->AddBlock_LoginForm();
      $menu_main = new Menu();
      $menu_main->AddItem("Register", "register.php");
      $menu_main->AddItem("Forgot your password ?", "forgot.php");
      $menu_main->AddItem("Member list", "memberlist.php");
      
      $bar->AddBlock_MenuBar("", $menu_main);
    }
    
    $bar->AddBlock_TagBoard($this->style->GetStyle());
    $bar->AddBlock_LastComments($this->db, $this->bbcode, $this->smilies);
    $bar->AddBlock_Legend($this->style);
    $bar->AddBlock_Baneers();
    
    $bar->End();
    
  }
  
  function Footer($version, $time="")
  {
    if(!empty($time))
    {
      echo  "<div id=\"stats\">\n";
      // echo  "Page generated using ".$time."s\n";
      echo  "Page generated using ".$time."s and ".$this->db->GetReqCount()." queries\n";
      echo  "</div>\n\n";
    }
 
    echo  "<div id=\"footer\">\n";
    echo  "nevertable ".$version." powered by <a href=\"http://shinobufan.free.fr/dotclear\">shino</a>\n";
    echo  "</div>\n\n";
 
    /* wz_tooltip */
    echo "<script type=\"text/javascript\" src=\"".ROOT_PATH."includes/js/wz_tooltip.js\"></script>\n";
  }


  function Table($mysql_results, $args, $diffview=false, $total="")
  {
    global $nextargs;
   
    echo "<div class=\"results\">\n";
    echo "<div class=\"results-prelude\">\n";
    if (!empty($total))
     echo $total." records displayed\n";
    echo "</div>\n";

    echo "<table><tr>\n";
    $this->_ResultsHeader();
    echo "</tr>\n";
    $i=0;
    /* liste des liens pour téléchargements */
    $_SESSION['download_list'] = "";
    $diffref = array();
    while ($fields = $this->db->FetchArray($mysql_results))
    {
      /* garde la référence en mémoire pour le diff */
      if ($diffview && $i==0)
        $diffref = $fields;
      $this->_RecordLine($i, $fields, $diffview, $diffref);
      $this->_JumpLine(12);
      $i++;
    }
    echo "</table>\n";
    echo "<br/>\n";
    
    echo "<center>Show results as downloadable list : <a href=\"?to=showlinklist\" target=\"_blank\">Link_List.lst</a></center>\n";
    echo "</div>\n\n";
  }
  
  function Level($results_contest, $results_oldones, $args, $total1="", $total2="")
  {
    $level = (integer) $args['level_f'];
    $set   = (integer) $args['levelset_f'];

    /* records du contest */
    echo "<div class=\"results\">\n";
    
    echo "<table width=\"400px\"><tr>\n";
    echo "<th>Level Shot</th></tr>\n";
    echo "<tr><td>";
    echo "<center>".GetShot($set, $level)."</center>\n";
    echo "</td></tr>\n";
    echo "</table>\n";
    echo "<br/>\n";

    if ($total1 != 0) {

    echo "<div class=\"results-prelude\">\n";
    echo "Contest records (".(integer)$total1.")\n";
    echo "</div>\n";
      
    echo "<table><tr>\n";
    $this->_ResultsHeader();
    echo "</tr>\n";
    $i=0;
    while ($fields = $this->db->FetchArray($results_contest))
    {
      $this->_RecordLine($i, $fields);
      $this->_JumpLine(12);
      $i++;
    }
    echo "</table>\n";
    echo "<br/>\n";
    }

    /* records anciens */
    if ($total2 != 0) {
    
    echo "<div class=\"results-prelude\">\n";
    echo "Old records (".(integer)$total2.")\n";
    echo "</div>\n";
      
    echo "<table><tr>\n";
    $this->_ResultsHeader();
    echo "</tr>\n";
    $i=0;
    while ($fields = $this->db->FetchArray($results_oldones))
    {
      $this->_RecordLine($i, $fields);
      $this->_JumpLine(12);
      $i++;
    }
    echo "</table>\n";
    }
    echo "</div>\n\n";
  }

  function Record($fields)
  {
    echo "<div class=\"oneresult\">\n";
    echo "<table><tr>\n";
    echo "<th style=\"width: 28px;\"></th>\n"; // new
    echo "<th>old</th>\n"; // days
    echo "<th style=\"width: 28px;\"></th>\n"; // best
    echo "<th style=\"width: 32px;\"></th>\n"; // type
    echo "<th style=\"width: 100px;\">player</th>\n";
    echo "<th>set</th>\n";
    echo "<th>level</th>\n";
    echo "<th>time</th>\n";
    echo "<th>coins</th>\n";
    echo "<th></th>\n"; // replay
    echo "<th></th>\n"; // comments
    echo "<th></th>\n"; // goto same records
    echo "</tr>\n";

    /* can be useful in case of print a new added record */
    $this->_RecordLine(0, $fields);
    
    echo "</table>\n";
    echo "</div>\n";
  }

  function RecordLink($fields)
  {
      global $config, $users_cache;
      
      echo "<div class=\"embedded\">\n";
      echo "<br/>\n";
      $link = "http://".$_SERVER['SERVER_NAME'] ."/".$config['nvtbl_path'] . "?link=".$fields['id'];
      $value =  "[url=".$link."]" .
                 get_type_by_number($fields['type']) ." by ".$users_cache[$fields['user_id']]." on ". get_levelset_by_number($fields['levelset']) . " " . $fields['level'].
                "[/url]";
      echo "<center><a href=\"".$link."\"  >bblink : </a>\n";
      echo "<input type=\"text\" size=\"".strlen($value)."\" value=\"".$value."\" readonly />\n";
      echo "</center>\n";
      echo "<br/>\n";
      echo "</div>\n";
  }

  function Comments($mysql_results, $date_format)
  {
    if ($this->db->NumRows($mysql_results)>0)
    {
      $o_user = new User($this->db);
      
      while ($val = $this->db->FetchArray($mysql_results))
      {
        $o_user->LoadFromId($val['user_id']);
        echo "<div class=\"comments\">\n";
        echo "<a name=\"".$val['id']."\"></a>\n";
        /* table générale */
        echo "<table>\n";
        echo "<tr><td>\n";
 
        /* table d'entete */
        echo "<div class=\"embedded\">\n";
        echo "<table class=\"com_header\">\n";
        echo "<tr>\n";
        echo "<td><a href=\"viewprofile.php?id=".$o_user->GetId()."\">".$o_user->GetPseudo()."</a></td><td style=\"text-align: right;\">".date($date_format,GetDateFromTimestamp($val['timestamp']))."</td>\n";
        
        if (Auth::Check(get_userlevel_by_name("moderator")))
        {
          echo "<td style=\"width: 16px;\">";
          echo "<a href=\"?to=comedit&amp;comid=".$val['id']."&amp;id=".$val['replay_id']."\">";
          echo $this->style->GetImage('edit', "Edit this comment" );
          echo "</a></td>\n";
          echo "<td style=\"width: 16px;\">";
          echo "<a href=\"?to=comdel&amp;comid=".$val['id']."&amp;id=".$val['replay_id']."\">";
          echo $this->style->GetImage('del', "Delete this comments" );
          echo "</a></td>\n";
        }

        else if (Auth::CheckUser($o_user->GetId()))
        {
          echo "<td style=\"width: 16px;\">";
          echo "<a href=\"?to=comedit&amp;comid=".$val['id']."&amp;id=".$val['replay_id']."\">";
          echo $this->style->GetImage('edit', "Edit this comment" );
          echo "</a></td>\n";
        }
        
        echo "</tr>\n";
        echo "</table>\n";
        echo "</div>\n";
        /* fin table d'entete */

        echo "</td></tr><tr><td>\n";

        /* table de contenu */
        echo "<div class=\"embedded\">\n";
        echo "<table>";
        $content = $this->bbcode->parse($val['content'], "all", false);
        $content = $this->smilies->Apply($content);
        echo "<tr>";
        echo "<td width=\"130px\" valign=\"top\"><center>".$o_user->GetAvatarHtml()."</center></td>\n";
        echo "<td class=\"com_content\">".$content."</td>\n";
        echo "</tr>\n";
        echo "</table>\n";
        echo "</div>\n";
        /* fin table de contenu */

        echo "</td></tr>\n";
        echo "</table>\n";
        /* fin table générale */

        echo "</div>\n";
      }
    }
  }

  function MemberList($mysql_results)
  {
    echo "<center>\n";
    echo "<div class=\"results\" style=\"width:650px; float: none; padding: 5 0 5 0;\">\n";
    echo "<table style=\"text-align: center;\"><tr>\n";
    echo "<th style=\"text-align: center;\"><a href=\"memberlist.php?sort=pseudo\">Name</a></th>\n";
    echo "<th style=\"text-align: center;\"><a href=\"memberlist.php?sort=records\">Records number</a></th>\n";
    echo "<th style=\"text-align: center;\"><a href=\"memberlist.php?sort=best\">Best records</a></th>\n";
    echo "<th style=\"text-align: center;\"><a href=\"memberlist.php?sort=best\">Rank</a></th>\n";
    echo "<th style=\"text-align: center;\"><a href=\"memberlist.php?sort=comments\">Comments</a></th>\n";
    echo "<th style=\"text-align: center;\"><a href=\"memberlist.php?sort=cat\">Category</a></th>\n";
    echo "</tr>\n";
    $i=0;
    while ($fields = $this->db->FetchArray($mysql_results))
    {
      $this->_MemberLine($i, $fields);
      $this->_JumpLine(5);
      $i++;
    }
    echo "</table>\n";
    echo "</div>\n";
    echo "</center>\n";
  }

  function UserProfile($o_user)
  {
    $user_id= $o_user->GetId();
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
    echo "<br/>\n";
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
    echo "<a href=\"".ROOT_PATH."index.php?folder=-1&filter=user_id&filterval=".$user_id." \">View all records of ".$pseudo."</a>\n";
    echo "<br/><a href=\"".ROOT_PATH."index.php?folder=".get_folder_by_name("contest")."&filter=user_id&filterval=".$o_user->GetId()." \">View all records (contest only)</a>\n";
    echo "</center>\n";
    
    echo "</td></tr>\n";
    echo "</table>\n";
    echo "</div>\n";
    /* fin table interne pagination */

    echo "</td></tr>\n";
    echo "</table>\n";
    echo  "</div>\n";
  }
  
  
  /*__FORMULAIRES__*/

  function CommentForm($replay_id, $content_memory="", $user_id=-1)
  {
    global $nextargs, $users_cache;

    if ($user_id === -1)
      $user_id = $_SESSION['user_id'];

    $pseudo = $users_cache[$user_id];

    echo  "<div class=\"nvform\" style=\"width: 600px;\">\n";
    echo  "<form method=\"post\" action=\"".$nextargs."\" name=\"commentform\">\n";
    echo  "<table><tr>\n";
    echo  "<td colspan=\"2\"><label for=\"pseudo\">name : </label>\n";
    echo  "<input type=\"text\" id=\"pseudo\" name=\"pseudo\" size=\"20\" readonly /></td>\n";
    
    echo  "</tr><tr>\n";
    echo  "<td colspan=\"2\"><center><textarea id=\"content\" name=\"content\" rows=\"12\" cols=\"90\"></textarea></center>\n";
    echo  "</td></tr><tr>\n";
  
    echo "<td colspan=\"2\"><center><input type=\"submit\" name=\"preview\" value=\"Preview\" />\n";
    echo  "<input type=\"submit\" value=\"Submit\" /></center>\n";
    echo  "<input type=\"hidden\" name=\"id\" id=\"id\" value=\"".$replay_id."\" /></td>\n";
    echo  "<input type=\"hidden\" name=\"user_id\" id=\"user_id\" value=\"".$user_id."\" /></td>\n";
    echo  "</tr><tr>\n";
    echo  "<td>\n";
    /* barre d'outils */
    global $toolbar_el;

    echo "<script type=\"text/javascript\" src=\"".ROOT_PATH."includes/js/toolbar.js\"></script>\n";

    foreach($toolbar_el as $key => $func)
    {
        echo "<a href=\"javascript:".$func."()\">".$this->style->GetIcon($key)."</a>";
    }

    echo "&nbsp;&nbsp;\n";

    echo "</td><td style=\"text-align: right\"\n";
   
    echo  "<a href=\"javascript:child=window.open('./smilies.php', 'Smiles', 'fullscreen=no,toolbar=no,status=no,menubar=no,scrollbars=no,resizable=yes,directories=no,location=no,width=270,height=200,left='+(Math.floor(screen.width/2)-140));child.focus()\">Add smiles \o/</a>";

    /* fin -- barre d'outils */
    echo  "</td></tr>\n";
    echo  "</table>\n";
    echo  "</form>\n";
    echo  "</div>\n\n";

    echo "<script type=\"text/javascript\">update_commentform_fields('".$pseudo."','".addcslashes($content_memory, "\0..\37")."')</script>\n";
  }

  function TypeForm($args)
  {
    global $types, $levelsets, $levels, $newonly;
  
    echo  "<div class=\"nvform\" style=\"width: 700px;\">\n";
    echo  "<form method=\"post\" action=\"?\" name=\"typeform\">\n";
    echo  "<table><tr>\n";
    echo  "<th colspan=\"6\">Selection switches</th>\n";
    echo  "</tr><tr>\n";
    echo  "<td><label for=\"table\">table select : </label>\n";
    echo  "<select name=\"type\" id=\"table\">\n";
  
    foreach ($types as $nb => $value)
      echo  "<option value=\"".$nb."\">".$types[$nb]["name"]."</option>\n";
  
    echo  "</select></td>\n";

    echo  "<td><label for=\"folder\">folder select : </label>\n";
    echo  "<select name=\"folder\" id=\"folder\">\n";
  
    /* Hard coded value for main page */
    echo  "<option value=\"0\">contest</option>\n";
    echo  "<option value=\"3\">old records</option>\n";
    echo  "<option value=\"-1\">all</option>\n";
    echo  "</select></td>\n";
  
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
  
    echo  "</tr></table>\n";
    echo "<br />\n";
  
    echo  "<table><tr>\n";
    echo  "<th colspan=\"6\">Filters</th>\n";
    echo  "</tr><tr>\n";
    echo  "<td><label for=\"diffview\">Diff view</label>\n";
    echo  "<input type=\"checkbox\" name=\"diffview\" id=\"diffview\" value=\"on\" /></td>\n";
    echo  "<td><label for=\"newonly\">Print only new records : </label>\n";
    echo  "<select name=\"newonly\" id=\"newonly\">\n";
  
    foreach ($newonly as $nb => $name)
      echo  "<option value=\"".$nb."\">".$name."</option>\n";
  
    echo  "</select></td>\n";
    echo  "</tr></table>\n";
    echo "<br />\n";
    echo  "<center><input type=\"submit\" value=\"Change\" /></center>\n";
  
    echo  "</form>\n";
    echo  "</div>\n\n";

    echo "<script type=\"text/javascript\">update_typeform_fields(".$args['type'].",".$args['folder'].",".$args['levelset_f'].",".$args['level_f'].",\"".$args['diffview']."\",".$args['newonly'].")</script>\n";
  }

  function EditForm()
  {

  }
  
  function AddFormAuto()
  {
    global $types,$levelsets, $levels, $nextargs;

    if (!Auth::Check(get_userlevel_by_name("member")))
    {
      button_error("You need to log in to post a record.", 400);
      return;
    }
    
    echo  "<div class=\"nvform\"  style=\"width: 600px;\">\n";
    echo  "<form enctype=\"multipart/form-data\" method=\"post\" action=\"upload.php?to=autoadd\" name=\"addform_auto\">\n";
    echo  "<table><tr>\n";
    echo  "<th colspan=\"6\">Auto add a record from replay file</th></tr><tr>\n";
    echo  "<td><label for=\"pseudo\">pseudo: </label></td>\n";
    echo  "<td colspan=\"3\"><input type=\"text\" id=\"pseudo\" name=\"pseudo\" size=\"10\" value=\"".$_SESSION['user_pseudo']."\" readonly /></td>";
    echo  "<td><input type=\"hidden\" id=\"user_id\" name=\"user_id\" size=\"10\" value=\"".$_SESSION['user_id']."\" readonly /></td><td></td></tr>\n<tr>";
    echo  "<td><label for=\"type\">type: </label></td>\n";
    echo  "<td><select id=\"type\" name=\"type\">\n";
  
    foreach ($types as $nb => $value)
    {
      if ($types[$nb]["name"] != "all")
          echo  "<option value=\"".$nb."\">".$types[$nb]["name"]."</option>\n";
    }
  
    echo  "</select>\n</td>";
    echo  "<td colspan=\"3\"><label for=\"goalnotreached\">Goal not reached</label>\n";
    echo  "<input type=\"checkbox\" name=\"goalnotreached\" id=\"goalnotreached\" value=\"on\" /></td>\n";
    
    echo "</tr><tr>\n";
 
    echo  "<td><label for=\"replayfile\">Replay file : </label></td>\n";
    echo  "<td colspan=\"5\"><input id=\"replayfile\" name=\"replayfile\" type=\"file\" /></td>\n";
    echo  "<td><input type=\"hidden\" name=\"MAX_FILE_SIZE\" value=\"".$config['upload_size_max']."\" />\n";

    echo "<input type=\"hidden\" id=\"folder\" name=\"folder\" value=\"".get_folder_by_name("incoming")."\" />\n";
    echo "</td>\n";
  
    echo  "</tr><tr><td>\n";

    echo  "<td colspan=\"2\"><center><input type=\"submit\" value=\"Go for it !\" /></center></td>\n";
    echo  "<td colspan=\"2\"><center><input type=\"reset\" value=\"Clear form\" /></center></td>\n";
    echo  "<td><input type=\"hidden\" id=\"to\" name=\"to\" value=\"autoadd\" /></td>\n";
    echo  "<td><input type=\"hidden\" id=\"user_id\" name=\"user_id\" value=\"".$_SESSION['user_id']."\" /></td>\n";
    echo  "</tr>\n";
  
    echo  "</table>\n";
    echo  "</form>\n";
    echo  "</div>\n\n";
  }


  /*__METHODES PRIVEES__*/
  
  function _RecordLine($i, $fields, $diffview=false, $diffref=array())
  {
    global $nextargs, $users_cache, $old_users;

    $rowclass = ($i % 2) ? "row1" : "row2";
    $tooltip=Javascriptize(GetShotMini($fields['levelset'], $fields['level'], 128));
    if ($i==0 && $diffview)
      echo  "<tr class=\"row1\" style=\"font-weight: bold;\"  onmouseover=\"return escape('".$tooltip."')\">\n";
    else
      echo  "<tr class=\"".$rowclass."\"  onmouseover=\"return escape('".$tooltip."')\">\n";

    $days=timestamp_diff_in_days(time(), GetDateFromTimestamp($fields['timestamp']));
       
    /* updated recently ? (2 days) */
    if($days<=3)
      echo "<td>".$this->style->GetImage('new')."</td>\n";
     else
        echo "<td></td>\n";
  
    /* old */
    echo "<td>".$days."&nbsp;d</td>\n";

    /* best ? */
    if($fields['isbest']==1)
      echo "<td>".$this->style->GetImage('best')."</td>\n";
    else
        echo "<td></td>\n";
      
    /* type image */
    echo  "<td>".$this->style->GetImage(get_type_by_number($fields['type']))."</td>\n";
  
    /* pseudo */
    if ($fields['user_id'] < 0)  /* utilisateur non inscrit */
      echo "<td>".$old_users[$fields['user_id']] ."</td>\n" ;
    else
    {
      echo  "<td><a href=\"viewprofile.php?id=".$fields['user_id']."\">";
      echo  $users_cache[$fields['user_id']] ."</a></td>\n" ;
    }
    /* set */
    echo  "<td>". get_levelset_by_number($fields['levelset']) ."</td>\n" ;
    /* level */
    
    echo  "<td><a href=\"index.php?levelset_f=".$fields['levelset']."&amp;level_f=".$fields['level']."\"> " . $fields['level'] . "</a></td>\n" ;
    /* time */
    if ($diffview && !empty($diffref) && $i>0) {
       $time = $fields['time'] - $diffref['time'];
       $sign_display=true;
    }
    else {
       $time = $fields['time'];
       $sign_display=false;
    }
       
    echo  "<td>" . sec_to_friendly_display($time, $sign_display) . "</td>\n" ;
    /* coins */
    if ($diffview && !empty($diffref) && $i>0)
    {
       $coins = $fields['coins'] - $diffref['coins'];
       /* ajout du signe + dans le cas du diff, c'est plus joli*/
       $coins = ($coins<0) ? $coins : "+".$coins;
    }
    else
       $coins = $fields['coins'];
    echo  "<td>" . $coins . "</td>\n" ;

    /* replay */
    if(!empty($fields["replay"]))
    {
      $replay  = replay_link($fields['folder'], $fields['replay']);
      echo  "<td>" . '<a href="' . $replay . '" type="application/octet-stream">replay</a>' . "</td>\n" ;
      $_SESSION['download_list'] .= replay_link($fields['folder'], $fields['replay']) . "\n";
    }
    else
    {
      echo  "<td>no replay</td>\n" ;
    }
      
    /* comments */
    $nb_comments = $fields['comments_count'];
    if ($nb_comments<1)
       $str = "comment !";
    else if ($nb_comments==1)
       $str = "1 comment";
    else
       $str = $nb_comments . "&nbsp;comments";
       echo  "<td><a href=\"record.php?id=".$fields['id']."\">".$str."</a></td>";
  
    /* "attach" */
    echo "<td><a href=\"?levelset_f=".$fields['levelset']."&amp;level_f=".$fields['level']."&amp;folder=-1\" title=\"Show all records for this level.\">";
    echo $this->style->GetImage('attach', "Show all records for this level.");
    echo "</a></td>";
     
    echo  "</tr>\n";
  }
  
  function _MemberLine($i, $fields)
  {
    global $nextargs, $config;

    $rowclass = ($i % 2) ? "row1" : "row2";
    echo  "<tr class=\"".$rowclass."\">\n";

    /* Name */
    echo "<td height=\"20px\">";
    if (!empty($fields['user_avatar']))
       $tooltip=Javascriptize("<center><img src=\"".ROOT_PATH.$config['avatar_dir']."/".$fields['user_avatar']."\" alt=\"\" /></center>");
    else
       $tooltip=Javascriptize("<center><i>No Avatar</i></center>");
    echo "<a href=\"viewprofile.php?id=".$fields['id']."\" onmouseover=\"return escape('".$tooltip."')\">";
    echo $fields['pseudo']."</a>\n";
    echo "</td>\n";

    /* Record number */
    echo "<td>";
    echo $fields['stat_total_records'];
    echo "</td>\n";

    /* Best records */
    echo "<td>";
    echo $fields['stat_best_records'];
    echo "</td>\n";

    /* Rank */
    echo "<td>";
    for($i=0; $i<CalculRank($fields['stat_best_records']); $i++)
      echo $this->style->GetImage('rank');
    echo "</td>\n";

    /* Comments */
    echo "<td>";
    echo $fields['stat_comments'];
    echo "</td>\n";

    /* Category */
    echo "<td>";
    echo get_userlevel_by_number($fields['level']);
    echo "</td>\n";

    echo "</tr>\n";
  }

  function _ResultsHeader()
  {
    global $nextargs;

    echo "<th style=\"width: 28px;\"></th>\n"; // new
    echo "<th><a href=\"".$nextargs."&amp;sort=old\">old</a></th>\n"; // days
    echo "<th style=\"width: 28px;\"></th>\n"; // best
    echo "<th style=\"width: 32px;\"></th>\n"; // type
    echo "<th style=\"width: 100px;\">player</th>\n";
    echo "<th><a href=\"".$nextargs."&amp;sort=level\">set</a></th>\n";
    echo "<th><a href=\"".$nextargs."&amp;sort=level\">lvl</a></th>\n";
    echo "<th><a href=\"".$nextargs."&amp;sort=time\">time</a></th>\n";
    echo "<th><a href=\"".$nextargs."&amp;sort=coins\">cns</a></th>\n";
    echo "<th></th>\n"; // replay
    echo "<th></th>\n"; // comments
    echo "<th></th>\n"; // goto same records
  }

  function _JumpLine($colspan)
  {
    echo "<tr><td colspan=\"".$colspan."\" style=\"background: #fff; height: 2px;\"></td></tr>\n";
  }
}

