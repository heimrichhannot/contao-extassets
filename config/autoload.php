<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2015 Leo Feyer
 *
 * @license LGPL-3.0+
 */


/**
 * Register the namespaces
 */
ClassLoader::addNamespaces(array
(
	'ExtAssets',
	'CssSplitter',
));


/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
	// Models
	'ExtAssets\ExtCssModel'          => 'system/modules/extassets/models/ExtCssModel.php',
	'ExtAssets\ExtJsModel'           => 'system/modules/extassets/models/ExtJsModel.php',
	'ExtAssets\ExtCssFileModel'      => 'system/modules/extassets/models/ExtCssFileModel.php',
	'ExtAssets\ExtJsFileModel'       => 'system/modules/extassets/models/ExtJsFileModel.php',

	// Classes
	'ExtAssets\ExtAutomator'         => 'system/modules/extassets/classes/ExtAutomator.php',
	'ExtAssets\ExtHashFile'          => 'system/modules/extassets/classes/ExtHashFile.php',
	'ExtAssets\ExtCssCombiner'       => 'system/modules/extassets/classes/ExtCssCombiner.php',
	'ExtAssets\ExtAssets'            => 'system/modules/extassets/classes/ExtAssets.php',
	'ExtAssets\ExtJs'                => 'system/modules/extassets/classes/ExtJs.php',
	'ExtAssets\ExtAssetsUpdater'     => 'system/modules/extassets/classes/ExtAssetsUpdater.php',
	'ExtAssets\ExtCss'               => 'system/modules/extassets/classes/ExtCss.php',
	'CssSplitter\Tests\SplitterTest' => 'system/modules/extassets/classes/vendor/php-css-splitter/tests/Tests/SplitterTest.php',
	'CssSplitter\Splitter'           => 'system/modules/extassets/classes/vendor/php-css-splitter/src/Splitter.php',
));
