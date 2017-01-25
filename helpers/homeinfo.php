<?php
/************************************************************************
 Product    : Home information (mod_homeinfo)
 Date       : 2012-10-23
 Copyright  : Copyright (C) 2012 Kjeholt Engineering. All rights reserved.
 Contact    : dev@kjeholt.se
 Url        : http://dev.kjeholt.se
 Licence    : Den Kjeholtska licensmodellen
 ---------------------------------------------------------
 File       : helpers/homeinfo.php
 Version    : 0.2.5
 Author     : Bjorn Kjeholt
 *************************************************************************/
 
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
class HomeInfoHelper {
	
	/*
	 *
	*/
	function getCurrentTemperature( &$param, $sensorId) {
		$db =& JFactory::getDBO();

		/*
		 * Get the latest stored data from the selected sensorId.
		 * If the data is more than 10 minutes old, ignore it and 
		 * put -- in the data field instead
		 */
		$queryTemp = "
			SELECT
				format(`#__hi_data_float`.`data`,1) AS `SensorCurrData`,
				`#__hi_data_float`.`time` as `SensorCurrTime`
			FROM
				`#__hi_data_float`
			WHERE
				(`#__hi_data_float`.`sensor_id` = '" . $sensorId . "')
			ORDER BY
				`#__hi_data_float`.`time` DESC
			LIMIT 0,1 ";
		$db->setQuery($queryTemp);
		if (!$db->query()) {
			JError::raiseError(500, $db->getErrorMsg());
		}
	
		if (($rowTemps = $db->loadAssoc()) != null) {
			$currTemp = $rowTemps['SensorCurrData'];
			$currTime = $rowTemps['SensorCurrTime'];
		} else {
			$currTemp = "--";
			$currTime = "--";
		}
	
		return array('temp'=>$currTemp,'time'=>$currTime);
		
	}
	
	/*
	 *
	*/
	function getTemperatures( &$params) {
		
		$xmlInfo = self::readXmlInfoFromDb();
		self::readXmlDataFromDb(&$xmlInfo);
		
		$xmlSensorList = $xmlInfo->xpath('//sensors/sensor[@type="temp"]');
		
		return $xmlSensorList;
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param mixed $xmlInfoRoot
	 */
	private function readXmlInfoFromDb($xmlInfoRoot=false) {
		$db =& JFactory::getDBO();

		if ($xmlInfoRoot == false)
			$xmlInfoRoot = new SimpleXMLElement('<info time ="' . time() . '" dbid="0"/>');

		$xmlInfoPtrArray = array();
		$xmlInfoPtrArray[0] = $xmlInfoRoot;
		
		
		$query = "
			SELECT 
				id AS ElementId,
				parent_id AS ParentElementId,
				name AS ElementName,
				value AS ElementValue,
				type AS ElementType
			FROM
				#__hi_xml
			WHERE
				(state = '1')
			ORDER BY
				id ASC";
		$db->setQuery($query);
		$db->query();
		
		if (($xmlArray = $db->loadAssocList()) != null) {

			foreach ($xmlArray as $xmlRow) {
				if ($xmlRow['ElementType'] == 'child') {
					/*
					 * NodeType = 'child'
					 */
					
//					echo "<p>xmlRow = <br>";
//					print_r($xmlRow);
//					echo "<br>End of ...</p>";
					
					$xmlInfoPtrArray[($xmlRow['ElementId'])] = 
							$xmlInfoPtrArray[($xmlRow['ParentElementId'])]->addChild(
																				$xmlRow['ElementName'], 
																				$xmlRow['ElementValue']);
					$xmlInfoPtrArray[($xmlRow['ElementId'])]->addAttribute(
																'dbid',
																$xmlRow['ElementId']);
						
				} else {
					/*
					 * NodeType = 'attribute'
					 */
//					echo "<p>xmlRow = <br>";
//					print_r($xmlRow);
//					echo "<br>End of ...</p>";
					
					$xmlInfoPtrArray[($xmlRow['ParentElementId'])]->addAttribute(
																		$xmlRow['ElementName'], 
																		$xmlRow['ElementValue']);
				}
			}			
		} else {
			/*
			 * The database #__hi_xml is empty
			 */	

		}

		return $xmlInfoRoot;
	}
	
	private function readXmlDataFromDb(&$xmlInfoStruct) {
		$db =& JFactory::getDBO();
		
		$xmlSensorArray = $xmlInfoStruct->xpath('//sensors/sensor');
		
		foreach ($xmlSensorArray as $xmlSensor) {
			$sensorId = $xmlSensor['id'];
			switch ($xmlSensor['type']) {
				case 'temp':
					$queryCalc = "
							SELECT
								format(min(`#__hi_data_float`.`data`),1) AS `SensorMinData`,
								format(max(`#__hi_data_float`.`data`),1) AS `SensorMaxData`,
								format(avg(`#__hi_data_float`.`data`),1) AS `SensorAvgData`
							FROM
								`#__hi_data_float`
							WHERE
								(`#__hi_data_float`.`sensor_id` = '" . $sensorId . "') AND
								(`#__hi_data_float`.`time` <= '" . time() . "') AND
								(`#__hi_data_float`.`time` >= '" . (time() - (60*60*24)) . "')";
					$db->setQuery($queryCalc);
					if (!$db->query()) {
							JError::raiseError(500, $db->getErrorMsg());
						}
					$sensorInfoRow = $db->loadAssoc();
					$utsTime = time();
					$xmlSensorStatAvg = $xmlSensor->addChild('data',$sensorInfoRow['SensorAvgData']);
					$xmlSensorStatMax = $xmlSensor->addChild('data',$sensorInfoRow['SensorMaxData']);
					$xmlSensorStatMin = $xmlSensor->addChild('data',$sensorInfoRow['SensorMinData']);
					$xmlSensorStatAvg->addAttribute('type','avg24');
					$xmlSensorStatMax->addAttribute('type','max24');
					$xmlSensorStatMin->addAttribute('type','min24');			
					$xmlSensorStatAvg->addAttribute('time',time());
					$xmlSensorStatMax->addAttribute('time',time());
					$xmlSensorStatMin->addAttribute('time',time());
						
					$sensorDataType = 'float';
					$sensorDataFormat = 'format(`data`,1)';

				break;
				
				case 'counter':
					$sensorDataType = 'float';
					$sensorDataFormat = '`data`';
					break;
				
				default:
					$sensorDataType = 'bool';
					$sensorDataFormat = '`data`';
					break;
			}
			
			$query = "
				SELECT
					`time` AS `SensorLatestDataTime`,
					". $sensorDataFormat ." AS `SensorLatestData`
				FROM
					`#__hi_data_" . $sensorDataType . "`
				WHERE
					(`sensor_id`='" . $xmlSensor['id'] . "')
				ORDER BY
					`time` DESC
				LIMIT
					1";
			
			$db->setQuery($query);
			if (!$db->query()) {
				JError::raiseError(500, $db->getErrorMsg());
			}
			$sensorInfoRow = $db->loadAssoc();
			$xmlSensorLatestData = $xmlSensor->addChild('data',$sensorInfoRow['SensorLatestData']);
			$xmlSensorLatestData->addAttribute('type','latest');
			$xmlSensorLatestData->addAttribute('time',$sensorInfoRow['SensorLatestDataTime']);
		}
		
		return true;
	}
	
}