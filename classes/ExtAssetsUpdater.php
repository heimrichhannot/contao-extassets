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
		\Controller::loadDataContainer('tl_extcss');

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
				foreach ($objDatabase->listFields($strTable) as $arrField)
				{
					// with extassets 1.1.1 bootstrapVariablesSRC changed to variablesSRC
					if($arrField['name'] == 'bootstrapVariablesSRC')
					{
						if(!$objDatabase->fieldExists('variablesSRC', $strTable))
						{
							$sql = &$GLOBALS['TL_DCA']['tl_extcss']['fields']['variablesSRC']['sql'];
							$objDatabase->query("ALTER TABLE $strTable ADD `variablesSRC` $sql");

							$sql = &$GLOBALS['TL_DCA']['tl_extcss']['fields']['variablesOrderSRC']['sql'];
							$objDatabase->query("ALTER TABLE $strTable ADD `variablesOrderSRC` $sql");
						}

						$objGroups = $objDatabase->execute('SELECT * FROM ' . $strTable . ' WHERE bootstrapVariablesSRC IS NOT NULL AND variablesSRC IS NULL');

						while($objGroups->next())
						{
							$variables = serialize(array($objGroups->bootstrapVariablesSRC));

							$objDatabase->prepare('UPDATE ' . $strTable . ' SET variablesSRC = ?, variablesOrderSRC = ? WHERE id = ?')->execute($variables,$variables,$objGroups->id);
						}

						$objDatabase->query("ALTER TABLE $strTable DROP `bootstrapVariablesSRC`");
					}

					if(in_array($arrField['name'], $arrNames)){
						\Database\Updater::convertSingleField($strTable, $arrField['name']);
					}
				}

			}
		}

		return;
	}
} 