<?php
/**
 * Contao Open Source CMS
 * 
 * Copyright (c) 2015 Heimrich & Hannot GmbH
 * @package datamaps
 * @author Rico Kaltofen <r.kaltofen@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

/**
 * Constants
 */
define(DATAMAP_ELEMENT_BUBBLE, 'BUBBLE');
define(DATAMAP_ELEMENT_STATE, 'STATE');

/**
 * Content elements
 */
$GLOBALS['TL_CTE']['includes']['datamap'] = 'HeimrichHannot\Datamaps\ContentDatamap';
$GLOBALS['TL_JAVASCRIPT']['d3.js'] = 'system/modules/datamaps/assets/vendor/d3/d3.min.js';
$GLOBALS['TL_JAVASCRIPT']['topojson'] = 'system/modules/datamaps/assets/vendor/topojson/topojson.js';
$GLOBALS['TL_JAVASCRIPT']['datamaps.all'] = 'system/modules/datamaps/assets/vendor/datamaps/dist/datamaps.all.min.js';
$GLOBALS['TL_USER_CSS']['datamaps']     = 'system/modules/datamaps/assets/css/datamaps.less|screen|static|1.0.0';


/**
 * Back end modules
 */
array_insert($GLOBALS['BE_MOD']['content'], count($GLOBALS['BE_MOD']['content']), array
  (
	'datamaps' => array
	(
		'tables'      => array('tl_datamaps', 'tl_datamaps_elements'),
		'icon'   	 => 'system/modules/datamaps/assets/icon.png',
	)
  )
);

/**
 * Register models
 */

$GLOBALS['TL_MODELS']['tl_datamaps']     			= '\\HeimrichHannot\\Datamaps\\DatamapsModel';
$GLOBALS['TL_MODELS']['tl_datamaps_elements']     = '\\HeimrichHannot\\Datamaps\\DatamapsElementsModel';

/**
 * Datamaps config
 */
$GLOBALS['TL_DATAMAPS'] = array_merge
(
	is_array($GLOBALS['TL_DATAMAPS']) ? $GLOBALS['TL_DATAMAPS'] : array(),
	array
	(
		'de_federal_states' => 'datamap_de_federal_states'
	)
);