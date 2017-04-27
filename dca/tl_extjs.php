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
 * Table tl_extjs
 */
$GLOBALS['TL_DCA']['tl_extjs'] = array(

    // Config
    'config'   => array(
        'dataContainer'    => 'Table',
        'enableVersioning' => true,
        'ctable'           => array('tl_extjs_file'),
        'sql'              => array(
            'keys' => array(
                'id' => 'primary',
            ),
        ),
    ),

    // List
    'list'     => array(
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
            'edit'   => array(
                'label' => &$GLOBALS['TL_LANG']['tl_extjs']['edit'],
                'href'  => 'table=tl_extjs_file',
                'icon'  => 'edit.gif',
            ),
            'copy'   => array(
                'label' => &$GLOBALS['TL_LANG']['tl_extjs']['copy'],
                'href'  => 'act=copy',
                'icon'  => 'copy.gif',
            ),
            'delete' => array(
                'label'      => &$GLOBALS['TL_LANG']['tl_extjs']['delete'],
                'href'       => 'act=delete',
                'icon'       => 'delete.gif',
                'attributes' => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"',
            ),
            'show'   => array(
                'label' => &$GLOBALS['TL_LANG']['tl_extjs']['show'],
                'href'  => 'act=show',
                'icon'  => 'show.gif',
            ),
        ),
    ),

    // Palettes
    'palettes' => array(
        'default' => '{title_legend},title;{bootstrap_legend},addBootstrap;',
    ),
    // Fields
    'fields'   => array(
        'id'           => array(
            'sql' => "int(10) unsigned NOT NULL auto_increment",
        ),
        'tstamp'       => array(
            'sql' => "int(10) unsigned NOT NULL default '0'",
        ),
        'title'        => array(
            'label'     => &$GLOBALS['TL_LANG']['tl_extjs']['title'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => array('mandatory' => true, 'maxlength' => 64),
            'sql'       => "varchar(64) NOT NULL default ''",
        ),
        'addBootstrap' => array(
            'label'     => &$GLOBALS['TL_LANG']['tl_extjs']['addBootstrap'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'default'   => true,
            'sql'       => "char(1) NOT NULL default ''",
        ),
    ),
);
