<?php

# ***** BEGIN LICENSE BLOCK *****
# This file is part of Shinotag .
# Copyright (c) 2004 Francois Guillet and contributors. All rights
# reserved.
#
# Shinotag is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
# 
# Shinotag is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
# 
# You should have received a copy of the GNU General Public License
# along with Shinotag; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
#
# ***** END LICENSE BLOCK *****
class Tag_Dialog
{
  var $db;
  var $bbcode;
  var $smilies;
  var $cache;
  var $style;
  var $out;

  /*__CONSTRUCTEUR__*/
  function Tag_Dialog(&$db, &$cache, &$bbcode, &$smilies, &$style,  &$out)
  {
    $this->db = &$db;
    $this->cache = &$cache;
    $this->bbcode = &$bbcode;
    $this->smilies = &$smilies;
    $this->style = &$style;
    $this->out = &$out;
  }

  function Tags($moder=false)
  {
    global $config;

    // Set a id for this cache
    $id = 'tags';

    // Test if thereis a valide cache for this id
    if ($this->cache->Hit($id) && (!$moder))
    {
      $data = $this->cache->Read();
    }
    else 
    {
      $data = "<div id=\"tags\">\n";
      $data .= "<table>\n";
  
      $i=0;
      $res = $this->db->RequestMatchTags();
      if(!$res)
      {
        button_error($this->db->GetError());
      }
      else if ($this->db->NumRows()>0)
      {
        while ($val = $this->db->FetchArray())
        { 
          $data .= "<tr class=\"tagheader\">\n";
          $data .= "<td>\n";
          if ($moder)
          {
              $data .=  "<a href=\"?to=del&amp;id=".$val['id']."\">".
                  $this->style->GetImage('del', "Delete this comments" ).
                   "</a>\n";
              $data .= "<a href=\"?to=edit&amp;id=".$val['id']."\">".
                  $this->style->GetImage('edit', "Edit this comments" ).
                   "</a>\n";
          }
          if (empty($val['link']))
            $pseudo = $val['pseudo'];
          else
            $pseudo = "<a href=\"".$val['link']."\" target=\"_blank\">".$val['pseudo']."</a>";

          $data .= "<span class=\"tag_pseudo\">".$pseudo."</span><br />\n";
        
          $data .= "<span class=\"tag_date\">".date($config['date_format'],GetDateFromTimestamp($val['timestamp']))."</span>\n";
          $data .= "</td>\n";
          $data .= "</tr>\n";

          if ($i % 2)
            $data .=  "<tr class=\"tagmsg1\">\n";
          else
            $data .=  "<tr class=\"tagmsg2\">\n";
         $tag=$val['content'];
         $tag = $this->bbcode->parse($tag, "all", false);
         $tag = $this->smilies->Apply($tag);
         $data .= "<td>".$tag."</td>\n";
         $data .= "</tr>\n";
         $i++;
       }
     }
     $data .= "</table>\n";
     $data .= "</div>\n";

     if (!$moder)
         $this->cache->Create($id, $data);
    } // end cache
    $this->out .= $data;
  }

  function TagForm()
  {
    global $nextargs, $strings;

    if (isset($_SESSION['user_pseudo'])) $default_pseudo = $_SESSION['user_pseudo'];

    if   (empty($nextargs)) $a="index.php?tag";
    else $a = $nextargs . "&amp;tag";

    $this->out .= "<div id=\"tagform\"\n>";
    $this->out .=   "<form method=\"post\" action=\"".$a."\" name=\"tagform\" id=\"tagform\">\n";
    $this->out .=   "<table><tr>\n";
    $this->out .=   "<td><label for=\"tag_pseudo\">".$strings['tagform_pseudo']."</label></td></tr>\n";
    $this->out .=   "<tr><td><center><input type=\"text\" id=\"tag_pseudo\" name=\"tag_pseudo\" maxlength=\"14\" value
=\"".$default_pseudo."\" /></center></td></tr>\n";
    $this->out .=   "<tr><td><label for=\"tag_link\">".$strings['tagform_link']."</label></td></tr>\n";
    $this->out .=   "<tr><td><center><input type=\"text\" id=\"tag_link\" name=\"tag_link\" maxlength=\"64\" /></cente
r></td></tr>\n";
    $this->out .=   "<tr><td><label for=\"content\">";
    $this->out .=   $strings['tagform_content']."&nbsp;";
    $this->out .=   "<a href=\"javascript:child=window.open('".ROOT_PATH."popup_tagtools.php?referer_form=tagform', 'Smiles', 'fullscreen=no,toolbar=
no,status=no,menubar=no,scrollbars=no,resizable=yes,directories=no,location=no,width=270,height=300,
left='+(Math.floor(screen.width/2)-140));child.focus()\">(extras)</a>";
    $this->out .=   "</label></td></tr>\n";
    $this->out .=   "<tr><td><center><textarea id=\"content\" name=\"content\" rows=\"5\"></textarea></center>
\n";
    $this->out .=   "</td></tr>\n";
    $this->out .=   "<tr><td><center><input type=\"submit\" value=\"".$strings['tagform_submit']."\" /></center></td></tr>\n";
    $this->out .=   "</table>\n";
    $this->out .=   "</form>\n";
    $this->out .=  "</div>\n";
  }   
  
}
?>
