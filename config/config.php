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
 * Custom Variables
 */
define('CSSDIR', 'assets/css/');

$componentsDir = 'assets/components';

if (version_compare(VERSION . '.' . BUILD, '4.0.0', '>='))
{
    $componentsDir = 'assets';
}

define('BOOTSTRAPDIR', $componentsDir . '/bootstrap/');
define('BOOTSTRAPLESSDIR', $componentsDir . '/bootstrap/less/');
define('BOOTSTRAPLESSCUSTOMDIR', $componentsDir . '/bootstrap/less/custom/');
define('BOOTSTRAPJSDIR', $componentsDir . '/bootstrap/js/');

define('FONTAWESOMEDIR', $componentsDir . '/font-awesome/');
define('FONTAWESOMECSSDIR', FONTAWESOMEDIR . 'css/');
define('FONTAWESOMELESSDIR', FONTAWESOMEDIR . 'less/');
define('FONTAWESOMELESSCUSTOMDIR', FONTAWESOMEDIR . 'less/custom/');
define('FONTAWESOMEFONTDIR', FONTAWESOMEDIR . 'fonts/');

define('ELEGANTICONSDIR', $componentsDir . '/elegant-icons/');
define('ELEGANTICONSCSSDIR', ELEGANTICONSDIR . 'css/');
define('ELEGANTICONSLESSDIR', ELEGANTICONSDIR . 'less/');
define('ELEGANTICONSFONTDIR', ELEGANTICONSDIR . 'fonts/');

define('LESSCSSCACHEDIR', 'assets/css/lesscache/');

/**
 * BACK END MODULES
 *
 * Back end modules are stored in a global array called "BE_MOD". You can add
 * your own modules by adding them to the array.
 */

$GLOBALS['BE_MOD']['design']['extcss'] = [
    'tables' => ['tl_extcss', 'tl_extcss_file', 'tl_files'],
    'icon'   => 'system/modules/extassets/assets/extcss/icon.png',
];

$GLOBALS['BE_MOD']['design']['extjs'] = [
    'tables' => ['tl_extjs', 'tl_extjs_file', 'tl_files'],
    'icon'   => 'system/modules/extassets/assets/extjs/icon.png',
];

/**
 * Mime types
 */
$GLOBALS['TL_MIME']['less'] = ['text/css', 'iconCSS.gif'];


/**
 * Hooks
 */
$GLOBALS['TL_HOOKS']['replaceDynamicScriptTags'][] = ['ExtAssets\ExtCss', 'hookReplaceDynamicScriptTags'];
$GLOBALS['TL_HOOKS']['replaceDynamicScriptTags'][] = ['ExtAssets\ExtJs', 'hookReplaceDynamicScriptTags'];
$GLOBALS['TL_HOOKS']['getPageLayout'][]            = ['ExtAssets\ExtCss', 'hookGetPageLayout'];

/**
 * Models
 */
$GLOBALS['TL_MODELS']['tl_extcss']      = 'ExtAssets\ExtCssModel';
$GLOBALS['TL_MODELS']['tl_extcss_file'] = 'ExtAssets\ExtCssFileModel';
$GLOBALS['TL_MODELS']['tl_extjs']       = 'ExtAssets\ExtJsModel';
$GLOBALS['TL_MODELS']['tl_extjs_file']  = 'ExtAssets\ExtJsFileModel';

/**
 * PurgeData
 */
$GLOBALS['TL_PURGE']['folders']['less'] = [
    'affected' => [BOOTSTRAPLESSCUSTOMDIR, FONTAWESOMELESSCUSTOMDIR, LESSCSSCACHEDIR, 'assets/css/'],
    'callback' => ['ExtAssets\ExtAutomator', 'purgeLessCache'],
];

/**
 * FRONT END MODULES
 *
 * Front end modules are stored in a global array called "FE_MOD". You can add
 * your own modules by adding them to the array.
 *
 * $GLOBALS['FE_MOD'] = array
 * (
 *    'group_1' => array
 *    (
 *       'module_1' => 'ModuleClass1',
 *       'module_2' => 'ModuleClass2'
 *    )
 * );
 *
 * The keys (like "module_1") are the module names, which are e.g. stored in the
 * database and used to find the corresponding translations. The values (like
 * "ModuleClass1") are the names of the classes, which will be loaded when the
 * module is rendered. The class "ModuleClass1" has to be stored in a file
 * named "ModuleClass1.php" in your module folder.
 */


/**
 * CONTENT ELEMENTS
 *
 * Content elements are stored in a global array called "TL_CTE". You can add
 * your own content elements by adding them to the array.
 *
 * $GLOBALS['TL_CTE'] = array
 * (
 *    'group_1' => array
 *    (
 *       'cte_1' => 'ContentClass1',
 *       'cte_2' => 'ContentClass2'
 *    )
 * );
 *
 * The keys (like "cte_1") are the element names, which are e.g. stored in the
 * database and used to find the corresponding translations. The values (like
 * "ContentClass1") are the names of the classes, which will be loaded when the
 * element is rendered. The class "ContentClass1" has to be stored in a file
 * named "ContentClass1.php" in your module folder.
 */


