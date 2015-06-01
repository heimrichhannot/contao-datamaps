<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2015 Heimrich & Hannot GmbH
 *
 * @package datamaps
 * @author  Rico Kaltofen <r.kaltofen@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

$dc = &$GLOBALS['TL_DCA']['tl_content'];

/**
 * Palettes
 */
$dc['palettes']['datamap'] =
	'{type_legend},type,headline;{datamap_legend},datamap,datamapBubbles;{template_legend:hide},customTpl;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space;{invisible_legend:hide},invisible,start,stop';

/**
 * Fields
 */
$arrFields = array
(
	'datamap' => array
	(
		'label'      => &$GLOBALS['TL_LANG']['tl_content']['datamap'],
		'exclude'    => true,
		'inputType'  => 'select',
		'foreignKey' => 'tl_datamaps.title',
		'eval'       => array('mandatory' => true, 'fieldType' => 'radio'),
		'sql'        => "int(10) unsigned NOT NULL default '0'",
		'relation'   => array('type' => 'belongsTo', 'load' => 'lazy'),
	)
);

$dc['fields'] = array_merge($dc['fields'], $arrFields);


class tl_content_datamap extends Backend
{

}