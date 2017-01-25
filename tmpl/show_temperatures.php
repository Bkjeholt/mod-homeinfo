<?php
/************************************************************************
 Product    : Home information (mod_homeinfo)
 Date       : 2012-10-23
 Copyright  : Copyright (C) 2012 Kjeholt Engineering. All rights reserved.
 Contact    : dev@kjeholt.se
 Url        : http://dev.kjeholt.se
 Licence    : Den Kjeholtska licensmodellen
 ---------------------------------------------------------
 File       : tmpl/show_temperatures.php
 Version    : 0.2.5
 Author     : Bjorn Kjeholt
 *************************************************************************/

// no direct access

defined('_JEXEC') or die; 

echo '<div class="moduletable"' . $params->get( 'moduleclass_sfx' ) . '>';
//echo '  <ul>';
echo '    <table width="100%">';
echo '      <tr>';
echo '        <th rowspan=2  width="40%">Sensor name</th>';
echo '        <th colspan=4>Temperature info</th>';
echo '      </tr>';
echo '      <tr>';
echo '        <th width="15%">Curr</th>';
echo '        <th width="15%">Min</th>';
echo '        <th width="15%">Max</th>';
echo '        <th width="15%">Avg</th>';
echo '      </tr>';

foreach ($xmlSensorList as $xmlSensor) {

	$xmlCurrDataList = $xmlSensor->xpath('//data[@type="latest"]');
	$xmlMinDataList = $xmlSensor->xpath('//data[@type="min24"]');
	$xmlMaxDataList = $xmlSensor->xpath('//data[@type="max24"]');
	$xmlAvgDataList = $xmlSensor->xpath('//data[@type="avg24"]');
	
	echo '      <tr>';
	echo '        <td>' . $xmlSensor['name'] . '</td>';

	foreach ($xmlSensor->data as $xmlData) {
		switch ($xmlData['type']) {
			case 'latest':
				$dataLatest = $xmlData;
			break;
			
			case 'avg24':
				$dataAvg24 = $xmlData;
			break;
			
			case 'max24':
				$dataMax24 = $xmlData;
			break;
			
			case 'min24':
				$dataMin24 = $xmlData;
			break;
			
			default:
				;
			break;
		};
	}
	echo '        <td>' . $dataLatest . '</td>';
	echo '        <td>' . $dataAvg24 . '</td>';
	echo '        <td>' . $dataMax24 . '</td>';
	echo '        <td>' . $dataMin24 . '</td>';

/*	echo '        <td>' . $xmlCurrDataList[0] . '</td>';
	echo '        <td>' . $xmlAvgDataList[0] . '</td>';
	echo '        <td>' . $xmlMaxDataList[0] . '</td>';
	echo '        <td>' . $xmlMinDataList[0] . '</td>';
*/
	echo '      </tr>';
	
}

echo '    </table>';
//echo '  </ul>';
echo '</div>';
?>
