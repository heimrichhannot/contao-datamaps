<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2015 Leo Feyer
 *
 * @license LGPL-3.0+
 */


/**
 * Register the namespaces
 */
ClassLoader::addNamespaces(array
(
	'HeimrichHannot',
));


/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
	// Models
	'HeimrichHannot\Datamaps\DatamapsElementsModel' => 'system/modules/datamaps/models/DatamapsElementsModel.php',
	'HeimrichHannot\Datamaps\DataMapsModel'         => 'system/modules/datamaps/models/DataMapsModel.php',

	// Elements
	'HeimrichHannot\Datamaps\ContentDatamap'        => 'system/modules/datamaps/elements/ContentDatamap.php',

	// Classes
	'HeimrichHannot\Datamaps\DataMapConfig'         => 'system/modules/datamaps/classes/DataMapConfig.php',
	'HeimrichHannot\Datamaps\DataMapSelectHelper'   => 'system/modules/datamaps/classes/DataMapSelectHelper.php',
	'HeimrichHannot\Datamaps\DataMap'               => 'system/modules/datamaps/classes/DataMap.php',
));


/**
 * Register the templates
 */
TemplateLoader::addFiles(array
(
	'datamap.defaults'          => 'system/modules/datamaps/templates/js',
	'ce_datamap'                => 'system/modules/datamaps/templates/elements',
	'datamap_de_federal_states' => 'system/modules/datamaps/templates/datamaps/de',
));
