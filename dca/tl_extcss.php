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
$GLOBALS['TL_DCA']['tl_extcss'] = array(

    // Config
    'config'      => array(
        'dataContainer'    => 'Table',
        'enableVersioning' => true,
        'ctable'           => array('tl_extcss_file'),
        'sql'              => array(
            'keys' => array(
                'id' => 'primary',
            ),
        ),
    ),

    // List
    'list'        => array(
        'sorting'           => array(
            'mode'   => 1,
            'fields' => array('title'),
            'flag'   => 1,
        ),
        'label'             => array(
            'fields' => array('title'),
            'format' => '%s',
        ),
        'global_operations' => array(
            'all' => array(
                'label'      => &$GLOBALS['TL_LANG']['MSC']['all'],
                'href'       => 'act=select',
                'class'      => 'header_edit_all',
                'attributes' => 'onclick="Backend.getScrollOffset();" accesskey="e"',
            ),
        ),
        'operations'        => array(
            'edit'       => array(
                'label' => &$GLOBALS['TL_LANG']['tl_extcss']['edit'],
                'href'  => 'table=tl_extcss_file',
                'icon'  => 'edit.gif',
            ),
            'editheader' => array(
                'label' => &$GLOBALS['TL_LANG']['tl_extcss']['editheader'],
                'href'  => 'act=edit',
                'icon'  => 'header.gif',
            ),
            'copy'       => array(
                'label' => &$GLOBALS['TL_LANG']['tl_extcss']['copy'],
                'href'  => 'act=copy',
                'icon'  => 'copy.gif',
            ),
            'delete'     => array(
                'label'      => &$GLOBALS['TL_LANG']['tl_extcss']['delete'],
                'href'       => 'act=delete',
                'icon'       => 'delete.gif',
                'attributes' => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"',
            ),
            'show'       => array(
                'label' => &$GLOBALS['TL_LANG']['tl_extcss']['show'],
                'href'  => 'act=show',
                'icon'  => 'show.gif',
            ),
        ),
    ),

    // Palettes
    'palettes'    => array(
        '__selector__' => array('addBootstrap'),
        'default'      => '{title_legend},title;{config_legend},observeFolderSRC,variablesSRC,addBootstrapPrint;{font_legend},addElegantIcons;',
    ),
    // Subpalettes
    'subpalettes' => array(),
    // Fields
    'fields'      => array(
        'id'                => array(
            'sql' => "int(10) unsigned NOT NULL auto_increment",
        ),
        'tstamp'            => array(
            'sql' => "int(10) unsigned NOT NULL default '0'",
        ),
        'title'             => array(
            'label'     => &$GLOBALS['TL_LANG']['tl_extcss']['title'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => array('mandatory' => true, 'maxlength' => 64),
            'sql'       => "varchar(64) NOT NULL default ''",
        ),
        'addBootstrapPrint' => array(
            'label'     => &$GLOBALS['TL_LANG']['tl_extcss']['addBootstrapPrint'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'eval'      => array('submitOnChange' => true),
            'sql'       => "char(1) NOT NULL default ''",
        ),
        'variablesSRC'      => array(
            'label'     => &$GLOBALS['TL_LANG']['tl_extcss']['variablesSRC'],
            'exclude'   => true,
            'inputType' => 'fileTree',
            'eval'      => array('multiple'   => true,
                                 'fieldType'  => 'checkbox',
                                 'orderField' => 'variablesOrderSRC',
                                 'files'      => true,
                                 'extensions' => 'css, less',
            ),
            'sql'       => "blob NULL",
        ),
        'variablesOrderSRC' => array(
            'label' => &$GLOBALS['TL_LANG']['tl_content']['variablesOrderSRC'],
            'sql'   => "blob NULL",
        ),
        'addElegantIcons'   => array(
            'label'     => &$GLOBALS['TL_LANG']['tl_extcss']['addElegantIcons'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'default'   => true,
            'sql'       => "char(1) NOT NULL default ''",
        ),
        'observeFolderSRC'  => array(
            'label'     => &$GLOBALS['TL_LANG']['tl_extcss']['observeFolderSRC'],
            'exclude'   => true,
            'inputType' => 'fileTree',
            'eval'      => array('fieldType' => 'radio', 'filesOnly' => false, 'extensions' => 'css, less'),
            'sql'       => (version_compare(VERSION, '3.2', '<')) ? "varchar(255) NOT NULL default ''" : "binary(16) NULL",
        ),
    ),
);

\HeimrichHannot\Haste\Dca\General::addDateAddedToDca('tl_extcss');
