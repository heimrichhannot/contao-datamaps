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
define('DATAMAP_ELEMENT_BUBBLE', 'BUBBLE');
define('DATAMAP_ELEMENT_STATE', 'STATE');

/**
 * Content elements
 */
$GLOBALS['TL_CTE']['includes']['datamap'] = 'HeimrichHannot\Datamaps\ContentDatamap';

$GLOBALS['TL_USER_CSS']['datamaps'] = 'system/modules/datamaps/assets/css/datamaps.less|screen|static|1.0.0';


/**
 * Back end modules
 */
array_insert($GLOBALS['BE_MOD']['content'], count($GLOBALS['BE_MOD']['content']), [
        'datamaps' => [
            'tables' => ['tl_datamaps', 'tl_datamaps_elements'],
            'icon'   => 'system/modules/datamaps/assets/img/icon.png',
        ]
    ]
);

/**
 * Register models
 */

$GLOBALS['TL_MODELS']['tl_datamaps']          = 'HeimrichHannot\Datamaps\DataMapsModel';
$GLOBALS['TL_MODELS']['tl_datamaps_elements'] = 'HeimrichHannot\Datamaps\DataMapsElementsModel';

/**
 * Datamaps config
 */


$GLOBALS['TL_DATAMAPS'] = array_merge
(
    $GLOBALS['TL_DATAMAPS'] ?? [],
    [
        'de_federal_states' => 'datamap_de_federal_states',
        'de_nrw' => 'datamap_de_nrw',
        'world_states' => 'datamap_world_states'
    ]
);
