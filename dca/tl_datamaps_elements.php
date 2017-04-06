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

$GLOBALS['TL_DCA']['tl_datamaps_elements'] = array
(
	// Config
	'config'      => array
	(
		'dataContainer'    => 'Table',
		'ptable'           => 'tl_datamaps',
		'enableVersioning' => true,
		'sql'              => array
		(
			'keys' => array
			(
				'id' => 'primary'
			)
		),
		'onsubmit_callback' => array
		(
			array('tl_datamaps_elements', 'setRewriteFlag')
		),
		'ondelete_callback' => array
		(
			array('tl_datamaps_elements', 'setRewriteFlag')
		),
	),
	// List
	'list'        => array
	(
		'sorting'           => array
		(
			'mode'                  => 4,
			'fields'                => array('sorting'),
			'panelLayout'           => 'filter;sort,search,limit',
			'headerFields'          => array('title'),
			'child_record_callback' => array('tl_datamaps_elements', 'listElements')
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
			'edit'   => array
			(
				'label' => &$GLOBALS['TL_LANG']['tl_datamaps_elements']['edit'],
				'href'  => 'act=edit',
				'icon'  => 'edit.gif'
			),
			'copy'   => array
			(
				'label' => &$GLOBALS['TL_LANG']['tl_datamaps_elements']['copy'],
				'href'  => 'act=paste&amp;mode=copy',
				'icon'  => 'copy.gif'
			),
			'cut'    => array
			(
				'label' => &$GLOBALS['TL_LANG']['tl_datamaps_elements']['cut'],
				'href'  => 'act=paste&amp;mode=cut',
				'icon'  => 'cut.gif'
			),
			'delete' => array
			(
				'label'      => &$GLOBALS['TL_LANG']['tl_datamaps_elements']['delete'],
				'href'       => 'act=delete',
				'icon'       => 'delete.gif',
				'attributes' => 'onclick="if (!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm']
								. '\')) return false; Backend.getScrollOffset();"'
			),
			'toggle' => array
			(
				'label'           => &$GLOBALS['TL_LANG']['tl_datamaps_elements']['toggle'],
				'icon'            => 'visible.gif',
				'attributes'      => 'onclick="Backend.getScrollOffset(); return AjaxRequest.toggleVisibility(this, %s);"',
				'button_callback' => array('tl_datamaps_elements', 'toggleIcon')
			),
			'show'   => array
			(
				'label' => &$GLOBALS['TL_LANG']['tl_datamaps_elements']['show'],
				'href'  => 'act=show',
				'icon'  => 'show.gif'
			)
		)
	),
	// Palettes
	'palettes'    => array
	(
		'__selector__'         => array('type', 'source', 'addLabel', 'published'),
		'default'              => '{title_legend},title,type',
		DATAMAP_ELEMENT_BUBBLE => '{title_legend},title,type;
									{location_legend},geocoderAddress,geocoderCountry,latitude,longitude;
									{source_legend:hide},source;
									{config_legend},fillKey,radius,borderWidth,borderColor,popupOnHover;
									{label_legend},addLabel;
									{publish_legend},published',
		DATAMAP_ELEMENT_STATE  => '{title_legend},title,type;
									{state_legend},geoID;
									{source_legend:hide},source;
									{config_legend},fillKey;
									{label_legend},addLabel;
									{publish_legend},published',
	),
	// Subpalettes
	'subpalettes' => array
	(
		'source_internal' => 'jumpTo',
		'source_article'  => 'articleId',
		'source_external' => 'url,target',
		'published'       => 'start,stop',
		'addLabel'        => 'labelOffsetX, labelOffsetY,smallState',
	),
	// Fields
	'fields'      => array
	(
		'id'              => array
		(
			'sql' => "int(10) unsigned NOT NULL auto_increment"
		),
		'pid'             => array
		(
			'foreignKey' => 'tl_datamaps.title',
			'sql'        => "int(10) unsigned NOT NULL default '0'",
			'relation'   => array('type' => 'belongsTo', 'load' => 'lazy')
		),
		'sorting'         => array
		(
			'sql' => "int(10) unsigned NOT NULL default '0'"
		),
		'tstamp'          => array
		(
			'sql' => "int(10) unsigned NOT NULL default '0'"
		),
		'title'           => array
		(
			'label'     => &$GLOBALS['TL_LANG']['tl_datamaps_elements']['title'],
			'exclude'   => true,
			'search'    => true,
			'inputType' => 'text',
			'eval'      => array('mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50'),
			'sql'       => "varchar(255) NOT NULL default ''"
		),
		'type'            => array
		(
			'label'     => &$GLOBALS['TL_LANG']['tl_datamaps_elements']['type'],
			'exclude'   => true,
			'filter'    => true,
			'inputType' => 'select',
			'options'   => array(DATAMAP_ELEMENT_BUBBLE, DATAMAP_ELEMENT_STATE),
			'default'   => DATAMAP_ELEMENT_BUBBLE,
			'reference' => &$GLOBALS['TL_LANG']['tl_datamaps_elements']['references'],
			'eval'      => array('mandatory' => true, 'submitOnChange' => true, 'tl_class' => 'w50'),
			'sql'       => "varchar(32) NOT NULL default ''"
		),
		'geoID'           => array
		(
			'label'            => &$GLOBALS['TL_LANG']['tl_datamaps_elements']['geoID'],
			'exclude'          => true,
			'inputType'        => 'select',
			'options_callback' => array('tl_datamaps_elements', 'getGeoIDs'),
			'eval'             => array('mandatory' => true, 'tl_class' => 'w50'),
			'sql'              => "varchar(32) NOT NULL default ''"
		),
		'geocoderAddress' => array
		(
			'label'     => &$GLOBALS['TL_LANG']['tl_datamaps_elements']['geocoderAddress'],
			'inputType' => 'text',
			'eval'      => array('maxlength' => 255, 'tl_class' => 'w50'),
			'sql'       => "varchar(255) NOT NULL default ''"
		),
		'geocoderCountry' => array
		(
			'label'     => &$GLOBALS['TL_LANG']['tl_datamaps_elements']['geocoderCountry'],
			'inputType' => 'select',
			'options'   => $this->getCountries(),
			'eval'      => array('includeBlankOption' => true, 'tl_class' => 'w50'),
			'sql'       => "varchar(2) NOT NULL default 'de'"
		),
		'latitude'        => array
		(
			'label'         => &$GLOBALS['TL_LANG']['tl_datamaps_elements']['latitude'],
			'exclude'       => true,
			'search'        => true,
			'inputType'     => 'text',
			'eval'          => array('maxlength' => 16, 'rgxp' => 'digit', 'tl_class' => 'w50'),
			'sql'           => "float(10,6) unsigned NOT NULL default '0.000000'",
			'save_callback' => array
			(
				array('tl_datamaps_elements', 'generateLatitude')
			)
		),
		'longitude'       => array
		(
			'label'         => &$GLOBALS['TL_LANG']['tl_datamaps_elements']['longitude'],
			'exclude'       => true,
			'search'        => true,
			'inputType'     => 'text',
			'eval'          => array('maxlength' => 16, 'rgxp' => 'digit', 'tl_class' => 'w50'),
			'sql'           => "float(10,6) unsigned NOT NULL default '0.000000'",
			'save_callback' => array
			(
				array('tl_datamaps_elements', 'generateLongitude')
			)
		),
		'source'          => array
		(
			'label'            => &$GLOBALS['TL_LANG']['tl_datamaps_elements']['source'],
			'default'          => 'none',
			'exclude'          => true,
			'filter'           => true,
			'inputType'        => 'radio',
			'options_callback' => array('tl_datamaps_elements', 'getSourceOptions'),
			'reference'        => &$GLOBALS['TL_LANG']['tl_news'],
			'eval'             => array('submitOnChange' => true, 'helpwizard' => true),
			'sql'              => "varchar(12) NOT NULL default ''"
		),
		'jumpTo'          => array
		(
			'label'      => &$GLOBALS['TL_LANG']['tl_datamaps_elements']['jumpTo'],
			'exclude'    => true,
			'inputType'  => 'pageTree',
			'foreignKey' => 'tl_page.title',
			'eval'       => array('mandatory' => true, 'fieldType' => 'radio'),
			'sql'        => "int(10) unsigned NOT NULL default '0'",
			'relation'   => array('type' => 'belongsTo', 'load' => 'lazy')
		),
		'articleId'       => array
		(
			'label'            => &$GLOBALS['TL_LANG']['tl_datamaps_elements']['articleId'],
			'exclude'          => true,
			'inputType'        => 'select',
			'options_callback' => array('tl_datamaps_elements', 'getArticleAlias'),
			'eval'             => array('chosen' => true, 'mandatory' => true),
			'sql'              => "int(10) unsigned NOT NULL default '0'"
		),
		'url'             => array
		(
			'label'     => &$GLOBALS['TL_LANG']['MSC']['url'],
			'exclude'   => true,
			'search'    => true,
			'inputType' => 'text',
			'eval'      => array('mandatory' => true, 'decodeEntities' => true, 'maxlength' => 255, 'tl_class' => 'w50'),
			'sql'       => "varchar(255) NOT NULL default ''"
		),
		'target'          => array
		(
			'label'     => &$GLOBALS['TL_LANG']['MSC']['target'],
			'exclude'   => true,
			'inputType' => 'checkbox',
			'eval'      => array('tl_class' => 'w50 m12'),
			'sql'       => "char(1) NOT NULL default ''"
		),
		'fillKey'         => array
		(
			'label'            => &$GLOBALS['TL_LANG']['tl_datamaps_elements']['fillKey'],
			'exclude'          => true,
			'inputType'        => 'select',
			'options_callback' => array('tl_datamaps_elements', 'getFillKeys'),
			'eval'             => array('chosen' => true, 'includeBlankOption' => true),
			'sql'              => "varchar(64) NOT NULL default ''"
		),
		'radius'          => array
		(
			'label'     => &$GLOBALS['TL_LANG']['tl_datamaps_elements']['radius'],
			'exclude'   => true,
			'default'   => 1,
			'inputType' => 'text',
			'eval'      => array('rgxp' => 'digit', 'mandatory' => true, 'tl_class' => 'w50'),
			'sql'       => "int(10) unsigned NOT NULL default '1'"
		),
		'borderWidth'     => array
		(
			'label'     => &$GLOBALS['TL_LANG']['tl_datamaps_elements']['borderWidth'],
			'exclude'   => true,
			'default'   => 1,
			'inputType' => 'text',
			'eval'      => array('rgxp' => 'digit', 'mandatory' => true, 'tl_class' => 'w50'),
			'sql'       => "int(10) unsigned NOT NULL default '1'"
		),
		'borderColor'     => array
		(
			'label'     => &$GLOBALS['TL_LANG']['tl_datamaps_elements']['borderColor'],
			'exclude'   => true,
			'default'   => 'rgb(48, 52, 53)',
			'inputType' => 'text',
			'eval'      => array('mandatory' => true, 'maxlength' => 32, 'tl_class' => 'w50'),
			'sql'       => "varchar(32) NOT NULL default ''"
		),
		'popupOnHover'    => array
		(
			'label'     => &$GLOBALS['TL_LANG']['tl_datamaps_elements']['popupOnHover'],
			'exclude'   => true,
			'inputType' => 'checkbox',
			'default'   => true,
			'eval'      => array('tl_class' => 'clr'),
			'sql'       => "char(1) NOT NULL default ''"
		),
		'addLabel' => array
		(
			'label'     => &$GLOBALS['TL_LANG']['tl_datamaps_elements']['addLabel'],
			'exclude'   => true,
			'flag'      => 1,
			'inputType' => 'checkbox',
			'eval'      => array('submitOnChange' => true),
			'sql'       => "char(1) NOT NULL default ''"
		),
		'labelOffsetX' => array
		(
			'label'     => &$GLOBALS['TL_LANG']['tl_datamaps_elements']['labelOffsetX'],
			'exclude'   => true,
			'default'   => 5.00, //required by bubble label to prevent overlap
			'inputType' => 'text',
			'eval'          => array('maxlength' => 10, 'rgxp' => 'digit', 'tl_class' => 'w50'),
			'sql'           => "float(10,2) NOT NULL default '5.00'",
		),
		'labelOffsetY' => array
		(
			'label'     => &$GLOBALS['TL_LANG']['tl_datamaps_elements']['labelOffsetY'],
			'exclude'   => true,
			'default'   => 0,
			'inputType' => 'text',
			'eval'          => array('maxlength' => 10, 'rgxp' => 'digit', 'tl_class' => 'w50'),
			'sql'           => "float(10,2) NOT NULL default '0.00'",
		),
		'smallState' => array
		(
			'label'     => &$GLOBALS['TL_LANG']['tl_datamaps_elements']['smallState'],
			'exclude'   => true,
			'flag'      => 1,
			'inputType' => 'checkbox',
			'sql'       => "char(1) NOT NULL default ''"
		),
		'published'       => array
		(
			'label'     => &$GLOBALS['TL_LANG']['tl_datamaps_elements']['published'],
			'exclude'   => true,
			'filter'    => true,
			'flag'      => 1,
			'inputType' => 'checkbox',
			'save_callback' => array
			(
				array('tl_datamaps_elements', 'setRewriteFlagOnPublish')
			),
			'eval'      => array('submitOnChange' => true, 'doNotCopy' => true),
			'sql'       => "char(1) NOT NULL default ''"
		),
		'start'           => array
		(
			'label'     => &$GLOBALS['TL_LANG']['tl_datamaps_elements']['start'],
			'exclude'   => true,
			'inputType' => 'text',
			'eval'      => array('rgxp' => 'datim', 'datepicker' => true, 'tl_class' => 'w50 wizard'),
			'sql'       => "varchar(10) NOT NULL default ''"
		),
		'stop'            => array
		(
			'label'     => &$GLOBALS['TL_LANG']['tl_datamaps_elements']['stop'],
			'exclude'   => true,
			'inputType' => 'text',
			'eval'      => array('rgxp' => 'datim', 'datepicker' => true, 'tl_class' => 'w50 wizard'),
			'sql'       => "varchar(10) NOT NULL default ''"
		)
	),
);

