<?php

/************************************************************************
 Product    : Home information
 Date       : 2012-10-17
 Copyright  : Copyright (C) 2012 Kjeholt Engineering. All rights reserved.
 Contact    : dev@kjeholt.se
 Url        : http://dev.kjeholt.se
 Licence    : Den Kjeholtska licensmodellen
 ------------------------------------------------------------------------
 File       : helpers/showTempGraph.php
 Version    : 0.2.3
 Author     : Bjorn Kjeholt
 *************************************************************************/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

define( 'DS', DIRECTORY_SEPARATOR );
require ( JPATH_BASE.DS.'administrator'.DS.
						'components'.DS.
						'com_homeinfo'.DS.
						'helpers'.DS.
						'svggraph.php' );

class HomeInfoHelperShowTempGraph {
	
	public function getGraph($sensorIdArray,$timeFrom,$timeTo) {
		$db =& JFactory::getDBO();
		$yScaleMax = -1000;
		$yScaleMin = 1000;
		/*
		 * Get data 
		 */
		$xmlCurves = new SimpleXMLElement('<curves time="' . time() . '"></curves>');
		foreach ($sensorIdArray as $sensorId) {
			$xmlCurve = $xmlCurves->addChild('curve');
			$xmlCurve->addAttribute('sensorid', $sensorId);
			
			$query = "
				SELECT		`time` AS `time`,
							`data` AS`data`
				FROM		`#__hi_data_float`
				WHERE		((`unit_id` = '" . $sensorId . "') AND
							 (`time` <= '" . $timeTo . "') AND
							 (`time` >= '" . $timeFrom . "'))
							 ORDER BY	`time` ASC ";
			$db->setQuery($query);
			$db->query();
	
			echo "<p>query = $query</p>";
			
			if (($sensorDataList = $db->loadAssocList()) != null) {
				foreach ($sensorDataList as $sensorData) {
					$xmlData = $xmlCurve->addChild('data',$sensorData['data']);
					$xmlData->addAttribute('x', $sensorData['time']);
					
					$yScaleMax = max(array(floatval($sensorData['data']), $yScaleMax));
					$yScaleMin = min(array(floatval($sensorData['data']), $yScaleMin));	
//					echo "<p>yScale = ($yScaleMin -- $yScaleMax)</p>";
				}
			}
		}
		
		/*
		 * Prepare graph
		 */
		$xmlGraph = homeInfoHelperSvgGraph::prepareGraph(
									$width=250, 
									$height=200, 
									$xLow=$timeFrom, 
									$xHigh=$timeTo, 
									$yLow = $yScaleMin, 
									$yHigh = $yScaleMax, 
									$yGrid=5);
		/*
		 * Draw curves
		 */
//		echo "<p>xmlCurves = " .htmlspecialchars($xmlCurves->asXML())."</p>";
		
		homeInfoHelperSvgGraph::drawCurve(&$xmlGraph,$xmlCurves);
//		echo "<p>xmlGraph = ".htmlspecialchars($xmlGraph->asXML())."</p>";
		
		return $xmlGraph;
	}
}