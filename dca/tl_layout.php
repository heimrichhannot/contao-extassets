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

$dc = &$GLOBALS['TL_DCA']['tl_layout'];

/**
 * fields
 */

$dc['fields']['extcss'] = array
(
		'label'                   => &$GLOBALS['TL_LANG']['tl_layout']['extcss'],
		'exclude'                 => true,
		'inputType'               => 'checkboxWizard',
		'foreignKey'              => 'tl_extcss.title',
		'eval'                    => array('multiple'=>true),
		'sql'                     => "varchar(255) NOT NULL default ''",
);

$dc['fields']['extjs'] = array
(
		'label'                   => &$GLOBALS['TL_LANG']['tl_layout']['extjs'],
		'exclude'                 => true,
		'inputType'               => 'checkboxWizard',
		'foreignKey'              => 'tl_extjs.title',
		'eval'                    => array('multiple'=>true),
		'sql'                     => "varchar(255) NOT NULL default ''",
);

/**
 * palettes
 */
$GLOBALS['TL_DCA']['tl_layout']['palettes']['default'] 	= str_replace('stylesheet','stylesheet,extcss',$GLOBALS['TL_DCA']['tl_layout']['palettes']['default']);
$GLOBALS['TL_DCA']['tl_layout']['palettes']['default'] 	= str_replace('script','script,extjs',$GLOBALS['TL_DCA']['tl_layout']['palettes']['default']);
