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

include_once ROOT_PATH ."classes/class.dialog.php";

class DialogAdmin extends Dialog
{
  /*__CONSTRUCTEUR__*/
  function DialogAdmin(&$db, $parent, $o_style)
  {
    parent::Dialog($db , $parent, $o_style);
  }

  function Prelude()
  {
    global $lang;

    $this->output  =   "<div id=\"prelude\">\n";
    $this->output .=   "<center>\n";
    $this->output .=   '<a href="?folder='.get_folder_by_name("incoming").'&amp;type='.get_type_by_name("all").'">';
    $this->output .=   sprintf($lang['ADMIN_PRELUDE_INCOMING'],
           	       $this->db->helper->RequestCountRecords(get_folder_by_name("incoming"), get_type_by_name("all"))
    		       );
    $this->output .=   "</a> &nbsp; | &nbsp; \n";
    $this->output .=   '<a href="?folder='.get_folder_by_name("trash").'&amp;type='.get_type_by_name("all").'">';
    $this->output .=   sprintf($lang['ADMIN_PRELUDE_TRASH'],
           	       $this->db->helper->RequestCountRecords(get_folder_by_name("trash"), get_type_by_name("all"))
    		       );
    $this->output .=   "</a> &nbsp; | &nbsp; \n";
    $this->output .=   '<a href="?folder='.get_folder_by_name("oldones").'&amp;type='.get_type_by_name("all").'">';
    $this->output .=   sprintf($lang['ADMIN_PRELUDE_OLDONES'],
           	       $this->db->helper->RequestCountRecords(get_folder_by_name("oldones"), get_type_by_name("all"))
    		       );
    $this->output .=   "</a> &nbsp; | &nbsp; \n";
    $this->output .=   '<a href="?folder='.get_folder_by_name("contest").'&amp;type='.get_type_by_name("all").'">';
    $this->output .=   sprintf($lang['ADMIN_PRELUDE_CONTEST'],
           	       $this->db->helper->RequestCountRecords(get_folder_by_name("contest"), get_type_by_name("all"))
    		       );
    $this->output .=   "</a>\n";
    $this->output .=   "</center>\n";
    $this->output .=   "</div>\n";
    
    $this->Output();
  }
  
  function SideBar()
  {
    global $lang;

    $bar = new SideBar();

    $menu_main = new Menu();
    $menu_main->AddItem($lang['ADMIN_MENU_CHECK'], "admin.php?check");
    $menu_main->AddItem($lang['ADMIN_MENU_RECOMPUTE'], "admin.php?recompute");
    $menu_main->AddItem($lang['ADMIN_MENU_CONFIG'], "config.php");
    $menu_main->AddItem($lang['ADMIN_MENU_MANAGEMENT'], "management.php");
    $menu_main->AddItem($lang['ADMIN_MENU_MEMBERS'], "memberlist.php");
    $menu_main->AddItem($lang['ADMIN_MENU_SETS'] , "sets.php");
    $menu_main->AddItem($lang['ADMIN_MENU_FILE_EXPLORER'], "filexplorer.php");
    $menu_main->AddItem($lang['ADMIN_MENU_PURGE_TRASH'] , "purgetrash.php");
    $menu_main->AddItem($lang['ADMIN_MENU_TAGBOARD_MOD'], "tag_moder.php");
    $menu_main->AddItem($lang['ADMIN_MENU_LEAVE'], "../index.php");

    $bar->AddBlock_MenuBar($lang['ADMIN_MENU_TITLE'], $menu_main);
    
    $this->output = $bar->End();
    $this->Output();
  }
  
