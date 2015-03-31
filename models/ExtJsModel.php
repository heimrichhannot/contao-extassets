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
 * Class ExtJsModel
 */
class ExtJsModel extends \Model
{

	protected static $strTable = 'tl_extjs';

	/**
	 * Find multiple javasript groups by their IDs
	 *
	 * @param array $arrIds     An array of group IDs
	 * @param array $arrOptions An optional options array
	 *
	 * @return \Model\Collection|null A collection of javasript groups or null if there are no javasript groups
	 */
	public static function findMultipleByIds($arrIds, array $arrOptions=array())
	{
		if (!is_array($arrIds) || empty($arrIds))
		{
			return null;
		}

		$t = static::$strTable;

		if (!isset($arrOptions['order']))
		{
			$arrOptions['order'] = \Database::getInstance()->findInSet("$t.id", $arrIds);
		}

		return static::findBy(array("$t.id IN(" . implode(',', array_map('intval', $arrIds)) . ")"), null, $arrOptions);
	}

}