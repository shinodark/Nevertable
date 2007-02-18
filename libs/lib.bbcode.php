<?php
/*******
*				 Class parse_bbcode
*
* @autor : Dracula
* @contact : maitre.dracula@free.fr
* @date : 11/2003
* @phpversion : 4
* @description : class to parse text which contains bbcodes.
* @utilisation : 
*
	
	$parserbbcode = new parse_bbcode(optionnal phpmethod);
	
	// phpmethod : string "string" for just colour phpcode
	//					   string "page" for colour phpcode and put it in a table, with alternate colour row and rows nubers.
	// "page" by default
	
	echo $parserbbcode->parse($bbcodetext, optionnal methods, optionnal strip_html_tags);
	
	// methods : string "all" for parse bbcode text with all methods
						array "array(method1, metod2, ...)" for parse the bbcode text with only methods you want.
						
	// strip_html_tags : "true" for colour and show html tags 
	//							   "false" for keep html tags
	//	"true" by default
								   
	
	
@example : 
	
	$parserbbcode = new parse_bbcode();
	echo $parserbbcode->parse($bbcodetext);
*
*	
*******/



class parse_bbcode
{

  // variable phpmethod : "string" for colour php code,
 // "page" to colour and show it in a table with number of line
  var $phpmethod;

  // Constructor (nothing to load)
  function parse_bbcode($phpmethod = "page") {
  $this->phpmethod = $phpmethod;
    return true;
  }

  /**
   * function parse
   *
   * @param string : the string to transform
   * @param Array of string : list of tokens
  **/
  function parse($text, $methods = "all", $striptags = true) {
    $text = str_replace("<?", "[php]", $text);
  $text = str_replace("?>", "[/php]", $text);
  
    $ret_val = $striptags ? $this->html($text) : $text;
	    if (is_array($methods)) {
      foreach ($methods AS $method) {
	if (method_exists($this, "_$method")) {
	  $ret_val = $this->{"_$method"}($ret_val);
	}
      }
    } elseif($methods == "all") {
      foreach(get_class_methods($this) AS $method) {
	if (ereg('^_.*$', $method)) {
	  $ret_val = $this->{$method}($ret_val);
	}
      }
    } else {
      $ret_val = false;
    }
	$ret_val = str_replace("\\'","'", $ret_val);
	$ret_val = str_replace("\\\"","\"", $ret_val);
	
	$ret_val = $striptags ? $this->nltobr($ret_val) : $ret_val;
    return $ret_val;
  }

  // parse a file
  function parsefile($file, $methods = "all", $striptags = true) {
    if (!file_exists($file)) {
      return false;
    }
    return $this->parse(implode('', file($file)), $methods, $striptags);
  }
  
  
  													### privates functions ###
//******************************************************************* 
  // html function: colour html tags. 
  function html($text)
  {
		$In = array(
			'`&lt;(/?[^ &<]+ *)&gt;`',
			'`([^ =]+)="(.*?)"`',
			'`&lt;([^< ]+)`',
			'`&lt;([^&]+?)&gt;`' ,
			'`(?<=[[:space:]])[[:space:]]`'
			);
		$Out = array(
			'<span class="html">&lt;$1&gt;</span>',
			'<span class="attribut">$1="</span><span class="value">$2</span><span class="attribut">"</span>',
			'&lt;$1',
			'<span class="html">&lt;$1&gt;</span>',
			' &nbsp;'
			);


		$text =  preg_replace($In, $Out, nl2br(htmlentities($text, ENT_NOQUOTES)));

	RETURN $text;
  }
  
  // \n to <br> tag
  function  nltobr($text) {
 RETURN  str_replace("\n","<br>",$text);
  }		
  
  
  
  /*
   * elementary functions
   */
				
	  // email bbcode [email]bob@bob.net[/email]
  function _email($text) {
    return preg_replace("#\[email\](.*?)\[/email\]#si", "<a href=\"mailto:\\1\">\\1</a>", $text);
  }


  // bold bbcode [b]bold[/b]
  function _bold($text) {
    return preg_replace("#\[b\](.*?)\[/b\]#si", "<b>\\1</b>", $text);
  }
  
  // italic bbcode [i]italic[/i]
  function _italique($text) {
    return preg_replace("#\[i\](.*?)\[/i\]#si", "<i>\\1</i>", $text);
  }
  
