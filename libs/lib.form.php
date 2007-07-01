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

class Form
{

    var $output;

    /*__Constructeur__
     Cette fonction initialise l'objet Form.
    */
    function Form($method, $action, $name, $width="", $enctype="", $class="generic_form")
    {
       $inline1 = ' class="'.$class.'"';
       if (!empty($width))
	 $inline2 = ' style="width: '.$width.'px;"';
       if (!empty($enctype))
	 $inline3 = ' enctype="'.$enctype.'"';
       $this->output  = "<div ".$inline1.$inline2.">\n"; 
       $this->output .= '<form method="'.$method.'" action="'.$action.'" name="'.$name.'" '.$inline3.'>'."\n"; 
       $this->output .= '<table><tr>'; 
       $this->output .= "\n"; 
    }

    function End()
    {
       $this->output .= "</tr></table>"; 
       $this->output .= "</form>\n"; 
       $this->output .= "</div>\n"; 
       $this->output .=  "\n\n";
       return $this->output;
    }
    
    function Br()
    {
       $this->output .= "</tr><tr>\n"; 
    }

    function AddTitle($title, $align="center")
    {
	$this->output .= '<th colspan="2" align="'.$align.'">'.$title.'</th></tr><tr>'."\n";
    }

    function AddLine($text="")
    {
	$this->output .= '<td colspan="2"><center>'.$text.'</center></td></tr><tr>'."\n";
    }
    
    function _AddInput($type, $id, $name, $label="", $size=20, $value, $options="")
    {
       $colspan=2;
       if (!empty($label))
       {
           $this->output .= '<td><label for="'.$name.'">'.$label.'</label></td>'."\n" ;
	   $colspan = 1;
       }
       if (!empty($size))
	  $inline1 = ' size="'.$size.'"';
       if (!empty($value))
	  $inline2 = ' value="'.$value.'"';
       if (!empty($options))
	  $inline3 = ' '.$options.' ';
       $this->output .= '<td colspan="'.$colspan.'"><input type="'.$type.'" id="'.$id.'" name="'.$name.'" '.$inline1.$inline2.$inline3.' /></td>'."\n";
    }
    
    function AddInputText($id, $name, $label="", $size=20, $value="", $options="")
    {
       $this->_AddInput("text", $id, $name, $label, $size, $value, $options);
    }
    
    function AddInputPassword($id, $name, $label="", $size=20, $value="", $options="")
    {
       $this->_AddInput("password", $id, $name, $label, $size, $value, $options);
    }
    
    function AddInputCheckbox($id, $name, $label="", $size=20, $value="on", $options="")
    {
       $this->_AddInput("checkbox", $id, $name, $label, $size, $value, $options);
    }
    
    function AddInputHidden($id, $name, $label="", $size=20, $value="", $options="")
    {
       $this->_AddInput("hidden", $id, $name, $label, $size, $value, $options);
    }
    
    function AddInputFile($id, $name, $label="", $size=20, $value="", $options="")
    {
       $this->_AddInput("file", $id, $name, $label, $size, $value, $options);
    }
    
    function AddTextArea($id, $name, $label="", $rows=5, $content="")
    {
       $colspan=2;
       if (!empty($label))
       {
           $this->output .= '<td><label for="'.$name.'">'.$label.'</label></td>'."\n" ;
	   $colspan = 1;
       }
       if (!empty($content))
	  $inline1 = $content;
       $this->output .= '<td colspan="'.$colspan.'"><textarea id="'.$id.'" name="'.$name.'" rows="'.$rows.'" style="width:100%;">'.$inline1.'</textarea></td>'."\n";
    }

    function AddSelect($id, $name, $select_array, $label="")
    {
       $colspan=2;
       if (!empty($label))
       {
           $this->output .= '<td><label for="'.$name.'">'.$label.'</label></td>'."\n" ;
	   $colspan = 1;
       }
       $this->output .= '<td><select id="'.$id.'" name="'.$name.'">';
       foreach ($select_array as $key => $value)
         $this->output .= '  <option value="'.$key.'">'.$value.'</option>';
       $this->output .= '</select></td>';
    }

    function AddInputSubmit($value="", $name="")
    {
       if (!empty($value))
          $inline1 = ' value="'.$value.'"';
       if (!empty($name))
          $inline2 = ' name="'.$name.'"';
       $this->output .= '<td colspan="2"><center><input type="submit" '.$inline1.$inline2.' /></center></td>'."\n";
    }
    
    function AddInputReset($value="", $name="")
    {
       if (!empty($value))
          $inline1 = ' value="'.$value.'"';
       if (!empty($name))
          $inline2 = ' name="'.$name.'"';
       $this->output .= '<td colspan="2"><center><input type="reset" '.$inline1.$inline2.' /></center></td>'."\n";
    }
    
    function AddInputExtraBirthday($label="")
    {
       global $days_numbers, $months_numbers, $years_numbers;

       $colspan=2;
       if (!empty($label))
       {
           $this->output .= '<td><label for="'.$name.'">'.$label.'</label></td>'."\n" ;
	   $colspan = 1;
       }
       $this->output .= '<td colspan="'.$colspan.'">';
       $this->output .= '<select id="birthday_day" name="birthday_day">';
       foreach ($days_numbers as $key)
          $this->output .= '  <option value="'.$key.'">'.$key.'</option>';
       $this->output .= '</select>';
       $this->output .= '/';
       $this->output .= '<select id="birthday_month" name="birthday_month">';
       foreach ($months_numbers as $key)
          $this->output .= '  <option value="'.$key.'">'.$key.'</option>';
       $this->output .= '</select>';
       $this->output .= '/';
       $this->output .= '<select id="birthday_year" name="birthday_year">';
       foreach ($years_numbers as $key)
          $this->output .= '  <option value="'.$key.'">'.$key.'</option>';
       $this->output .= '</select>';
       $this->output .= '</td>';
    }
    
}