  function Table($mysql_results, $args)
  {
    global $nextargs;
    
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

  function Record($fields)
  {
  	global $nextargs;
  
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
        gui_button_error($u->GetError(), 400);
    if(!$s->LoadFromId($fields['levelset']))
        gui_button_error($s->GetError(), 400);
    $fields['pseudo'] = $u->GetPseudo();
    $fields['set_name'] = $s->GetName();
    $this->_RecordLine(0, $fields, false);
    
    $this->output .=  "</table>\n";
    $this->output .=  "</div>\n";
    
    $this->Output();
  }

  function MemberList($mysql_results)
  {
    global $lang;

    $this->output  =  "<center>\n";
    $this->output .=  "<div class=\"results\" style=\"width:100%; float: none;\">\n";
    $this->output .=  "<table style=\"text-align: center;\"><tr>\n";
    $this->output .=  "<caption>".$lang['ADMIN_MEMBERS_TITLE']."</caption>\n";
    $this->output .=  "<th style=\"text-align: center;\"><a href=memberlist.php?sort=id>#</a></th>\n";
    $this->output .=  "<th style=\"text-align: center;\"><a href=memberlist.php?sort=pseudo>".$lang['MEMBER_HEADER_NAME']."</a></th>\n";
    $this->output .=  "<th style=\"text-align: center;\"><a href=memberlist.php?sort=records>".$lang['MEMBER_HEADER_RECORDS']."</a></th>\n";
    $this->output .=  "<th style=\"text-align: center;\"><a href=memberlist.php?sort=best>".$lang['MEMBER_HEADER_BEST_RECORDS']."</a></th>\n";
    $this->output .=  "<th style=\"text-align: center;\"><a href=memberlist.php?sort=comments>".$lang['MEMBER_COMMENTS']."</a></th>\n";
    $this->output .=  "<th style=\"text-align: center;\">".$lang['MEMBER_MAIL']."</th>\n";
    $this->output .=  "<th style=\"text-align: center;\"><a href=memberlist.php?sort=cat>".$lang['MEMBER_CATEGORY']."</a></th>\n";
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
  
  /*__FORMULAIRES__*/

  function TypeForm($args)
  {
    global $types, $levels, $folders, $newonly, $lang;
  
    $this->output .=   "<div class=\"generic_form\" style=\"width: 700px;\">\n";
    $this->output .=   "<form method=\"post\" action=\"?\" name=\"typeform\">\n";
    $this->output .=   "<table><tr>\n";
    $this->output .=   "<th colspan=\"6\">".$lang['TYPE_FORM_TITLE']."</th>\n";
    $this->output .=   "</tr><tr>\n";
    $this->output .=   "<td><label for=\"table\">".$lang['TYPE_FORM_TABLE_SELECT']."</label>\n";
    $this->output .=   "<select name=\"type\" id=\"table\">\n";
  
    foreach ($types as $nb => $value)
      $this->output .=   "<option value=\"".$nb."\">".$lang[$types[$nb]]."</option>\n";
  
    $this->output .=   "</select></td>\n";

    $this->output .=   "<td><label for=\"folder\">".$lang['TYPE_FORM_FOLDER_SELECT']."</label>\n";
    $this->output .=   "<select name=\"folder\" id=\"folder\">\n";
  
    foreach ($folders as $nb => $name)
    {
      $this->output .=   "<option value=\"".$nb."\">".$lang[$name]."</option>\n";
    }
    $this->output .=   "</select></td>\n";
  
    $this->output .=   "<td><label for=\"levelset_f\">".$lang['TYPE_FORM_SET']."</label>\n";
    $this->output .=   "<select name=\"levelset_f\" id=\"levelset_f\">\n";
    $this->output .=     "<option value=\"0\">".$lang['all']."</option>\n";
    foreach ($this->db->helper->GetSets() as $id => $name)
    {
      $this->output .=   "<option value=\"".$id."\">".$name."</option>\n";
    }
    $this->output .=   "</select></td>\n";
    $this->output .=   "<td><label for=\"level_f\">".$lang['TYPE_FORM_LEVEL']."</label>\n";
    $this->output .=   "<select name=\"level_f\" id=\"level_f\">\n";
    $this->output .=   "<option value=\"0\">".$lang['all']."</option>\n";
    foreach ($levels as $name => $value)
    {
      $this->output .=   "<option value=\"".$value."\">".$name."</option>\n";
    }
    $this->output .=   "</select></td>\n";
  
    $this->output .=   "</tr></table>\n";
    $this->output .=  "<br />\n";
  
    $this->output .=   "<table><tr>\n";
    $this->output .=   "<th colspan=\"6\">".$lang['TYPE_FORM_FILTERS']."</th>\n";
    $this->output .=   "</tr><tr>\n";
    $this->output .=   "<td><label for=\"diffview\">".$lang['TYPE_FORM_DIFFVIEW']."</label>\n";
    $this->output .=   "<input type=\"checkbox\" name=\"diffview\" id=\"diffview\" value=\"on\" /></td>\n";
    $this->output .=   "<td><label for=\"newonly\">".$lang['TYPE_FORM_NEWONLY']."</label>\n";
    $this->output .=   "<select name=\"newonly\" id=\"newonly\">\n";
  
    foreach ($newonly as $nb => $name)
      $this->output .=   "<option value=\"".$nb."\">".$lang[$name]."</option>\n";
  
    $this->output .=   "</select></td>\n";
    $this->output .=   "</tr></table>\n";
    $this->output .=  "<br />\n";
    $this->output .=   "<center><input type=\"submit\" value=\"".$lang['GUI_BUTTON_APPLY']."\" /></center>\n";
  
    $this->output .=   "</form>\n";
    $this->output .=   "</div>\n\n";

    $this->output .= "<script type=\"text/javascript\">\n";
      $this->output .=  "change_form_select('typeform', 'type',".$args['type'].");\n";
      $this->output .=  "change_form_select('typeform', 'folder',".$args['folder'].");\n";
      $this->output .=  "change_form_select('typeform', 'levelset_f',".$args['levelset_f'].");\n";
      $this->output .=  "change_form_select('typeform', 'level_f',".$args['level_f'].");\n";
      $this->output .=  "change_form_checkbox('typeform', 'diffview','".$args['diffview']."');\n";
      $this->output .=  "change_form_select('typeform', 'newonly',".$args['newonly'].");\n";
    $this->output .= "</script>\n";

    $this->Output();
  }

  function EditForm()
  {
    global $types, $levels, $nextargs, $lang;
    
    $this->output  =   "<div class=\"generic_form\" style=\"width: 650px;\">\n";
    $this->output .=   "<form method=\"post\" action=\"admin.php?edit\" name=\"editform\">\n";
    $this->output .=   "<table><tr>\n";
    $this->output .=   "<th colspan=\"6\">".$lang['ADMIN_EDIT_FORM_TITLE']."</th></tr><tr>\n";
    $this->output .=   "<td><label for=\"id\">id:</label></td>\n";
    $this->output .=   "<td><input type=\"text\" id=\"id\" name=\"id\" size=\"3\" readonly />\n";
    $this->output .=   "</td></tr><tr>\n";
    /* pseudo est donné à titre indicatif, non utilisé */
    $this->output .=   "<td><label for=\"pseudo\">".$lang['ADMIN_EDIT_FORM_PSEUDO']."</label></td>\n";
    $this->output .=   "<td><input type=\"text\" id=\"pseudo\" name=\"pseudo_info\" size=\"15\" readonly /></td>\n";
    $this->output .=   "<td><label for=\"user_id\"> # </label></td>\n";
    $this->output .=   "<td><input type=\"text\" id=\"user_id\" name=\"user_id\" size=\"5\" readonly /></td><td colspan=\"2\"></td></tr>\n<tr>";
    $this->output .=   "<td><label for=\"levelset\">".$lang['ADMIN_EDIT_FORM_SET'] ."</label></td>\n";
    $this->output .=   "<td><select name=\"levelset\" id=\"levelset\">\n";
  
    foreach ($this->db->helper->GetSets() as $id => $name)
    {
      $this->output .=   "<option value=\"".$id."\">".$name."</option>\n";
    }
  
    $this->output .=   "</select></td>\n";
    $this->output .=   "<td><label for=\"level\">".$lang['ADMIN_EDIT_FORM_LEVEL']."</label></td>\n";
    $this->output .=   "<td><select name=\"level\" id=\"level\">\n";
  
    foreach ($levels as $name => $value)
    {
      $this->output .=   "<option value=\"".$value."\">".$name."</option>\n";
    }
  
    $this->output .=   "</select>\n</td>\n";
    $this->output .=   "<td><label for=\"type\">".$lang['ADMIN_EDIT_FORM_TYPE']."</label></td>\n";
    $this->output .=   "<td><select id=\"type\" name=\"type\">\n";
  
    foreach ($types as $nb => $value)
    {
      if ($value != "all")
          $this->output .=   "<option value=\"".$nb."\">".$lang[$value]."</option>\n";
    }
  
    $this->output .=   "</select>\n</td></tr><tr>\n";

    $this->output .=   "<td colspan=\"3\"><label for=\"time\">".$lang['ADMIN_EDIT_FORM_TIME']."</label></td>\n";
    $this->output .=   "<td><input type=\"text\" id=\"time\" name=\"time\" size=\"7\" /></td>\n";
    $this->output .=   "<td><label for=\"coins\">".$lang['ADMIN_EDIT_FORM_COINS']."</label></td>\n";
    $this->output .=   "<td><input type=\"text\" id=\"coins\" name=\"coins\" size=\"5\"/></td></tr>\n<tr>";
    $this->output .=   "<td><label for=\"replay\">".$lang['ADMIN_EDIT_FORM_REPLAY_FILE']."</label></td>\n";
    $this->output .=   "<td colspan=\"5\"><input type=\"text\" id=\"replay\" name=\"replay\" size=\"50\" />\n";
  
    $this->output .=   "</td></tr><tr>\n";


    $this->output .=   "<td colspan=\"2\"></td>\n";
    $this->output .=   "<td colspan=\"1\"><center><input type=\"submit\" value=\"".$lang['GUI_BUTTON_APPLY']."\" /></center></td>\n";
    $this->output .=   "<td colspan=\"1\"><center><input type=\"reset\" /></center></td>\n";
    $this->output .=   "<td colspan=\"2\">\n";
    $this->output .=   "<input type=\"hidden\" id=\"to\" name=\"to\" value=\"edit\" />\n";
    $this->output .=   "</td>\n";
    $this->output .=   "</tr>\n";
  
    $this->output .=   "</table>\n";
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
    $this->output .= "<script type=\"text/javascript\">\n";
      $this->output .=  "function change_editform(idP,user_idP, pseudoP, levelsetP, levelP, timeP, coinsP, replayP, typeP) {;\n";
        $this->output .=  "change_form_input('editform', 'id', idP);\n";
        $this->output .=  "change_form_input('editform', 'user_id', user_idP);\n";
        $this->output .=  "change_form_input('editform', 'pseudo', pseudoP);\n";
        $this->output .=  "change_form_select('editform', 'levelset', levelsetP-1);\n";
	$this->output .=  "change_form_select('editform', 'level', levelP);\n";
        $this->output .=  "change_form_input('editform', 'time', timeP);\n";
        $this->output .=  "change_form_input('editform', 'coins', coinsP);\n";
        $this->output .=  "change_form_input('editform', 'replay', replayP);\n";
	$this->output .=  "change_form_select('editform', 'type', typeP-1);\n";
      $this->output .= "}\n";
    $this->output .= "</script>\n";

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
      $res = $this->db->helper->MatchUserById($fields['user_id']);
      $val = $this->db->FetchArray($res);
      $this->output .=   "<td><a href=\"mailto:".$val['email']." \">";
      $this->output .=   $fields['pseudo'] ."</a></td>\n" ;
    }
    else
    {
      if (!empty($fields['pseudo'])) /* utilisateur non inscrit */
        $this->output .=  "<td>".$fields['pseudo'] ."</td>\n" ;
      else
      {
        $this->output .=   "<td><a href=\"editprofile.php?id=".$fields['user_id']."\">";
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
    $this->output .=  "<a href=\"?".$action."&amp;id=".$fields['id']."\">";
    $this->output .=  $this->style->GetImage($img, $bull);
    $this->output .=  "</a></td>\n";

    /* to contest */
    if ($fields['folder'] != get_folder_by_name("contest") && $fields['folder'] != get_folder_by_name("incoming"))
    {
      $this->output .=  "<td style=\"width: 16px;\">";
      $this->output .=  "<a href=\"?repcontest&amp;id=".$fields['id']."\">";
      $this->output .=  $this->style->GetImage('undo', "Reinject in contest");
      $this->output .=  "</a></td>\n";
    }
    else if ($fields['folder'] == get_folder_by_name("incoming"))
    {
      $this->output .=  "<td style=\"width: 16px;\">";
      $this->output .=  "<a href=\"?repcontest&amp;id=".$fields['id']."\">";
      $this->output .=  $this->style->GetImage('tocontest+', "Reinject in contest, always add");
      $this->output .=  "</a></td>\n";
      $this->output .=  "<td style=\"width: 16px;\">";
      $this->output .=  "<a href=\"?repcontest&amp;overwrite=on&amp;id=".$fields['id']."\">";
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
    global $nextargs, $userlevel, $lang;

    $readonly = Auth::Check(get_userlevel_by_name("root")) ? "" : "readonly" ;
    
    $rowclass = ($i % 2) ? "row1" : "row2";
    $this->output .=   "<tr class=\"".$rowclass."\">\n";
    
    $this->output .=  "<form name=\"memberform_".$fields['id']."\" id=\"memberform_".$fields['id']."\" method=\"post\" action=\"memberlist.php?upmember&amp;id=".$fields['id']."\" >\n";

    /* id */
    $this->output .=  "<td>";
    $this->output .=  "<a href=\"editprofile.php?id=".$fields['id']."\" >".$fields['id']."</a>";
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
    $this->output .=  "<input type=\"submit\" value=\"".$lang['ADMIN_MEMBERS_FORM_UPDATE']."\" />";
    $this->output .=  "</td>\n";
    $this->output .=  "</form>\n";

    /* Delete */
    $this->output .=  "<form name=\"memberdelete_".$fields['id']."\" method=\"post\" action=\"memberlist.php?delmember&amp;id=".$fields['id']."\" >\n";
    $this->output .=  "<td>";
    $this->output .=  "<input type=\"submit\" value=\"".$lang['ADMIN_MEMBERS_FORM_DELETE']."\" />";
    $this->output .=  "</td>\n";
    $this->output .=  "</form>\n";

    $this->output .=  "</tr>\n";
    
    $this->output .= "<script type=\"text/javascript\">\n";
      $this->output .=  "change_form_select('memberform_".$fields['id']."', 'authlevel', ".$i." )";
    $this->output .= "</script>\n";
  }
  
  function _JumpLine($colspan)
  {
    $this->output .=  "<tr><td colspan=\"".$colspan."\" style=\"background: #fff; height: 2px;\"></td></tr>\n";
  }
}
