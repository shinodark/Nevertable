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
/* Code taken mostly from punBB 1.3.2 http://www.punbb.fr/ */
if (!defined('NVRTBL'))
	exit;
	
//
// Truncate URL if longer than 55 characters (add http:// or ftp:// if missing)
//
function _handle_url_tag($url, $link = '')
{
	$full_url = str_replace(array(' ', '\'', '`', '"'), array('%20', '', '', ''), $url);
	if (strpos($url, 'www.') === 0)			// If it starts with www, we add http://
		$full_url = 'http://'.$full_url;
	else if (strpos($url, 'ftp.') === 0)	// Else if it starts with ftp, we add ftp://
		$full_url = 'ftp://'.$full_url;
	/*else if (!preg_match('#^([a-z0-9]{3,6})://#', $url)) 	// Else if it doesn't start with abcdef://, we add http://
		$full_url = 'http://'.$full_url;
    */
	$link = ($link == '' || $link == $url) ? ((utf8_strlen($url) > 55) ? utf8_substr($url, 0 , 39).' &#133; '.utf8_substr($url, -10) : $url) : stripslashes($link);

	return '<a href="'.$full_url.'">'.$link.'</a>';
}


//
// Turns an URL from the [img] tag into an <img> tag or a <a href...> tag
//
function _handle_img_tag($url, $alt = null)
{
	if ($alt == null)
		$alt = $url;

	$img_tag = '<img class="sigimage" src="'.$url.'" alt="'.htmlspecialchars($alt, ENT_QUOTES, 'UTF-8').'" />';

	return $img_tag;
}

//
// Parse the contents of [list] bbcode
//
function _handle_list_tag($content, $type = '*')
{
	if (strlen($type) != 1)
		$type = '*';

	if (strpos($content,'[list') !== false)
	{
		$pattern = array('/\[list(?:=([1a\*]))?\]((?>(?:(?!\[list(?:=(?:[1a\*]))\]|\[\/list\]).+?)|(?R))*)\[\/list\]/ems');
		$replace = array('handle_list_tag(\'$2\', \'$1\')');
		$content = preg_replace($pattern, $replace, $content);
	}

	$content = preg_replace('#\s*\[\*\](.*?)\[/\*\]\s*#s', '<li><p>$1</p></li>', trim($content));

	if ($type == '*')
		$content = '<ul>'.$content.'</ul>';
	else
		if ($type == 'a')
			$content = '<ol class="alpha">'.$content.'</ol>';
		else
			$content = '<ol class="decimal">'.$content.'</ol>';

	return '</p>'.$content.'<p>';
}

class parse_bbcode
{

  /**
   * function parse
   *
   * @param string : the string to transform
  **/
  function parse($text, $is_inline=false) {
  	
  	global $lang;
    
	if (strpos($text, '[quote') !== false)
	{
		if ($is_inline)
		{
			$text = preg_replace('#\[quote=(&quot;|"|\'|)(.*?)\\1\]#e', 'str_replace(array(\'[\', \'\\"\'), array(\'&#91;\', \'"\'), \'$2\')." ".$lang[\'WROTE\'].":&nbsp;<span class=\"quotebox\">"', $text);
			$text = preg_replace('#\[quote\]\s*#', '<span class="quotebox">', $text);
			$text = preg_replace('#\s*\[\/quote\]#S', '</span>', $text);
		}
		else
		{			
		 	$text = preg_replace('#\[quote=(&quot;|"|\'|)(.*?)\\1\]#e', '"<div class=\"quotebox\"><cite>".str_replace(array(\'[\', \'\\"\'), array(\'&#91;\', \'"\'), \'$2\')."&nbsp;".$lang[\'WROTE\'].":</cite><blockquote>"', $text);
		 	$text = preg_replace('#\[quote\]\s*#', '<div class="quotebox"><blockquote>', $text);
		 	$text = preg_replace('#\s*\[\/quote\]#S', '</blockquote></div>', $text);
		}
	}


	/* list */
	$pattern[] = '/\[list(?:=([1a\*]))?\]((?>(?:(?!\[list(?:=(?:[1a\*]))\]|\[\/list\]).+?)|(?R))*)\[\/list\]/ems';
	$replace[] = '_handle_list_tag(\'$2\', \'$1\')';

	$pattern[] = '#\[b\](.*?)\[/b\]#ms';
	$pattern[] = '#\[i\](.*?)\[/i\]#ms';
	$pattern[] = '#\[u\](.*?)\[/u\]#ms';
	$pattern[] = '#\[colou?r=([a-zA-Z]{3,20}|\#[0-9a-fA-F]{6}|\#[0-9a-fA-F]{3})](.*?)\[/colou?r\]#ms';
	if (!$is_inline)
		$pattern[] = '#\[h\](.*?)\[/h\]#ms';
	//$pattern[] = '#\[spoiler\](.*?)\[/spoiler\]#ms';

	$replace[] = '<strong>$1</strong>';
	$replace[] = '<em>$1</em>';
	$replace[] = '<span style="text-decoration: underline;">$1</span>';
	$replace[] = '<span style="color: $1">$2</span>';
	if (!$is_inline)
		$replace[] = '<h5>$1</h5>';
	//$replace[] = '<blockquote><div class="quotebox" onclick="pchild=this.getElementsByTagName(\'p\'); if(pchild[0].style.visibility!=\'hidden\'){pchild[0].style.visibility=\'hidden\'; pchild[0].style.height=\'0\';}else{pchild[0].style.visibility=\'\'; pchild[0].style.height=\'\';}"><span style="font-style:italic">Spoiler:</span><p style="visibility:hidden; height:0;">$1</p></div></blockquote>';


	$pattern[] = '#\[img\]((ht|f)tps?://)([^\s<"]*?)\[/img\]#e';
	$pattern[] = '#\[img=([^\[]*?)\]((ht|f)tps?://)([^\s<"]*?)\[/img\]#e';

	$replace[] = '_handle_img_tag(\'$1$3\')';
	$replace[] = '_handle_img_tag(\'$2$4\', \'$1\')';

	$pattern[] = '#\[url\]([^\[]*?)\[/url\]#e';
	$pattern[] = '#\[url=([^\[]+?)\](.*?)\[/url\]#e';
	$pattern[] = '#\[email\]([^\[]*?)\[/email\]#';
	$pattern[] = '#\[email=([^\[]+?)\](.*?)\[/email\]#';

	$replace[] = '_handle_url_tag(\'$1\')';
	$replace[] = '_handle_url_tag(\'$1\', \'$2\')';
	$replace[] = '<a href="mailto:$1">$1</a>';
	$replace[] = '<a href="mailto:$1">$2</a>';

	$text = preg_replace($pattern, $replace, $text);


	return $text;
  }
 
}

?>
