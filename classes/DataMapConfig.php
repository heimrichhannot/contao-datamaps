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


use Contao\File;
use Contao\FrontendTemplate;

class DataMapConfig extends \Controller
{

	private static $arrUrlCache = array();

	protected $objConfig;

	public function __construct($objConfig)
	{
		parent::__construct();

		$this->objConfig = $objConfig;
	}

	public static function createConfigJs($objConfig, $debug = false)
	{
		$objInstance = new static($objConfig);

		$cache = !$GLOBALS['TL_CONFIG']['debugMode'];

		$objT = new FrontendTemplate('datamap.defaults');
		$objT->config = $objInstance->getConfigJs();
		$objT->bubbles = $objInstance->getConfigBubblesJs();
		$objT->states = $objInstance->getConfigStateJs();
		$objT->cssID = static::getCssIDFromModel($objConfig);

		$strName = $objT->cssID;
		$strFile = 'assets/js/' . $strName . '.js';
		$strFileMinified = 'assets/js/' . $strName . '.min.js';

		if (!file_exists(TL_ROOT . '/' . $strFile)) {
		    \File::putContent($strFile, '');
        }
		$objFile = new \File($strFile, );

        if (!file_exists(TL_ROOT . '/' . $strFileMinified)) {
            \File::putContent($strFileMinified, '');
        }
		$objFileMinified = new \File($strFileMinified, file_exists(TL_ROOT . '/' . $strFileMinified));
		$minify = $cache && class_exists('\MatthiasMullie\Minify\JS');

		// simple file caching
		if(static::doRewrite($objConfig, $objFile, $objFileMinified, $cache, $debug))
		{
			$strChunk = $objT->parse();
			$objFile->write($strChunk);
			$objFile->close();

			// minify js
			if($minify)
			{
				$objFileMinified = new \File($strFileMinified);
				$objMinify = new \MatthiasMullie\Minify\JS();
				$objMinify->add($strChunk);
				$objFileMinified->write(rtrim($objMinify->minify(), ";") . ";"); // append semicolon, otherwise "(intermediate value)(...) is not a function"
				$objFileMinified->close();
			}
		}

		$GLOBALS['TL_JAVASCRIPT']['d3.js'] = 'system/modules/datamaps/assets/vendor/d3/d3' . (!$GLOBALS['TL_CONFIG']['debugMode'] ? '.min' : '') . '.js|static';
		$GLOBALS['TL_JAVASCRIPT']['topojson'] = 'system/modules/datamaps/assets/vendor/topojson/topojson.js|static';
		$GLOBALS['TL_JAVASCRIPT']['datamaps.all'] = 'system/modules/datamaps/assets/vendor/datamaps/dist/datamaps.all' . ($GLOBALS['TL_CONFIG']['debugMode'] ? '.min' : '') . '.js|static';
		$GLOBALS['TL_JAVASCRIPT'][$strName] = $minify ? ($strFileMinified . '|static') : $strFile;
	}

	public static function doRewrite($objConfig, $objFile, $objFileMinified, $cache, $debug)
	{
		$rewrite = $objConfig->tstamp > $objFile->mtime || $objFile->size == 0 || ($cache && $objFileMinified == 0) || $debug;

		// child elements update trigger
		if($objConfig->rewrite)
		{
			$rewrite = true;
			$objConfig->rewrite = false;
			$objConfig->save();
		}

		return $rewrite;
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
		$objT->config = \StringUtil::decodeEntities($strConfig);

		return $objT->parse();
	}

	public static function getModelValuesAsStringArray(\Model $objModel, array $arrSkipFields = array())
	{
		$arrValues = array();

		\Controller::loadDataContainer($objModel::getTable());

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

		return \StringUtil::decodeEntities(json_encode($arrData));
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

		return \StringUtil::decodeEntities(json_encode($arrData));
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
					self::$arrUrlCache[$strCacheKey] = \StringUtil::encodeEmail($objItem->url);
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