class tl_datamaps_elements extends Backend
{

	/**
	 * Import the back end user object
	 */
	public function __construct()
	{
		parent::__construct();
		$this->import('BackendUser', 'User');
	}

	public function getGeoIDs(DataContainer $dc)
	{
		$arrOptions = array();

		$objDataMap = \HeimrichHannot\Datamaps\DataMapsModel::findByPk($dc->activeRecord->pid);

		if ($objDataMap === null) {
			return $arrOptions;
		}

		$objGeo = \HeimrichHannot\Datamaps\DataMap::getGeoData($objDataMap->type);

		while ($objGeo->next()) {
			$arrOptions[$objGeo->id] = $objGeo->id . ($objGeo->properties->name ? ' [' . $objGeo->properties->name . ']' : '');
		}

		return $arrOptions;
	}

	public function getFillKeys(DataContainer $dc)
	{
		$arrOptions = array();

		$objDataMap = \HeimrichHannot\Datamaps\DataMapsModel::findByPk($dc->activeRecord->pid);

		if ($objDataMap === null) {
			return $arrOptions;
		}
		
		$arrFills = deserialize($objDataMap->fills, true);
		
		foreach ($arrFills as $arrFill) {
			if (empty($arrFill['key'])) {
				continue;
			}

			$arrOptions[$arrFill['key']] = $arrFill['key'];
		}

		return $arrOptions;
	}

