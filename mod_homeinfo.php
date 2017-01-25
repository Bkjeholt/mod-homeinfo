<?php
/************************************************************************
 Product    : Home information (mod_homeinfo)
 Date       : 2012-10-23
 Copyright  : Copyright (C) 2012 Kjeholt Engineering. All rights reserved.
 Contact    : dev@kjeholt.se
 Url        : http://dev.kjeholt.se
 Licence    : Den Kjeholtska licensmodellen
 ---------------------------------------------------------
 File       : mod_homeinfo.php
 Version    : 0.2.5
 Author     : Bjorn Kjeholt
 *************************************************************************/
 
// no direct access

defined('_JEXEC') or die;

// Include the syndicate functions only once
define( 'DS', DIRECTORY_SEPARATOR );
define('JPATH_COMPONENT_HELPERS', JPATH_BASE.DS.'administrator'.DS.
							  	  'components'.DS.
								  'com_homeinfo'.DS.
								  'helpers');

// Define the search path to the component declaration and class files

define( 'JPATH_ADMINISTRATOR_COMPONENT_PATH', JPATH_ADMINISTRATOR . DS .
		'components' . DS .
		'com_homeinfo' );

define( 'JPATH_ADMINISTRATOR_INCLUDES_PATH', JPATH_ADMINISTRATOR_COMPONENT_PATH . DS .
		'includes' );

define( 'JPATH_ADMINISTRATOR_HELPER_PATH', JPATH_ADMINISTRATOR_COMPONENT_PATH . DS .
		'helpers' );

define( 'JPATH_ADMINISTRATOR_CONSTANT_PATH', JPATH_ADMINISTRATOR_COMPONENT_PATH . DS .
		'includes' );

define( 'JPATH_MODULE_SCRIPT_PATH', dirname(__FILE__) . DS .
		'scripts' . DS );

// Included component helper classes
require_once JPATH_ADMINISTRATOR_HELPER_PATH . DS . 'services.php';
require_once JPATH_ADMINISTRATOR_HELPER_PATH . DS . 'sensordata.php';
require_once JPATH_ADMINISTRATOR_HELPER_PATH . DS . 'xmlconfig.php';

require_once dirname(__FILE__).'/helpers/homeinfo.php';
require_once dirname(__FILE__).'/helpers/showPowerStatistics.php';
// require_once dirname(__FILE__).'/helpers/showTempGraph.php';

require_once ( JPATH_BASE.DS.'administrator'.DS.
		'components'.DS.
		'com_homeinfo'.DS.
		'helpers'.DS.
		'svggraph.php' );

/*
 * Get the JavaScript functions useful for this module
 */
$document = JFactory::getDocument();
$document->addScript('/media/com_homeinfo/js/showCurrInfo.js');


//	// Get the user data
//	
//	$list   = modTemperatureHelper::getData($params);

/*
 * Start using the showTemperatures layout
 */


//	// Get the layout
//	require JModuleHelper::getLayoutPath('mod_hometemp', $params->get('layout', 'default'));

switch ($params->get('homeinfo_view')) {
	case 0:
		/*
		 * Get an array with information regarding all active sensors and 
		 * corresponding temperature figures.
		 */
		$sensorData = array();

		$xmlSensorList = homeInfoHelper::getTemperatures($param);

		require JModuleHelper::getLayoutPath('mod_homeinfo', $params->get('layout', 'show_temperatures'));
		;
	break;
	
	case 1:
		;
	break;
	
	case 2:
		;
	break;
	
	case 3:
		;
	break;
	
	case 4: /* MOD_HOMEINFO_PARAM_CURR_SENSOR_VALUE */
		$sensorId = $params->get('homeinfo_outdoor_sensor_id');
		$currInfoArray = array();
		$currInfoArray = homeInfoHelper::getCurrentTemperature($param,$sensorId);
		$currTemp = $currInfoArray['temp'];
		$currTime = $currInfoArray['time'];
		require JModuleHelper::getLayoutPath('mod_homeinfo', $params->get('layout', 'show_currtemp'));
		
	break;
	
	case 5: /* Power dissipation information */
		$sensorIdPowerTotal = $params->get('homeinfo_power_total_sensor_id');
		$sensorIdPowerHeat  = $params->get('homeinfo_power_heat_sensor_id');
		
		$xmlSvgInfo = HomeInfoHelperShowPowerStatistics::getSvgGraph();
		require JModuleHelper::getLayoutPath('mod_homeinfo', $params->get('layout', 'show_powerStatistics'));
	break;
	
	case 6: /* Current temperature graphical presentation */
		$sensorId = $params->get('homeinfo_outdoor_sensor_id');
//		$xmlSvgInfo = HomeInfoHelperShowTempGraph::getGraph(array($sensorId), (time() - 60*60*24*7), time());
		$xmlSvgInfo = homeInfoHelperSvgGraph::getGraph(array($sensorId), (time() - 60*60*24*7), time());
		
		require JModuleHelper::getLayoutPath('mod_homeinfo', $params->get('layout', 'showTempGraph'));
	break;
		
	case 7: /* */
		$sensorIdPowerTotal = $params->get('homeinfo_power_total');
		$sensorIdPowerHeat  = $params->get('homeinfo_power_heat');

		require JModuleHelper::getLayoutPath('mod_homeinfo', $params->get('layout', 'showPowerStatistics'));
		
	break;
	
	default:
		;
	break;
}