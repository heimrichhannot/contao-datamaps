<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2015 Heimrich & Hannot GmbH
 *
 * @package anwaltverein
 * @author  Rico Kaltofen <r.kaltofen@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

namespace HeimrichHannot\Datamaps;


class DataMapConfig extends \Controller
{

	private static $arrUrlCache = array();

	protected $objConfig;

	protected function __construct($objConfig)
	{
		parent::__construct();

		$this->objConfig = $objConfig;
	}

	public static function createConfigJs($objConfig, $debug = true)
	{
		$objInstance = new static($objConfig);

		$objT = new \FrontendTemplate('datamap.defaults');
		$objT->config = $objInstance->getConfigJs();
		$objT->bubbles = $objInstance->getConfigBubblesJs();
		$objT->states = $objInstance->getConfigStateJs();
		$objT->cssID = static::getCssIDFromModel($objConfig);

		$strFile = 'assets/js/' . $objT->cssID . '.js';

		$objFile = new \File($strFile, file_exists(TL_ROOT . '/' . $strFile));

		// simple file caching
		if ($objConfig->tstamp > $objFile->mtime || $objFile->size == 0 || $debug) {
			$objFile->write($objT->parse());
			$objFile->close();
		}
		
		$GLOBALS['TL_JAVASCRIPT']['datamap_' . $objT->cssID] = $strFile . (!$debug ? '|static' : '');
	}

	protected function getConfigJs()
	{
		$arrConfig = array();

		$strConfig = '';

		foreach ($this->objConfig->row() as $key => $data) {
			switch ($key)
			{
				case 'defaultFill':
					$arrConfig['fills']['defaultFill'] = $data;
				break;
				case 'fills':
					$arrFills = deserialize($data, true);

					foreach($arrFills as $arrFill)
					{
						if(!isset($arrFill['key']) && !isset($arrFill['color'])) continue;

						$arrConfig['fills'][$arrFill['key']] = $arrFill['color'];
					}
					break;
				default:
					continue;
			}

		}
		
		$jsonConfig = json_encode($arrConfig);
		
		$strConfig = preg_replace(array('/^{/', '/}$/'), array('', ''), $jsonConfig); // remove start and trailing brace
		
		$objT = new \FrontendTemplate($GLOBALS['TL_DATAMAPS'][$this->objConfig->type]);
		$objT->setData(static::getModelValuesAsStringArray($this->objConfig, array('title', 'type', 'fills', 'defaultFill')));
		$objT->config = \String::decodeEntities($strConfig);

		return $objT->parse();
	}

	public static function getModelValuesAsStringArray(\Model $objModel, array $arrSkipFields = array())
	{
		$arrValues = array();

		foreach($objModel->row() as $key => $value)
		{
			if(!isset($GLOBALS['TL_DCA'][$objModel::getTable()]['fields'][$key])) continue;

			$arrData = $GLOBALS['TL_DCA'][$objModel::getTable()]['fields'][$key];

			if(!isset($arrData['inputType']) || in_array($key, $arrSkipFields)) continue;

			if($arrData['eval']['rgxp'] == 'digit')
			{
				$value = intval($value);
			}

			if($arrData['inputType'] == 'checkbox' && !$arrData['eval']['multiple'])
			{
				$value = $value ? 'true' : 'false';
			}

			if($arrData['eval']['multiple'] || $arrData['inputType'] == 'multiColumnWizard')
			{
				$value = deserialize($value, true);
			}

			// check type as well, otherwise
			if($value === '') continue;

			$arrValues[$key] = $value;
		}
		
		return $arrValues;
	}

	protected function getConfigStateJs()
	{
		$arrData = array();

		$objElements = DatamapsElementsModel::findPublishedByPidsAndTypes(array($this->objConfig->id), array(DATAMAP_ELEMENT_STATE));

		if ($objElements === null)
		{
			return '';
		}

		while ($objElements->next())
		{
			$arrData[$objElements->geoID] = $this->generateElementData($objElements, $this->objConfig);
		}

		return \String::decodeEntities(json_encode($arrData));
	}

	protected function getConfigBubblesJs()
	{
		$arrData = array();

		$objElements = DatamapsElementsModel::findPublishedByPidsAndTypes(array($this->objConfig->id), array(DATAMAP_ELEMENT_BUBBLE));

		if ($objElements === null)
		{
			return '';
		}

		while ($objElements->next())
		{
			$arrData[] = $this->generateElementData($objElements, $this->objConfig);
		}

		return \String::decodeEntities(json_encode($arrData));
	}

	protected function generateElementData($objItem)
	{
		$arrData = $objItem->row();

		if (($strUrl = static::generateElementUrl($objItem)) !== null)
		{
			$arrData['link'] = $this->replaceInsertTags($strUrl);
		}

		return $arrData;
	}

	/**
	 * Generate a URL and return it as string
	 *
	 * @param object
	 * @param boolean
	 *
	 * @return string
	 */
	protected function generateElementUrl($objItem)
	{
		$strCacheKey = 'id_' . $objItem->id;

		// Load the URL from cache
		if (isset(self::$arrUrlCache[$strCacheKey])) {
			return self::$arrUrlCache[$strCacheKey];
		}

		// Initialize the cache
		self::$arrUrlCache[$strCacheKey] = null;

		switch ($objItem->source) {
			// Link to an external page
			case 'external':
				if (substr($objItem->url, 0, 7) == 'mailto:') {
					self::$arrUrlCache[$strCacheKey] = \String::encodeEmail($objItem->url);
				} else {
					self::$arrUrlCache[$strCacheKey] = ampersand($objItem->url);
				}
				break;

			// Link to an internal page
			case 'internal':
				if (($objTarget = $objItem->getRelated('jumpTo')) !== null) {
					self::$arrUrlCache[$strCacheKey] = ampersand(\Controller::generateFrontendUrl($objTarget->row()));
				}
				break;

			// Link to an article
			case 'article':
				if (($objArticle = \ArticleModel::findByPk($objItem->articleId, array('eager' => true))) !== null
					&& ($objPid = $objArticle->getRelated('pid')) !== null
				) {
					self::$arrUrlCache[$strCacheKey] = ampersand(
						\Controller::generateFrontendUrl(
							$objPid->row(),
							'/articles/' . ((!\Config::get('disableAlias')
											 && $objArticle->alias != '') ? $objArticle->alias : $objArticle->id)
						)
					);
				}
				break;
		}

		return self::$arrUrlCache[$strCacheKey];
	}

	public static function getCssIDFromModel($objConfig)
	{
		$strID = static::stripNamespaceFromClassName($objConfig);

		return 'datamap_' . substr(md5($strID . '_' . $objConfig->id), 0, 6);
	}

	public static function stripNamespaceFromClassName($obj)
	{
		$strClass = get_class($obj);

		if (preg_match('@\\\\([\w]+)$@', $strClass, $matches)) {
			$strClass = $matches[1];
		}

		return $strClass;
	}

	public function getCSSClassFromModel($objConfig)
	{
		return standardize($objConfig->type);
	}
}