<?php
# ***** BEGIN LICENSE BLOCK *****
# This file is part of Nevertable .
# Copyright (c) 2004 Francois Guillet and contributors. All rights
# reserved.
#
# Nevertable is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
-# the Free Software Foundation; either version 2 of the License, or
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

define('ROOT_PATH', "./");
define('NVRTBL', 1);
define('NVRTBL_PATH', "http://www.nevercorner.net/table/");
define('LEVEL', 30);
include_once ROOT_PATH ."config.inc.php";
include_once ROOT_PATH ."includes/common.php";
include_once ROOT_PATH ."includes/classes.php";

$table = new Nvrtbl();


$ts = $table->GetEarlierDate();
$items = $seq = '';

$Records = $table->GetLastRecords(LEVEL);

for ($i=0; $i<LEVEL; $i++)
{
    $URL = NVRTBL_PATH . "index.php?link=".$Records[$i]['id'];
    $seq .=  "<rdf:li rdf:resource=\"".$URL."\" />\n";
   
    $content = "That's a ".$Records[$i]['type']." on ".$Records[$i]['level']." ~ time: ".$Records[$i]['time']." ~ coins: ".$Records[$i]['coins'];
    $itemappend = 
        "<item rdf:about=\"".$URL."\">\n".
		"  <title>".$Records[$i]['title']."</title>\n".
		"  <link>".$URL."</link>\n".
		"  <dc:date>".$Records[$i]['date']."</dc:date>\n".
		"  <dc:creator>".$Records[$i]['pseudo']."</dc:creator>\n".
		"  <dc:subject>".$Records[$i]['title']."</dc:subject>\n".
		"  <content:encoded><![CDATA[".$content."]]></content:encoded>\n".
	    "</item>\n";

    $items .= $itemappend;
}

$table->Close();

header('Content-Type: text/xml; charset=iso-8859-1');
echo '<?xml version="1.0" encoding="iso-8859-1" ?>'."\n";
?>

<rdf:RDF
  xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
  xmlns:dc="http://purl.org/dc/elements/1.1/"
  xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
  xmlns:admin="http://webns.net/mvcb/"
  xmlns:content="http://purl.org/rss/1.0/modules/content/"
  xmlns="http://purl.org/rss/1.0/">

<channel rdf:about="http://www.nevercorner.net/table/">
  <title>Nevertable - Neverball Hall of Fame RSS feed</title>
  <description>List of all neverball records</description>
  <link>http://www.nevercorner.net/table/</link>
  <dc:language>en</dc:language>
  <dc:creator></dc:creator>
  <dc:rights></dc:rights>
  <dc:date><?php echo getIsoDate($ts); ?></dc:date>
  <admin:generatorAgent rdf:resource="http://www.nevercorner.net/table/" />
  
  <sy:updatePeriod>daily</sy:updatePeriod>
  <sy:updateFrequency>1</sy:updateFrequency>
  <sy:updateBase><?php echo getIsoDate($ts); ?></sy:updateBase>
  
  <items>
  <rdf:Seq>
  <?php echo $seq; ?>
  </rdf:Seq>
  </items>
</channel>

<?php echo $items; ?>

</rdf:RDF>
