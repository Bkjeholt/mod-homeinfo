<?php
/************************************************************************
 Product    : Home information
 Date       : 2012-10-10
 Copyright  : Copyright (C) 2012 Kjeholt Engineering. All rights reserved.
 Contact    : http://dev.kjeholt.se
 Licence    : Den Kjeholtska licensmodellen
 ---------------------------------------------------------
 File       : tmpl/show_powerStatistics.php
 Version    : 0.2.1
 Author     : Bjšrn Kjeholt
 *************************************************************************/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
echo "<div>";
foreach ($xmlSvgInfo->svg as $xmlSvg) {
	echo $xmlSvg->asXML();
}
echo "</div>";

