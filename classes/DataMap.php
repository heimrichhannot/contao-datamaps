<?php
/**
 * Contao Open Source CMS
 * 
 * Copyright (c) 2015 Heimrich & Hannot GmbH
 * @package anwaltverein
 * @author Rico Kaltofen <r.kaltofen@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

namespace HeimrichHannot\Datamaps;


class DataMap implements \ArrayAccess, \Countable, \IteratorAggregate
{
	/**
	 * Current index
	 * @var integer
	 */
	protected $intIndex = -1;

	/**
	 * Models
	 * @var array
	 */
	protected $arrItems = array();


	/**
	 * Create a new datamap
	 *
	 * @param array $arrItems An array of items from the datamap
	 *
	 */
	public function __construct(array $arrItems)
	{
		$this->arrItems = array_values($arrItems);
	}

	public static function getGeoData($strType)
	{
		$arrData = array();

		$strTemplate = $GLOBALS['TL_DATAMAPS'][$strType];

		$strTemplatePath = \Frontend::getTemplate($strTemplate);

		$strContent = file_get_contents($strTemplatePath);
		
		preg_match('#dataUrl:?[\s][\'|\"](?<PATH>.*.json)[\'|\"]#', $strContent, $arrMatches);

		if(($strMapPath = $arrMatches['PATH']) == '') return $arrData;

		if(!file_exists(TL_ROOT . '/'. $strMapPath)) return $arrData;

		preg_match('#scope:?[\s][\'|\"](?<SCOPE>.*)[\'|\"]#', $strContent, $arrMatches);

		if(($strScope = $arrMatches['SCOPE']) == '') return $arrData;

		$strDataMap = file_get_contents(TL_ROOT . '/'. $strMapPath);

		$arrMapData = json_decode($strDataMap);
		
		$arrData = $arrMapData->objects->{$strScope}->geometries;

		return new static($arrData);
	}


	/**
	 * Return the current item as associative array
	 *
	 * @return array The current item as array
	 */
	public function row()
	{
		if ($this->intIndex < 0)
		{
			$this->first();
		}

		return $this->arrModels[$this->intIndex];
	}

	/**
	 * Set an object property
	 *
	 * @param string $strKey   The property name
	 * @param mixed  $varValue The property value
	 */
	public function __set($strKey, $varValue)
	{
		if ($this->intIndex < 0)
		{
			$this->first();
		}

		$this->arrItems[$this->intIndex]->{$strKey} = $varValue;
	}


	/**
	 * Return an object property
	 *
	 * @param string $strKey The property name
	 *
	 * @return mixed|null The property value or null
	 */
	public function __get($strKey)
	{
		if ($this->intIndex < 0)
		{
			$this->first();
		}

		if (isset($this->arrItems[$this->intIndex]->{$strKey}))
		{
			return $this->arrItems[$this->intIndex]->{$strKey};
		}

		return null;
	}


	/**
	 * Check whether a property is set
	 *
	 * @param string $strKey The property name
	 *
	 * @return boolean True if the property is set
	 */
	public function __isset($strKey)
	{
		if ($this->intIndex < 0)
		{
			$this->first();
		}

		return isset($this->arrItems[$this->intIndex]->{$strKey});
	}

	/**
	 * Return the number of rows in the result set
	 *
	 * @return integer The number of rows
	 */
	public function count()
	{
		return count($this->arrModels);
	}


	/**
	 * Go to the first row
	 *
	 * @return \Model\Collection The model collection object
	 */
	public function first()
	{
		$this->intIndex = 0;
		return $this;
	}


	/**
	 * Go to the previous row
	 *
	 * @return \Model\Collection|false The model collection object or false if there is no previous row
	 */
	public function prev()
	{
		if ($this->intIndex < 1)
		{
			return false;
		}

		--$this->intIndex;
		return $this;
	}


	/**
	 * Return the current model
	 *
	 * @return \Model The model object
	 */
	public function current()
	{
		if ($this->intIndex < 0)
		{
			$this->first();
		}

		return $this->arrModels[$this->intIndex];
	}


	/**
	 * Go to the next item
	 *
	 * @return \Array The array or false if there is no next row
	 */
	public function next()
	{
		if (!isset($this->arrItems[$this->intIndex + 1]))
		{
			return false;
		}

		++$this->intIndex;
		return $this;
	}


	/**
	 * Go to the last row
	 *
	 * @return \Array The array
	 */
	public function last()
	{
		$this->intIndex = count($this->arrItems) - 1;
		return $this;
	}


	/**
	 * Reset the datamap
	 *
	 * @return \Array The array datamap
	 */
	public function reset()
	{
		$this->intIndex = -1;
		return $this;
	}


	public function fetchEach($strKey)
	{
		$this->reset();
		$return = array();

		while ($this->next())
		{
			$return[] = $this->{$strKey};
		}

		return $return;
	}


	/**
	 * Fetch all columns of every row
	 *
	 * @return array An array with all items
	 */
	public function fetchAll()
	{
		$this->reset();
		$return = array();

		while ($this->next())
		{
			$return[] = $this;
		}

		return $return;
	}

	/**
	 * Check whether an offset exists
	 *
	 * @param integer $offset The offset
	 *
	 * @return boolean True if the offset exists
	 */
	public function offsetExists($offset)
	{
		return isset($this->arrItems[$offset]);
	}


	/**
	 * Retrieve a particular offset
	 *
	 * @param integer $offset The offset
	 *
	 * @return \Array|null The array or null
	 */
	public function offsetGet($offset)
	{
		return $this->arrItems[$offset];
	}


	/**
	 * Set a particular offset
	 *
	 * @param integer $offset The offset
	 * @param mixed   $value  The value to set
	 *
	 * @throws \RuntimeException The collection is immutable
	 */
	public function offsetSet($offset, $value)
	{
		throw new \RuntimeException('This collection is immutable');
	}


	/**
	 * Unset a particular offset
	 *
	 * @param integer $offset The offset
	 *
	 * @throws \RuntimeException The collection is immutable
	 */
	public function offsetUnset($offset)
	{
		throw new \RuntimeException('This collection is immutable');
	}


	/**
	 * Retrieve the iterator object
	 *
	 * @return \ArrayIterator The iterator object
	 */
	public function getIterator()
	{
		return new \ArrayIterator($this->arrItems);
	}
}