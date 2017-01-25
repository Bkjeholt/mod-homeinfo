<?php
/************************************************************************
 Product    : Home information (mod_homeinfo)
 Date       : 2013-04-26
 Copyright  : Copyright (C) 2012-2013 Kjeholt Engineering. All rights reserved.
 Contact    : dev@kjeholt.se
 Url        : http://dev.kjeholt.se
 Licence    : Den Kjeholtska licensmodellen
 ---------------------------------------------------------
 File       : tmpl/show_currtemp.php
 Version    : 0.3.4
 Author     : Bjorn Kjeholt
 *************************************************************************/

// no direct access

defined('_JEXEC') or die;

$document = JFactory::getDocument();
$document->addScriptDeclaration('
			window.onload = function() {
				updateCurrValue("'.$sensorId.'", 
								"mod_homeinfo_sensor_data", 
								"mod_homeinfo_sensor_data_time",
								"http://dev.kjeholt.se/index.php?format=raw&option=com_homeinfo&task=get_temperature");
			};
		');

?>
<div id="mod_homeinfo_show_sensor_info">
	Current temp is: <span id="mod_homeinfo_sensor_data">---</span>
</div>
<div id="mod_homeinfo_sensor_data_time">
</div>

<?php 

