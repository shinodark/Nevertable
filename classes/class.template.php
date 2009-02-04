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
if (!defined('NVRTBL'))
	exit;
	
class Template
{
  var $smilies;
  var $bbcode;
  var $table;
  var $cache;
  
  var $tpl_cur_dir;
  var $tpl_def_dir;
  var $tpl_params;
  var $tpl_open;

  /*__CONSTRUCTEUR__*/
  function Template(&$parent)
  {
  	global $config;
  	
    $this->table = $parent;
    $this->smilies = new Smilies();
    $this->bbcode = new parse_bbcode("none");
    $this->cache  = new Cache("text");
    
    $this->tpm_args = array();
    $this->tpl_cur_dir = ROOT_PATH . $config['theme_dir'] . $this->table->style->GetStyle() . '/templates/';
    $this->tpl_def_dir = ROOT_PATH . $config['theme_dir'] . $config['theme_default'] . '/templates/';
    
    $this->tpl_open = false;
  }


  /**
   * Show a main template
   *
   * @param $tpl name of the template 
   * @param $tpl_params array of variables used by the template
   */
  function Show($tpl, $tpl_params = array(), $cache_id = "")
  {
  	$this->tpl_params = $tpl_params;
  	
  	/* Create variables inpout of the template */
  	if (!empty($tpl_params))
  	{
  		foreach ($tpl_params as $arg => $value)
  		{
  			$$arg = $value;
  		}
  	}
  	
  	/* Load template file and revert back on default theme if it's not found */

  	if($this->_CheckTplFile($tpl, $tplfile))
  	{
  		if (!$this->tpl_open)
  		{
	  		$this->tpl_open = true;
	  		if (!empty($cache_id) && $this->cache->Hit($cache_id))
	  		{
	  			echo $this->cache->Read($cache_id);
	  		}
	  		else
	  		{
				/* Buffering output to store data */
	  			if (!empty($cache_id))
	  			{
	  		       ob_start();
				}
	  		
	        	/* Load template */
				include $tplfile;
	  		
	  		    /* Create cache */
				if (!empty($cache_id))
				{
					$this->cache->Create($cache_id, ob_get_contents());
					ob_flush();
					ob_end_clean();
				}
	  		}
	  		$this->tpl_open = false;
  		}
  		else
  		{
  			echo "Multiple template inclusions error";
  		}
  	}
  	
  	/* Reset tpl parameters */
  	$this->tpl_params = array();

  }
  
  
  /**
   * Inlcude a sub template using current variables initialized by main template
   * This Method have to be used inside a template file to include sub templates
   *
   * @param $tpl name of the template to include
   * @param $add_tpl_params: additional parameters to add for this sub template
   */
  function SubTemplate($tpl, $add_tpl_params = array(), $sub_cache_id = "")
  {
  	/* Explode variables */
    if (!empty($this->tpl_params))
  	{
  		foreach ($this->tpl_params as $arg => $value)
  		{
  			$$arg = $value;
  		}
  	}
    if (!empty($add_tpl_params))
  	{
  		foreach ($add_tpl_params as $arg => $value)
  		{
  			$$arg = $value;
  		}
  	}
  	
    /* Load template file and revert back on default theme if it's not found */
  	if($this->_CheckTplFile($tpl, $tplfile))
  	{
  	  	if (!empty($sub_cache_id) &&$this->cache->Hit($sub_cache_id))
  		{
  			echo $this->cache->Read($sub_cache_id);
  		}
  		else
  		{
			/* Buffering output to store data */
  			if (!empty($sub_cache_id))
  			{
  		       ob_start();
			}
  		
        	/* Load template */
			include $tplfile;
  		
  		    /* Create cache */
			if (!empty($sub_cache_id))
			{
				$this->cache->Create($sub_cache_id, ob_get_contents());
				ob_end_clean();
			}
  		}
  	}
  }
  
  /*
   * Render raw text to html (bbcode and smilies)
   * @param: text Text to render
   * @return: rendered text
   */
  function RenderText($text)
  {
  	 $out = CleanContentHtml($text);
  	 $out = $this->bbcode->parse($out, "all", false);
     $out = $this->smilies->Apply($out);
     return $out;
  }
  
  function _CheckTplFile($tpl, &$tplfile)
  {
  	$tplfile = $this->tpl_cur_dir . $tpl . '.tpl.php';
    /* Load template file and revert back on default theme if it's not found */
  	if(!file_exists($tplfile))
  	{
  		$tplfile = $this->tpl_def_dir . $tpl . '.tpl.php';
  		if (!file_exists($tplfile))
  		{
  			echo "Error loading template " . $tplfile;
  			return false;
  		}
  	}
  	
  	return true;
  }
  
}
