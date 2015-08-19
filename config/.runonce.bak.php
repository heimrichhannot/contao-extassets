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

class ExtAssetsRunOnce extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->import('Files');
    }

    public function run()
    {
        if(class_exists('\\ExtAssets\\ExtAssetsUpdater'))
        {
            \ExtAssets\ExtAssetsUpdater::run();
        }

        $this->removeObsoleteFrameworks();
    }

    protected function removeObsoleteFrameworks()
    {
        // no longer required, update via composer/components
        if(is_dir(TL_ROOT . '/assets/bootstrap'))
        {
            $objFolder = new \Folder('/assets/bootstrap');
            $objFolder->delete();
        }
        // no longer required, update via composer/components
        if(is_dir(TL_ROOT . '/assets/font-awesome'))
        {
            $objFolder = new \Folder('/assets/font-awesome');
            $objFolder->delete();
        }

    }
}

$objExtAssetsRunOnce = new ExtAssetsRunOnce();
$objExtAssetsRunOnce->run();