	/**
	 * Return the "toggle visibility" button
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
	public function toggleIcon($row, $href, $label, $title, $icon, $attributes)
	{
		if (strlen($this->Input->get('tid'))) {
			$this->toggleVisibility($this->Input->get('tid'), ($this->Input->get('state') == 1), (@func_get_arg(12) ?: null));
			$this->redirect($this->getReferer());
		}

		// Check permissions AFTER checking the tid, so hacking attempts are logged
		if (!$this->User->isAdmin && !$this->User->hasAccess('tl_datamaps_elements::published', 'alexf')) {
			return '';
		}

		$href .= '&amp;tid=' . $row['id'] . '&amp;state=' . ($row['published'] ? '' : 1);

		if (!$row['published']) {
			$icon = 'invisible.gif';
		}

		return '<a href="' . $this->addToUrl($href) . '" title="' . specialchars($title) . '"' . $attributes . '>' . Image::getHtml($icon, $label)
			   . '</a> ';
	}


	/**
	 * Disable/enable an element
	 *
	 * @param integer
	 * @param boolean
	 */
	public function toggleVisibility($intId, $blnVisible, DataContainer $dc=null)
	{
		// Check permissions to publish
		if (!$this->User->isAdmin && !$this->User->hasAccess('tl_datamaps_elements::published', 'alexf')) {
			$this->log('Not enough permissions to publish/unpublish Datamaps Element ID "' . $intId . '"', __METHOD__, TL_ERROR);
			$this->redirect('contao/main.php?act=error');
		}

		$objVersions = new Versions('tl_datamaps_elements', $intId);
		$objVersions->initialize();

		// Trigger the save_callback
		if (is_array($GLOBALS['TL_DCA']['tl_datamaps_elements']['fields']['published']['save_callback'])) {
			foreach ($GLOBALS['TL_DCA']['tl_datamaps_elements']['fields']['published']['save_callback'] as $callback) {
				if (is_array($callback)) {
					$this->import($callback[0]);
					$blnVisible = $this->{$callback[0]}->{$callback[1]}($blnVisible, ($dc ?: $this));
				} elseif (is_callable($callback)) {
					$blnVisible = $callback($blnVisible, $this);
				}
			}
		}

		// Update the database
		$this->Database->prepare("UPDATE tl_datamaps_elements SET tstamp=" . time() . ", published='" . ($blnVisible ? 1 : '') . "' WHERE id=?")
			->execute($intId);

		$objVersions->create();
		$this->log(
			'A new version of record "tl_datamaps_elements.id=' . $intId . '" has been created' . $this->getParentEntries('tl_datamaps', $intId),
			__METHOD__,
			TL_GENERAL
		);
	}

