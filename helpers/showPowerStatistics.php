<?php
/************************************************************************
 Product    : Home information
 Date       : 2012-10-10
 Copyright  : Copyright (C) 2012 Kjeholt Engineering. All rights reserved.
 Contact    : dev@kjeholt.se
 Url        : http://dev.kjeholt.se
 Licence    : Den Kjeholtska licensmodellen
 ---------------------------------------------------------
 File       : helpers/showPowerStatistics.php
 Version    : 0.2.2
 Author     : Bjorn Kjeholt
 *************************************************************************/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
class HomeInfoHelperShowPowerStatistics {

	private function getPowerInfo () {
		$db =& JFactory::getDBO();
		
		$sensorIdList = array('15','14');
		
		$dataValueArray = array();
		$prevData = array();
		foreach ($sensorIdList as $sensorId) {
			$dataValueArray[($sensorId)] = array();
			$prevData[($sensorId)] = null;
		}
		
		for ($dayNumber = -30; $dayNumber <= 0; $dayNumber++) {
			$prevMeasureTime = time() + ($dayNumber*24*60*60);
			$query = "
				SELECT 
					`#__hi_data_float`.`data` AS `SensorData`,
					`#__hi_data_float`.`sensor_id` AS `UnitId`
				FROM
					`#__hi_data_float`
				WHERE 
					((";
			$queryWhere = null;
			foreach ($sensorIdList as $sensorId) {
				if ($queryWhere != '') 
					$queryWhere .= 'OR';
				$queryWhere .= "(`#__hi_data_float`.`sensor_id` = '". $sensorId ."')";
			}
			$query .= $queryWhere . ") AND (`#__hi_data_float`.`time` < '" . $prevMeasureTime . "') AND
					 (`#__hi_data_float`.`time` > '0'))
				ORDER BY `#__hi_data_float`.`time`DESC
				LIMIT " . count($sensorIdList);
//echo "<p>query = $query</p>";
			$db->setQuery($query);
			if (!$db->query()) {
				JError::raiseError(500, $db->getErrorMsg());
			}

			/*
			 * 
			 */
			foreach ($sensorIdList as $sensorId) {
				$dataValueArray[($sensorId)][($dayNumber)] = floatval(0);
			}
			if (($resultArray = $db->loadAssocList('UnitId')) != null) {
//				echo "<p>daynumber = $dayNumber<br>";
				print_r($resultArray);
//				echo "</p>";
				foreach ($resultArray as $result) {
					$sensorId = $result['UnitId'];
					
					if ($prevData[($sensorId)] != null) {
						$dataValueArray[($sensorId)][($dayNumber)] = 
									floatval($result['SensorData']) - $prevData[($sensorId)];
						$prevData[($sensorId)] = 
									floatval($result['SensorData']);
					} else {
						$dataValueArray[($sensorId)][($dayNumber)] = floatval(0);
						$prevData[($sensorId)] = floatval($result['SensorData']);
					}
				}	
			} else {
				foreach ($sensorIdList as $sensorId) {
					$dataValueArray[($sensorId)][($dayNumber)] = floatval(0);
					$prevData[($sensorId)] = floatval(0);				
				}
			}
		}
		if (false) {
			echo "
				<comment>
					<h4>HomeInfoHelperShowPowerStatistics::getPowerInfo</h4>
					<p>ValueArray = ";
			print_r($dataValueArray);
			echo "
					</p>
				<comment>";
		}
		return $dataValueArray;
	}
	
	private function createDiagram($width=200, $height=200) {
		$xMin = 30;
		$xMax = $width - 20;
		$yMin = 20;
		$yMax = $height -30;
		
		$xMinValue = -1*60*60*24*30;
		$xMaxValue = 0;
		
		$xKoef = ($xMin - $xMax) / ($xMinValue - $xMaxValue);
		$xOffset = $xMax;
		
		$yMinValue = 0.0;
		$yMaxValue = 5.0;
		
		$yKoef = ($yMax - $yMin) / ($yMinValue - $yMaxValue);
		$yOffset = $yMax;
		
		$xmlInitialStr = '
			<info time="' . time() .'">
				<graph width="' . $width . '" height="' . $height . '">
					<y min="' . $yMin .'" max="'.$yMax . '" 
					   valuemin="'. $yMinValue .'" valuemax="'. $yMaxValue .'" 
					   koef="'. $yKoef .'" offset="'. $yOffset .'"/>
					<x min="' . $xMin .'" max="'.$xMax . '" 
					   valuemin="'. $xMinValue .'" valuemax="'. $xMaxValue .'" 
					   koef="'. $xKoef .'" offset="'. $xOffset .'"/>
				</graph>
				<svg xmlns="http://www.w3.org/2000/svg" width="' . $width . '" height="' . $height . '" version="1.1">
					<g id="basic">
						<text x="1" y="' . ($yMin-10) . '" style="font-size:8px;font-style:italic;">kWh/h</text>
						<line x1="' . $xMin . '" x2="' . $xMin . '" y1="' . $yMin . '" y2="' . $yMax . '" style="stroke:#000000;stroke-width:1"/>
						<line x1="' . $xMin . '" x2="'. $xMax .'" y1="' . $yMax . '" y2="' . $yMax . '" style="stroke:#000000;stroke-width:1"/>
					</g>
					<g id="y-scale">
						<line x1="' . ($xMin-5) . '" x2="' . $xMin . '" y1="' . ($yMin) . '" y2="' . ($yMin) . '" style="stroke:#000000;stroke-width:1"/>
						<line x1="' . ($xMin-5) . '" x2="' . $xMin . '" y1="' . $yMax . '" y2="' . $yMax . '" style="stroke:#000000;stroke-width:1"/>
						<text x="5" y="' . ($yMin) . '" style="font-size:8px;font-style:italic;text-align:end">5.0</text>
						<text x="5" y="' . $yMax . '" style="font-size:8px;font-style:italic;text-align:end">0.0</text>
					</g>
					<g id="x-scale">
						<line x1="' . ($xMax-10) . '" x2="' . ($xMax-10) . '" y1="' . ($yMax) . '" y2="' . ($yMax+5) . '" style="stroke:#FF0000;stroke-width:1"/>
						<line x1="' . ($xMin+10) . '" x2="' . ($xMin+10) . '" y1="' . ($yMax) . '" y2="' . ($yMax+5) . '" style="stroke:#FF0000;stroke-width:1"/>
						<text x="' . ($xMin+0) . '" y="' . ($yMax+5) . '" style="font-size:8px;font-style:italic">One month ago</text>
						<text x="' . ($xMax-20) . '" y="' . ($yMax+5) . '" style="font-size:8px;font-style:italic">Today</text>
					</g>
				</svg>
			</info>';
		
//		echo "<p>xmlInitialStr = $xmlInitialStr</p>";
		
		$xmlGraph = new SimpleXMLElement($xmlInitialStr);
		$xmlSvgArray = $xmlGraph->xpath('//svg');
		
		foreach ($xmlGraph->svg as $xmlSvg) {
			$xmlGroup = $xmlSvg->addChild('g');
			$xmlGroup->addAttribute('id','y-scale-grid');
			for ($i = 1; $i <= 5; $i = $i + 1) {
				$yGrid = floatval($yOffset) + (floatval($yKoef)*floatval($i));
				
				$xmlGridLine = $xmlGroup->addChild('line');
				$xmlGridLine->addAttribute('x1',$xMin);
				$xmlGridLine->addAttribute('x2',$xMax);
				$xmlGridLine->addAttribute('y1',$yGrid);
				$xmlGridLine->addAttribute('y2',$yGrid);
				$xmlGridLine->addAttribute('style','stroke:grey;stroke-width:0.2');				
			}
//			echo "<p>xmlGroup = " .htmlspecialchars($xmlGroup->asXML()). "</p>";
		}
		return $xmlGraph;
	}
	
	private function drawBox(&$xmlSvg, $x, $y, $width) {
		$xmlXInfoArray = $xmlSvg->xpath('//graph/x');
		foreach ($xmlXInfoArray as $xmlXInfo) {
			$svgX = floatval($xmlXInfo['offset']) + (floatval($xmlXInfo['koef'])*floatval($x));
		}	
		$xmlYInfoArray = $xmlSvg->xpath('//graph/y');
		foreach ($xmlYInfoArray as $xmlYInfo) {
			$svgYMin = floatval($xmlYInfo['offset']) + (floatval($xmlYInfo['koef'])*floatval($y));
			$svgYMax = floatval($xmlYInfo['max']);
		}	
//		echo "<p>x_info = $svgX : $x : " . $xmlXInfo['offset'] . " : " .$xmlXInfo['koef']."<p>";
//		echo "<p>y_info = ($svgYMin : $svgYMax) : $y : " . $xmlYInfo['offset'] . " : " .$xmlYInfo['koef']."<p>";
		
		$xmlRect = $xmlSvg->addChild('rect');
		$xmlRect->addAttribute('x',($svgX-($width/2)));
		$xmlRect->addAttribute('y',$svgYMin);
		$xmlRect->addAttribute('width',$width);
		$xmlRect->addAttribute('height',($svgYMax - $svgYMin));
		$xmlRect->addAttribute('style','fill:white;stroke:blue;stroke-width:1;fill-opacity:0.2');
		
		
	}
	
	public function getSvgGraph() {
		
		$xmlSvgInfo =& self::createDiagram(250, 200);

		$dataValueArray = array();
		$dataValueArray = self::getPowerInfo();
		
		foreach ($xmlSvgInfo->svg as $xmlSvg) {
			foreach ($dataValueArray as $sensorId => $sensorDataArray) {
				foreach ($sensorDataArray as $dayNumber => $dataValue) {
					self::drawBox(&$xmlSvg, $dayNumber*24*60*60, ($dataValue/24), 4);
				}
			}
		}
		
//		echo "<p>--------------------<br>xmlSvgInfo = " . htmlspecialchars($xmlSvgInfo->asXML()) . "</p>";
		
		return $xmlSvgInfo;
	}
}