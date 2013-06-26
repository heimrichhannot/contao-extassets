<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (C) 2005-2012 Leo Feyer
 *
 * @package ExtAssets
 * @link    http://contao.org
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */


/**
 * Register the namespaces
 */
ClassLoader::addNamespaces(array
(
	'ExtAssets',
));


/**
 * Register the classes
*/
ClassLoader::addClasses(array
(
	/**
	 * Classes
	 */
	'ExtAssets\ExtCss'							=> 'system/modules/extassets/classes/ExtCss.php',
	'ExtAssets\ExtCssCombiner'					=> 'system/modules/extassets/classes/ExtCssCombiner.php',
	'ExtAssets\ExtHashFile'						=> 'system/modules/extassets/classes/ExtHashFile.php',
	'ExtAssets\ExtJs'							=> 'system/modules/extassets/classes/ExtJs.php',

	/**
	 * Models
	 */
	'ExtAssets\ExtCssModel' 					=> 'system/modules/extassets/models/ExtCssModel.php',
	'ExtAssets\ExtCssFileModel'    				=> 'system/modules/extassets/models/ExtCssFileModel.php',
	'ExtAssets\ExtJsModel'    					=> 'system/modules/extassets/models/ExtJsModel.php',
	'ExtAssets\ExtJsFileModel'    				=> 'system/modules/extassets/models/ExtJsFileModel.php',
));