	/**
	 * Get all articles and return them as array
	 *
	 * @param \DataContainer
	 *
	 * @return array
	 */
	public function getArticleAlias(DataContainer $dc)
	{
		$arrPids  = array();
		$arrAlias = array();

		if (!$this->User->isAdmin) {
			foreach ($this->User->pagemounts as $id) {
				$arrPids[] = $id;
				$arrPids   = array_merge($arrPids, $this->Database->getChildRecords($id, 'tl_page'));
			}

			if (empty($arrPids)) {
				return $arrAlias;
			}

			$objAlias = $this->Database->prepare(
				"SELECT a.id, a.title, a.inColumn, p.title AS parent FROM tl_article a LEFT JOIN tl_page p ON p.id=a.pid WHERE a.pid IN(" . implode(
					',',
					array_map('intval', array_unique($arrPids))
				) . ") ORDER BY parent, a.sorting"
			)
				->execute($dc->id);
		} else {
			$objAlias = $this->Database->prepare(
				"SELECT a.id, a.title, a.inColumn, p.title AS parent FROM tl_article a LEFT JOIN tl_page p ON p.id=a.pid ORDER BY parent, a.sorting"
			)
				->execute($dc->id);
		}

		if ($objAlias->numRows) {
			System::loadLanguageFile('tl_article');

			while ($objAlias->next()) {
				$arrAlias[$objAlias->parent][$objAlias->id] =
					$objAlias->title . ' (' . ($GLOBALS['TL_LANG']['tl_article'][$objAlias->inColumn] ?: $objAlias->inColumn) . ', ID '
					. $objAlias->id . ')';
			}
		}

		return $arrAlias;
	}


