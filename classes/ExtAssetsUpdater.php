<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2014 Heimrich & Hannot GmbH
 * @package extassets
 * @author Rico Kaltofen <r.kaltofen@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

namespace ExtAssets;


class ExtAssetsUpdater
{
	public static function run()
	{
		$objDatabase = \Database::getInstance();

		$arrFields = array
		(
			'tl_extcss'      => array('bootstrapVariablesSRC', 'observeFolderSRC'),
			'tl_extcss_file' => array('src'),
			'tl_extjs_file'  => array('src'),
		);

		if (version_compare(VERSION, '3.2', '>=')) {

			foreach ($arrFields as $strTable => $arrNames) {

				if (!$objDatabase->tableExists($strTable)) continue;

				// convert file fields
				foreach ($objDatabase->listFields($strTable) as $arrField) {
					if(in_array($arrField['name'], $arrNames)){
						\Database\Updater::convertSingleField($strTable, $arrField['name']);
					}
				}
			}
		}

		return;
	}
} 