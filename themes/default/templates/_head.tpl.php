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
if (!defined('NVRTBL'))
	exit;
		
/* Template html header, nevertbale standard */
/**
 * @param: title: Titre de la page
 */

?>

<title><?php echo $title ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<link rel="stylesheet" href="<?php echo $this->table->style->GetCss() ?>" type="text/css" />
<link rel="alternate" type="application/rss+xml" title="Nevertable rss feed" href="http://www.nevercorner.net/table/rss.php" />
<meta name="Description" content="Neverball Hall of Fame - Contest between hardcore Neverballers" />
<meta name="Keywords" content="neverball, contest, nevercorner, game, ball, super monkey ball" />

<script type="text/javascript" src="<?php echo ROOT_PATH ?>/includes/js/jsutil.js"></script>