	/**
	 * Add the source options depending on the allowed fields (see #5498)
	 *
	 * @param \DataContainer
	 *
	 * @return array
	 */
	public function getSourceOptions(DataContainer $dc)
	{
		if ($this->User->isAdmin) {
			return array('none', 'internal', 'article', 'external');
		}

		$arrOptions = array('none');

		// Add the "internal" option
		if ($this->User->hasAccess('tl_datamaps_elements::jumpTo', 'alexf')) {
			$arrOptions[] = 'internal';
		}

		// Add the "article" option
		if ($this->User->hasAccess('tl_datamaps_elements::articleId', 'alexf')) {
			$arrOptions[] = 'article';
		}

		// Add the "external" option
		if ($this->User->hasAccess('tl_datamaps_elements::url', 'alexf') && $this->User->hasAccess('tl_datamaps_elements::target', 'alexf')) {
			$arrOptions[] = 'external';
		}

		// Add the option currently set
		if ($dc->activeRecord && $dc->activeRecord->source != '') {
			$arrOptions[] = $dc->activeRecord->source;
			$arrOptions   = array_unique($arrOptions);
		}

		return $arrOptions;
	}

	/**
	 * Get geo latitude from address
	 *
	 * @param string
	 * @param object
	 *
	 * @return string
	 */
	function generateLatitude($varValue, DataContainer $dc)
	{
		if ($varValue) {
			return $varValue;
		}

		$varCoordinates = \delahaye\GeoCode::getCoordinates($dc->activeRecord->geocoderAddress, $dc->activeRecord->geocoderCountry, 'de');

		$arrCoordinates = trimsplit(',', $varCoordinates);

		return $arrCoordinates[0];
	}