/**
 * BACK END FORM FIELDS
 *
 * Back end form fields are stored in a global array called "BE_FFL". You can
 * add your own form fields by adding them to the array.
 *
 * $GLOBALS['BE_FFL'] = array
 * (
 *    'input'  => 'FieldClass1',
 *    'select' => 'FieldClass2'
 * );
 *
 * The keys (like "input") are the field names, which are e.g. stored in the
 * database and used to find the corresponding translations. The values (like
 * "FieldClass1") are the names of the classes, which will be loaded when the
 * field is rendered. The class "FieldClass1" has to be stored in a file named
 * "FieldClass1.php" in your module folder.
 */


/**
 * FRONT END FORM FIELDS
 *
 * Front end form fields are stored in a global array called "TL_FFL". You can
 * add your own form fields by adding them to the array.
 *
 * $GLOBALS['TL_FFL'] = array
 * (
 *    'input'  => 'FieldClass1',
 *    'select' => 'FieldClass2'
 * );
 *
 * The keys (like "input") are the field names, which are e.g. stored in the
 * database and used to find the corresponding translations. The values (like
 * "FieldClass1") are the names of the classes, which will be loaded when the
 * field is rendered. The class "FieldClass1" has to be stored in a file named
 * "FieldClass1.php" in your module folder.
 */


/**
 * PAGE TYPES
 *
 * Page types are stored in a global array called "TL_PTY". You can add your own
 * page types by adding them to the array.
 *
 * $GLOBALS['TL_PTY'] = array
 * (
 *    'type_1' => 'PageType1',
 *    'type_2' => 'PageType2'
 * );
 *
 * The keys (like "type_1") are the field names, which are e.g. stored in the
 * database and used to find the corresponding translations. The values (like
 * "PageType1") are the names of the classes, which will be loaded when the
 * page is rendered. The class "PageType1" has to be stored in a file named
 * "PageType1.php" in your module folder.
 */


/**
 * MODEL MAPPINGS
 *
 * Model names are usually built from the table names, e.g. "tl_user_group"
 * becomes "UserGroupModel". There might be situations, however, where you need
 * to specify a custom mapping, e.g. when you are using nested namespaces.
 *
 * $GLOBALS['TL_MODELS'] = array
 * (
 *    'tl_user'       => 'Vendor\Application\UserModel',
 *    'tl_user_group' => 'Vendor\Application\UserGroupModel'
 * );
 *
 * You can register your mappings in the config.php file of your extension.
 */


/**
 * MAINTENANCE MODULES
 *
 * Maintenance modules are stored in a global array called "TL_MAINTENANCE". You
 * can add your own maintenance modules by adding them to the array.
 *
 * $GLOBALS['TL_MAINTENANCE'] = array
 * (
 *    'ClearCache',
 *    'RebuildSearchIndex'
 * );
 *
 * Take a look at the system/modules/core/classes/PurgeData.php file to see how
 * maintenance modules are set up. The class "ClearCache" has to be stored in a
 * file named "ClearCache.php" in your module folder.
 */


/**
 * PURGE JOBS
 *
 * Purge jobs are stored in a global array called "TL_PURGE". You can add your
 * own purge jobs by adding them to the array.
 *
 * $GLOBALS['TL_PURGE'] = array
 * (
 *    'job_1' => array
 *    (
 *       'tables' => array
 *       (
 *          'index' => array
 *          (
 *             'callback' => array('Automator', 'purgeSearchTables'),
 *             'affected' => array('tl_search', 'tl_search_index')
 *          ),
 *       )
 *   );
 *
 * There are three categories: "tables" stores jobs which truncate database
 * tables, "folders" stores jobs which purge folders and "custom" stores jobs
 * which only trigger a callback function.
 */


/**
 * CRON JOBS
 *
 * Cron jobs are stored in a global array called "TL_CRON". You can add your own
 * cron jobs by adding them to the array.
 *
 * $GLOBALS['TL_CRON'] = array
 * (
 *    'monthly' => array
 *    (
 *       array('Automator', 'purgeImageCache')
 *    ),
 *    'weekly'   => array(),
 *    'daily'    => array(),
 *    'hourly'   => array(),
 *    'minutely' => array()
 * );
 *
 * Note that this is rather a command scheduler than a cron job, which does not
 * guarantee an exact execution time. You can replace the command scheduler with
 * a real cron job though.
 */


/**
 * HOOKS
 *
 * Hooks are stored in a global array called "TL_HOOKS". You can register your
 * own functions by adding them to the array.
 *
 * $GLOBALS['TL_HOOKS'] = array
 * (
 *    'hook_1' => array
 *    (
 *       array('MyClass', 'myPostLogin'),
 *       array('MyClass', 'myPostLogout')
 *    )
 * );
 *
 * Hooks allow you to add functionality to the core without having to modify the
 * source code by registering callback functions to be executed on a particular
 * event. For more information see https://contao.org/manual.html.
 */


/**
 * AUTO ITEMS
 *
 * Auto items are stored in a global array called "TL_AUTO_ITEM". You can
 * register your own auto items by adding them to the array.
 *
 * $GLOBALS['TL_AUTO_ITEM'] = array('items', 'events');
 *
 * Auto items are keywords, which are used as parameters by certain modules.
 * When rebuilding the search index URLs, Contao needs to know about these
 * keywords so it can handle them properly.
 */
