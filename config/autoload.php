<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2013 Leo Feyer
 *
 * @package Extassets
 * @link    https://contao.org
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
	// Models
	'ExtAssets\ExtJsFileModel'  => 'system/modules/extassets/models/ExtJsFileModel.php',
	'ExtAssets\ExtCssFileModel' => 'system/modules/extassets/models/ExtCssFileModel.php',
	'ExtAssets\ExtCssModel'     => 'system/modules/extassets/models/ExtCssModel.php',
	'ExtAssets\ExtJsModel'      => 'system/modules/extassets/models/ExtJsModel.php',

	// Classes
	'ExtAssets\ExtJs'           => 'system/modules/extassets/classes/ExtJs.php',
	'ExtAssets\ExtAutomator'    => 'system/modules/extassets/classes/ExtAutomator.php',
	'ExtAssets\ExtHashFile'     => 'system/modules/extassets/classes/ExtHashFile.php',
	'InputTest'                 => 'system/modules/extassets/classes/vendor/lessphp/tests/InputTest.php',
	'ApiTest'                   => 'system/modules/extassets/classes/vendor/lessphp/tests/ApiTest.php',
	'ExtAssets\ExtCss'          => 'system/modules/extassets/classes/ExtCss.php',
	'ExtAssets\ExtCssCombiner'  => 'system/modules/extassets/classes/ExtCssCombiner.php',
	'ExtAssets\ExtAssets'       => 'system/modules/extassets/classes/ExtAssets.php',
));