	/**
	 * Get geo latitude from address
	 *
	 * @param string
	 * @param object
	 *
	 * @return string
	 */
	function generateLongitude($varValue, DataContainer $dc)
	{
		if ($varValue) {
			return $varValue;
		}

		$varCoordinates = \delahaye\GeoCode::getCoordinates($dc->activeRecord->geocoderAddress, $dc->activeRecord->geocoderCountry, 'de');

		$arrCoordinates = trimsplit(',', $varCoordinates);

		return $arrCoordinates[1];
	}

	/**
	 * List records
	 *
	 * @param array
	 *
	 * @return string
	 */
	public function listElements($arrRow)
	{
		$key    = $arrRow['published'] ? 'published' : 'unpublished';
		$return = '<div class="cte_type ' . $key . '"><strong>' . $arrRow['title'] . '</strong></div><div>'
				  . $GLOBALS['TL_LANG']['tl_datamaps_elements']['references'][$arrRow['type']] . '</div>' . "\n";

		return $return;
	}

	/**
	 * Callback, that detects the current element and triggers set rewrite flag on parent datamap
	 *
	 * @param DataContainer $dc
	 *
	 * @return bool
	 */
	public function setRewriteFlag(DataContainer $dc)
	{
		$objElement = \HeimrichHannot\Datamaps\DatamapsElementsModel::findByPk($dc->id);

		if($objElement === null)
		{
			return false;
		}

		$this->setRewriteOnParent($objElement->pid);
	}

	/**
	 * Trigger set rewrite flag on parent datamap on toggle element
	 *
	 * @param Boolean       $varValue
	 * @param DataContainer $dc
	 *
	 * @return mixed
	 */
	public function setRewriteFlagOnPublish($varValue, DataContainer $dc)
	{
		$this->setRewriteOnParent($dc->id);

		return $varValue;
	}

	/**
	 * Set rewrite flag on parent datamap
	 *
	 * @param $intParent DataMapsModel ID
	 *
	 * @return bool
	 */
	public function setRewriteOnParent($intParent)
	{
		$objDataMap = HeimrichHannot\Datamaps\DataMapsModel::findByPk($intParent);

		if($objDataMap === null)
		{
			return false;
		}

		$objDataMap->rewrite = true;
		$objDataMap->save();
	}
}