  // Underline bbcode [u]underline[/u]
  function _underline($text) {
    return preg_replace("#\[u\](.*?)\[/u\]#si", "<u>\\1</u>", $text);
  }
  
  // link maker bbcode [url=www.google.fr]google[/url]
  function _url($text) {
   if (ereg_replace('://', '', $text)) {
      $remplacement = '<a href="\1" target="_blank">\2</a>';
    } else {
      $remplacement = '<a href="http://\1" target="_blank">\2</a>';
    }
    return preg_replace("#\[url=(.*?)\](.*?)\[/url\]#si", $remplacement, $text);
  }
  
  // image bbcode [img]my_image.gif[/img]
  function _img($text) {
    return preg_replace("#\[img\](.*?)\[/img\]#si", "<img src=\"\\1\" border=\"none\">", $text);
  }

  // br bbcode
  function _br($text) {
    return ereg_replace('\[br\]', '<br />', $text);
  }
  
 
  // center bbcode
  function _center($text) {
  RETURN $text = preg_replace("|\[(/?center)\]|Ui","<\\1>",$text);
  }
  
  // citation bbcode
  function _quote($text) {
   $text = preg_replace("|\[quote\]|Ui","<blockquote>",$text);
   $text = preg_replace("|\[quote=[\"']?([^\"']+)[\"']?\]|Ui","<blockquote>\\1 wrote:<br />",$text);
   $text = preg_replace("|\[/quote\]|Ui","</blockquote>",$text);
   RETURN $text;
  }
  
  // hr bbcode
  function _hr($text) {
  RETURN preg_replace("|\[hr\]|Ui","<hr noshade size=1 />",$text);
  }
  
  // font bbcode
  function _font($text)
  {
  		$text = preg_replace("|\[font=([a-z]+)\]|Ui","<span style=\"color: \\1\">",$text);
        $text = preg_replace("§\[/(font|size|color)\]§Ui","</span>",$text);
        $text = preg_replace("§\[size=([1-4][0-9]|[89])\]§Ui","<span style=\"font-size: \\1pt\">",$text); 
        $text = preg_replace("§\[color=(#[0-9A-F]{6}|[a-z]+)\]§Ui","<span style=\"color: \\1\">",$text);
		RETURN $text;
 }
 
 function _list($text) {
 
 //lists
 // Listes à puces
        $text = eregi_replace("\[/list\]","[/list]",$text); // ??? Je me souviens plus de l'utilité de ce truc
        while(eregi("\[list(=([1a])?)?\]",$text,$out)) {
            $deb = strpos($text,$out[0]);
            $txt = substr($text,$deb);
            $fin = strpos($txt,"[/list]");
            $txt = substr($txt,0,$fin+7);
            
            // On détermine le type de liste
            if($out[2] == "1") $in = '<ol type="1">';
            elseif($out[2] == "a") $in = '<ol type="a">';
            elseif(!empty($out[1])) $in = '<ol>';
            else $in = "<ol style=\"list-style: disc\">";

            // Hop on construit la liste
            $lst = $in.substr($txt,strlen($out[0]),-7);
            $lst = preg_replace("/\[\*\](.+?)/U","  <li>\\1</li>",$lst);
            $lst = str_replace("\n","",$lst);
            if(!empty($out[1])) $lst.= "</ol>";
            else $lst.= "</ol>";
           $text = str_replace($txt,$lst,$text);
			
        }
	RETURN $text;
  }
  
