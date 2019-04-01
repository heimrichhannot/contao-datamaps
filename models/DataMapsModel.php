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


use Contao\Model;

/**
 * Class DataMapsModel
 * @package HeimrichHannot\Datamaps
 *
 * @property int $id
 * @property int $tstamp
 * @property string|bool $rewrite
 * @property string $title
 */
class DataMapsModel extends Model
{

	protected static $strTable = 'tl_datamaps';

}