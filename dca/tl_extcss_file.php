<?php

use ExtAssets\ExtCssModel;

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
$this->loadLanguageFile('tl_files');

/**
 * Table tl_extcss_file
 */
$GLOBALS['TL_DCA']['tl_extcss_file'] = array(

    // Config
    'config'   => array(
        'dataContainer'    => 'Table',
        'ptable'           => 'tl_extcss',
        'enableVersioning' => true,
        'sql'              => array(
            'keys' => array(
                'id'  => 'primary',
                'pid' => 'index',
            ),
        ),
        'onload_callback'  => array(
            array(
                'tl_extcss_file',
                'observeFolder',
            ),
        ),
    ),

    // List
    'list'     => array(
        'sorting'           => array(
            'mode'                  => 4,
            'fields'                => array(
                'sorting',
            ),
            'headerFields'          => array(
                'title',
                'tstamp',
            ),
            'panelLayout'           => 'filter;sort,search,limit',
            'child_record_callback' => array(
                'tl_extcss_file',
                'listCSSFiles',
            ),
            'child_record_class'    => 'no_padding',
            'disableGrouping'       => true,
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
                'label' => &$GLOBALS['TL_LANG']['tl_extcss_file']['edit'],
                'href'  => 'act=edit',
                'icon'  => 'edit.gif',
            ),
            'copy'   => array(
                'label' => &$GLOBALS['TL_LANG']['tl_extcss_file']['copy'],
                'href'  => 'act=copy',
                'icon'  => 'copy.gif',
            ),
            'cut'    => array(
                'label'      => &$GLOBALS['TL_LANG']['tl_extcss_file']['cut'],
                'href'       => 'act=paste&amp;mode=cut',
                'icon'       => 'cut.gif',
                'attributes' => 'onclick="Backend.getScrollOffset();"',
            ),
            'delete' => array(
                'label'      => &$GLOBALS['TL_LANG']['tl_extcss_file']['delete'],
                'href'       => 'act=delete',
                'icon'       => 'delete.gif',
                'attributes' => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"',
            ),
            'show'   => array(
                'label' => &$GLOBALS['TL_LANG']['tl_extcss_file']['show'],
                'href'  => 'act=show',
                'icon'  => 'show.gif',
            ),
        ),
    ),

    // Palettes
    'palettes' => array(
        'default' => '{src_legend},src;',
    ),

    // Fields
    'fields'   => array(
        'id'      => array(
            'sql' => "int(10) unsigned NOT NULL auto_increment",
        ),
        'pid'     => array(
            'foreignKey' => 'tl_news_archive.title',
            'sql'        => "int(10) unsigned NOT NULL default '0'",
            'relation'   => array(
                'type' => 'belongsTo',
                'load' => 'eager',
            ),
        ),
        'sorting' => array(
            'sorting' => true,
            'flag'    => 2,
            'sql'     => "int(10) unsigned NOT NULL default '0'",
        ),
        'tstamp'  => array(
            'sql' => "int(10) unsigned NOT NULL default '0'",
        ),
        'src'     => array(
            'label'     => &$GLOBALS['TL_LANG']['tl_extcss_file']['src'],
            'exclude'   => true,
            'inputType' => 'fileTree',
            'eval'      => array(
                'fieldType'  => 'radio',
                'filesOnly'  => true,
                'mandatory'  => true,
                'extensions' => 'css, less',
            ),
            'sql'       => (version_compare(VERSION, '3.2', '<')) ? "varchar(255) NOT NULL default ''" : "binary(16) NULL",
        ),
    ),
);

class tl_extcss_file extends Backend
{

    public function __construct()
    {
        parent::__construct();
        $this->import('BackendUser', 'User');
    }

    public function observeFolder($dc)
    {
        $objModel = ExtCssModel::findByPk($dc->id);

        if ($objModel === null)
        {
            return false;
        }

        $dca = &$GLOBALS['TL_DCA']['tl_extcss_file'];

        \ExtAssets\ExtCss::observeCssGroupFolder($dc->id);
    }

    /**
     * Add the type of input field
     *
     * @param
     *            array
     *
     * @return string
     */
    public function listCSSFiles($arrRow)
    {
        $objFiles = FilesModel::findById($arrRow['src']);

        // Return if there is no result
        if ($objFiles === null)
        {
            return '';
        }

        // Show files and folders
        if ($objFiles->type == 'folder')
        {
            $thumbnail = $this->generateImage('folderC.gif');
        }
        else
        {
            $objFile   = new \File($objFiles->path, true);
            $thumbnail = $this->generateImage($objFile->icon);
        }

        return '<div class="tl_content_left" style="line-height:21px"><div style="float:left; margin-right:2px;">' . $thumbnail . '</div>' . $objFiles->name
               . '<span style="color:#b3b3b3;padding-left:3px">[' . str_replace($objFiles->name, '', $objFiles->path) . ']</span></div>' . "\n";
    }
}