  // page bbcode : replace the tag by the page body of the specified url
  // this function is only for the public function parsefile()
  // Warning : this process is slow and unstable with complex pages
  function _page($text) {
    if (preg_match_all("#\[page\](.+?)\[/page\]#si", $text, $match)) {
      for($i = 0; $i <= count($match[0]); $i++) {
	$tag = $match[1][$i];
	$buffer = '';
	if (eregi('^http://.+$', $tag)) {
	  if ($fd = fopen($tag, 'r')) {
	    while (!feof ($fd)) {
	      $buffer .= fgets($fd, 4096);
	    }
	    fclose($fd);
	    $buffer = eregi_replace('^.*<body[^>]*>', '',    $buffer);
	    $buffer = eregi_replace('</body>.*$', '',        $buffer);
	    $buffer = eregi_replace('<html[^>*]>', '',       $buffer);
	    $buffer = eregi_replace('</html>', '',           $buffer);
	    $buffer = eregi_replace('http://', 'h££p://',    $buffer);
	    $buffer = eregi_replace('<img([^>]+)src=["\']*/*([^\£"\'> ]{2,200})["\']*([^>]*)>', '<img\1src="'.$tag.'/\2"\3>',         $buffer);
	    $buffer = eregi_replace('<a([^>]+)href=["\']*/*([^\£"\'> ]{2,200})["\']*([^>]*)>', '<a\1href="'.$tag.'/\2"\3>',           $buffer);
	    $buffer = eregi_replace('<form([^>]+)action=["\']*/*([^\£"\'> ]{2,200})["\']*([^>]*)>', '<form\1action="'.$tag.'/\2"\3>', $buffer);
	    $buffer = eregi_replace('h££p://', 'http://',    $buffer);
	    $buffer = eregi_replace('<head>.*</head>', '',   $buffer);
		$buffer = eregi_replace('<script.*/script>', '', $buffer);
		
		
	  }
	}
	$text = str_replace($match[0][$i], $buffer, $text);
      }
    }
    return ereg_replace('\[page\][^\[]*\[/page\]', '', $text);
  }
  
  
	// this function appeal the "phpmethod" function 
	function _php($text)
	{
		if($this->phpmethod == "string")
		{
		RETURN	$this->stringphp($text);
		}
		elseif($this->phpmethod == "page")
		{
		RETURN $this->pagephp($text);
		}
		else
		{
		RETURN $text;
		}
	}
	
	/**  PHPbbcodes functions **/
	
	// php highlighter bbcode [php] function coucou() { return 'test'; } [/php]
  function stringphp($text) {
    $strip_php_tags = ereg('<\?.+\?>', $text) ? false : true;
    if(preg_match_all("#\[php\](.+?)\[/php\]#si", $text, $match)) {
      for($i = 0; $i <= count($match[0]); $i++) {
	$match2 = str_replace('[php]',  ($strip_php_tags ? '<?' : ''), $match[0][$i]);
	$match2 = str_replace('[/php]', ($strip_php_tags ? '?>' : ''), $match2);
	ob_start();
	highlight_string($match2);
	$match2 = ob_get_contents();
	ob_end_clean();
	$text = str_replace($match[0][$i], $match2, $text);
	$text = $strip_php_tags ? eregi_replace('<font *color="#[0-9A-F]{6}"> *(&lt;\?|\?&gt;) *</font>', '', $text) : $text;
      }
    }
    return $text;
  }
	
	
	

	// function pagephp to show the php code colored in a table with number of line		
	function pagephp($text)
	{
	
		if(preg_match_all("#\[php\](.+?)\[/php\]#si", $text, $match))
		{
		
			for($i = 0; $i < count($match[0]); $i++)
				{
					$match2 = str_replace("[php]", "<?", $match[0][$i]);
					$match2 = str_replace("[/php]", "?>", $match2);
					$match2 = str_replace("&lt;","<", $match2);
					$match2 = str_replace ("&gt;", ">", $match2);
					ob_start();
					highlight_string($match2);
					$code = ob_get_contents();
					ob_end_clean();
					$code = eregi_replace("<br />", "<br>", $code);
					$code = eregi_replace("\r</font>", "</font>\r", $code);
					$code = str_replace("\n","",$code);
					$code = str_replace("<code>", "", $code);
					$code = str_replace("</code>", "", $code);
					
					$code = explode("<br>", $code);

					$newcode = "<table style=\"width: 95%\"><tr><td>";

					for($j = 0; $j < count($code); $j++)
						{
							if($j % 2)
							$class_id = "couleur3";
							else
							$class_id = "couleur4";
							$newcode.= "<span class=\"".$class_id."\" style=\"width: 100%\"onmouseout=\"change_couleur2(this)\" onmouseover=\"change_couleur2(this)\"><span style=\"color: white\"><b>".($j+1)."</b></span>&nbsp;&nbsp;".$code[$j]."</span>";
						} 
					$newcode .= "</td></tr></table>";
					
					$text = str_replace($match[0][$i],$newcode,$text);
					
					}
				}
				RETURN "<div class=\"colourcode\">".$text."\n</div>";
			} 
			
			
			
			 function _classcolourhtml($text) {
	return preg_replace("#\[html\](.*?)\[/html\]#si", "<span class=\"html\">\\1</span>", $text);
	  } 
				
					
}

?>
