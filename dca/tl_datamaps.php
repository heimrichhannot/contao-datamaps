<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2015 Heimrich & Hannot GmbH
 *
 * @package datamap
 * @author  Rico Kaltofen <r.kaltofen@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

$GLOBALS['TL_DCA']['tl_datamaps'] = array
(
	// Config
	'config'   => array
	(
		'dataContainer'    => 'Table',
		'ctable'           => array('tl_datamaps_elements'),
		'switchToEdit'     => true,
		'enableVersioning' => true,
		//		'onload_callback' => array
		//		(
		//			array('tl_datamaps', 'checkPermission')
		//		),
		'sql'              => array
		(
			'keys' => array
			(
				'id' => 'primary'
			)
		)
	),
	// List
	'list'     => array
	(
		'sorting'           => array
		(
			'mode'        => 1,
			'fields'      => array('title'),
			'flag'        => 1,
			'panelLayout' => 'filter;search,limit'
		),
		'label'             => array
		(
			'fields' => array('title'),
			'format' => '%s',
		),
		'global_operations' => array
		(
			'all' => array
			(
				'label'      => &$GLOBALS['TL_LANG']['MSC']['all'],
				'href'       => 'act=select',
				'class'      => 'header_edit_all',
				'attributes' => 'onclick="Backend.getScrollOffset();" accesskey="e"'
			)
		),
		'operations'        => array
		(
			'edit'       => array
			(
				'label'      => &$GLOBALS['TL_LANG']['tl_datamaps']['edit'],
				'href'       => 'table=tl_datamaps_elements',
				'icon'       => 'edit.gif',
				'attributes' => 'class="contextmenu"'
			),
			'editheader' => array
			(
				'label'           => &$GLOBALS['TL_LANG']['tl_datamaps']['editheader'],
				'href'            => 'act=edit',
				'icon'            => 'header.gif',
				'button_callback' => array('tl_datamaps', 'editHeader'),
				'attributes'      => 'class="edit-header"'
			),
			'copy'       => array
			(
				'label' => &$GLOBALS['TL_LANG']['tl_datamaps']['copy'],
				'href'  => 'act=copy',
				'icon'  => 'copy.gif'
			),
			'delete'     => array
			(
				'label'      => &$GLOBALS['TL_LANG']['tl_datamaps']['delete'],
				'href'       => 'act=delete',
				'icon'       => 'delete.gif',
				'attributes' => 'onclick="if (!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm']
								. '\')) return false; Backend.getScrollOffset();"'
			),
			'show'       => array
			(
				'label' => &$GLOBALS['TL_LANG']['tl_datamaps']['show'],
				'href'  => 'act=show',
				'icon'  => 'show.gif'
			)
		)
	),
	// Palettes
	'palettes' => array
	(
		'__selector__' => array('type'),
		'default'      => '{title_legend},type,title;{geography_legend},popupOnHover,highlightOnHover,highlightFillColor,highlightBorderColor,highlightBorderWidth;{fills_legend},defaultFill,fills'
	),
	// Fields
	'fields'   => array
	(
		'id'     => array
		(
			'sql' => "int(10) unsigned NOT NULL auto_increment"
		),
		'tstamp' => array
		(
			'sql' => "int(10) unsigned NOT NULL default '0'"
		),
		'rewrite' => array
		(
			'sql' => "char(1) NOT NULL default ''"
		),
		'title'  => array
		(
			'label'     => &$GLOBALS['TL_LANG']['tl_datamaps']['title'],
			'exclude'   => true,
			'search'    => true,
			'inputType' => 'text',
			'eval'      => array('mandatory' => true, 'maxlength' => 255, 'tl_class' => 'clr'),
			'sql'       => "varchar(255) NOT NULL default ''"
		),
		'type'   => array
		(
			'label'     => &$GLOBALS['TL_LANG']['tl_datamaps']['type'],
			'exclude'   => true,
			'inputType' => 'select',
			'options'   => is_array($GLOBALS['TL_DATAMAPS']) ? array_keys($GLOBALS['TL_DATAMAPS']) : array(),
			'reference' => &$GLOBALS['TL_LANG']['tl_datamaps']['references'],
			'eval'      => array('tl_class' => 'w50', 'mandatory' => true),
			'sql'       => "varchar(128) NOT NULL default ''"
		),
		'highlightOnHover' => array
		(
			'label'     => &$GLOBALS['TL_LANG']['tl_datamaps_elements']['highlightOnHover'],
			'exclude'   => true,
			'inputType' => 'checkbox',
			'default'	=> true,
			'eval'      => array('tl_class' => 'w50'),
			'sql'       => "char(1) NOT NULL default ''"
		),
		'popupOnHover'  => array
		(
			'label'     => &$GLOBALS['TL_LANG']['tl_datamaps_elements']['popupOnHover'],
			'exclude'   => true,
			'inputType' => 'checkbox',
			'default'	=> true,
			'eval'      => array('tl_class' => 'w50'),
			'sql'       => "char(1) NOT NULL default ''"
		),
		'defaultFill'  => array
		(
			'label'     => &$GLOBALS['TL_LANG']['tl_datamaps']['defaultFill'],
			'exclude'   => true,
			'default'	=> 'rgb(194,197,200)',
			'inputType' => 'text',
			'eval'      => array('mandatory' => true, 'maxlength' => 32, 'tl_class' => 'clr', 'decodeEntities' => true),
			'sql'       => "varchar(32) NOT NULL default ''"
		),
		'highlightFillColor'  => array
		(
			'label'     => &$GLOBALS['TL_LANG']['tl_datamaps']['highlightFillColor'],
			'exclude'   => true,
			'default'	=> 'rgb(252,141,89)',
			'inputType' => 'text',
			'eval'      => array('mandatory' => true, 'maxlength' => 32, 'tl_class' => 'clr', 'decodeEntities' => true),
			'sql'       => "varchar(32) NOT NULL default ''"
		),
		'highlightBorderColor'  => array
		(
			'label'     => &$GLOBALS['TL_LANG']['tl_datamaps']['highlightBorderColor'],
			'exclude'   => true,
			'default'	=> 'rgba(250, 15, 160, 0.2)',
			'inputType' => 'text',
			'eval'      => array('mandatory' => true, 'maxlength' => 32, 'tl_class' => 'clr', 'decodeEntities' => true),
			'sql'       => "varchar(32) NOT NULL default ''"
		),
		'highlightBorderWidth'  => array
		(
			'label'     => &$GLOBALS['TL_LANG']['tl_datamaps']['highlightBorderWidth'],
			'exclude'   => true,
			'default'	=> '2',
			'inputType' => 'text',
			'eval'      => array('mandatory' => true, 'maxlength' => 10, 'tl_class' => 'clr', 'rgxp' => 'digit'),
			'sql'       => "int(10) unsigned NOT NULL default '0'"
		),
		'fills'  => array
		(
			'label'     => &$GLOBALS['TL_LANG']['tl_datamaps']['fills'],
			'inputType' => 'multiColumnWizard',
			'exclude'   => true,
			'eval'      => array
			(
				'columnFields' => array
				(
					'key'   => array
					(
						'label'     => &$GLOBALS['TL_LANG']['tl_datamaps']['fills']['key'],
						'exclude'   => true,
						'inputType' => 'text',
						'eval'      => array('style' => 'width:280px', 'chosen' => true, 'includeBlankOption' => true, 'maxlength' => 64)
					),
					'color' => array
					(
						'label'     => &$GLOBALS['TL_LANG']['tl_datamaps']['fills']['color'],
						'exclude'   => true,
						'inputType' => 'text',
						'eval'      => array('style' => 'width:300px', 'includeBlankOption' => true, 'chosen' => true)
					),
				)
			),
			'sql'       => "blob NULL"
		),
	)
);

class tl_datamaps extends Backend
{
	/**
	 * Import the back end user object
	 */
	public function __construct()
	{
		parent::__construct();
		$this->import('BackendUser', 'User');
	}

	public function getDataMapTypes(DataContainer $dc)
	{
		return \HeimrichHannot\Datamaps\DataMapSelectHelper::getDatamapsAsOptionsArray();
	}

	/**
	 * Return the edit header button
	 *
	 * @param array
	 * @param string
	 * @param string
	 * @param string
	 * @param string
	 * @param string
	 *
	 * @return string
	 */
	public function editHeader($row, $href, $label, $title, $icon, $attributes)
	{
		return ($this->User->isAdmin || count(preg_grep('/^tl_datamaps::/', $this->User->alexf)) > 0) ?
			'<a href="' . $this->addToUrl($href . '&amp;id=' . $row['id']) . '" title="' . specialchars($title) . '"' . $attributes . '>'
			. Image::getHtml($icon, $label) . '</a> ' : Image::getHtml(preg_replace('/\.gif$/i', '_.gif', $icon)) . ' ';
	}
}