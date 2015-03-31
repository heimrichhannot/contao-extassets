<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (C) 2005-2013 Leo Feyer
 *
 * @package   ExtAssets
 * @author    r.kaltofen@heimrich-hannot.de
 * @license   GNU/LGPL
 * @copyright Heimrich & Hannot GmbH
 */


/**
 * Namespace
 */
namespace ExtAssets;

if(!class_exists('ExtCssFileModel'))
{
	
/**
 * Class ExtCssFileModel
 */
class ExtCssFileModel extends \Model
{

	protected static $strTable = 'tl_extcss_file';


	/**
	 * Find multiple css files by their pid
	 *
	 * @param array $arrIds     An array of group IDs
	 * @param array $arrOptions An optional options array
	 *
	 * @return \Model\Collection|null A collection of css files or null if there are no css files
	 */
	public static function findMultipleByPids(array $arrPids = array(), array $arrOptions=array())
	{
		$t = static::$strTable;

		if (!is_array($arrPids) || empty($arrPids))
		{
			return null;
		}

		$arrColumns = array("$t.pid IN(" . implode(',', array_map('intval', $arrPids)) . ")");

		if (!isset($arrOptions['order']))
		{
			$arrOptions['order'] = "$t.sorting";
		}

		return static::findBy($arrColumns, null, $arrOptions);
	}

}

}