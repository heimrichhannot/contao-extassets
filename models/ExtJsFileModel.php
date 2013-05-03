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

/**
 * Class ExtJsFileModel
 */
class ExtJsFileModel extends \Model
{

	protected static $strTable = 'tl_extjs_file';

	/**
	 * Find multiple javasript files by their IDs
	 *
	 * @param array $arrIds     An array of group IDs
	 * @param array $arrOptions An optional options array
	 *
	 * @return \Model\Collection|null A collection of javascript files or null if there are no javascript files
	 */
	public static function findMultipleByPid($intId, array $arrOptions=array())
	{
		$t = static::$strTable;

		$arrColumns = array("$t.pid=?");

		if (!isset($arrOptions['order']))
		{
			$arrOptions['order'] = "$t.sorting";
		}

		return static::findBy($arrColumns, $intId, $arrOptions);
	}

}