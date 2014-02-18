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
 * Table tl_extcss
 */
$GLOBALS['TL_DCA']['tl_extcss'] = array
(

		// Config
		'config' => array
		(
				'dataContainer'               => 'Table',
				'enableVersioning'            => true,
				'ctable'                      => array('tl_extcss_file'),
				'sql' => array
				(
						'keys' => array
						(
								'id' => 'primary'
						)
				)
		),

		// List
		'list' => array
		(
				'sorting' => array
				(
						'mode'                    => 1,
						'fields'                  => array('title'),
						'flag'                    => 1
				),
				'label' => array
				(
						'fields'                  => array('title'),
						'format'                  => '%s'
				),
				'global_operations' => array
				(
						'all' => array
						(
								'label'               => &$GLOBALS['TL_LANG']['MSC']['all'],
								'href'                => 'act=select',
								'class'               => 'header_edit_all',
								'attributes'          => 'onclick="Backend.getScrollOffset();" accesskey="e"'
						)
				),
				'operations' => array
				(
					'edit' => array
					(
						'label'               => &$GLOBALS['TL_LANG']['tl_extcss']['edit'],
						'href'                => 'table=tl_extcss_file',
						'icon'                => 'edit.gif'
					),
					'copy' => array
					(
						'label'               => &$GLOBALS['TL_LANG']['tl_extcss']['copy'],
						'href'                => 'act=copy',
						'icon'                => 'copy.gif'
					),
					'delete' => array
					(
						'label'               => &$GLOBALS['TL_LANG']['tl_extcss']['delete'],
						'href'                => 'act=delete',
						'icon'                => 'delete.gif',
						'attributes'          => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"'
					),
					'show' => array
					(
						'label'               => &$GLOBALS['TL_LANG']['tl_extcss']['show'],
						'href'                => 'act=show',
						'icon'                => 'show.gif'
					)
				)
			),

			// Palettes
			'palettes' => array
			(
				'__selector__'				=> array('addBootstrap'),
				'default'                   => '{title_legend},title;{bootstrap_legend},addBootstrap;{font_awesome_legend},addFontAwesome;'
			),
			// Subpalettes
			'subpalettes' => array
			(
				'addBootstrap'				=> 'bootstrapVariablesSRC',
			),
			// Fields
			'fields' => array
			(
				'id' => array
				(
					'sql'                     => "int(10) unsigned NOT NULL auto_increment"
				),
				'tstamp' => array
				(
					'sql'                     => "int(10) unsigned NOT NULL default '0'"
				),
				'title' => array
				(
					'label'                   => &$GLOBALS['TL_LANG']['tl_extcss']['title'],
					'exclude'                 => true,
					'inputType'               => 'text',
					'eval'                    => array('mandatory'=>true, 'maxlength'=>255),
					'sql'                     => "varchar(255) NOT NULL default ''"
				),
				'addBootstrap' => array(
					'label'                   => &$GLOBALS['TL_LANG']['tl_extcss']['addBootstrap'],
					'exclude'                 => true,
					'inputType'               => 'checkbox',
					'default'				  => true,
					'eval'                    => array('submitOnChange'=>true),
					'sql'					  => "char(1) NOT NULL default ''",
				),
				'bootstrapVariablesSRC'	=> array(
					'label'                   => &$GLOBALS['TL_LANG']['tl_extcss']['bootstrapVariablesSRC'],
					'exclude'                 => true,
					'inputType'               => 'fileTree',
					'eval'                    => array('fieldType'=>'radio', 'filesOnly'=>true, 'extensions'=>'css, less'),
					'sql'                     => "binary(16) NULL"
				),
				'addFontAwesome' => array(
					'label'                   => &$GLOBALS['TL_LANG']['tl_extcss']['addFontAwesome'],
					'exclude'                 => true,
					'inputType'               => 'checkbox',
					'default'                 => true,
					'sql'                     => "char(1) NOT NULL default ''",
				),
			)
);
