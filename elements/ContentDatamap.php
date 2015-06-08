<?php
/**
 * Contao Open Source CMS
 * 
 * Copyright (c) 2015 Heimrich & Hannot GmbH
 * @package datamaps
 * @author Rico Kaltofen <r.kaltofen@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

namespace HeimrichHannot\Datamaps;


class ContentDatamap extends \ContentElement
{
	protected $strTemplate = 'ce_datamap';

	protected $objConfig;

	public function generate()
	{
		if (TL_MODE == 'BE')
		{
			$this->strTemplate = 'be_wildcard';
			$this->Template = new \BackendTemplate($this->strTemplate);
			$this->Template->title = $this->headline;
		}

		$this->objConfig = DataMapsModel::findByPk($this->datamap);

		if ($this->objConfig === null) return;

		return parent::generate();
	}

	protected function compile()
	{
		DataMapConfig::createConfigJs($this->objConfig); // $this is needed for reference (replaceinserttags)
		$this->Template->datamapCssID = DataMapConfig::getCssIDFromModel($this->objConfig);
		$this->Template->datamapCssClass = DataMapConfig::getCSSClassFromModel($this->objConfig);
	}
}