<?php

use ExtAssets\ExtAssets;
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

        $this->updateFrameworks();
    }

    protected function updateFrameworks()
    {
        ExtAssets::initBootstrap(true);
        ExtAssets::initFontAwesome(true);
    }
}

$objExtAssetsRunOnce = new ExtAssetsRunOnce();
$objExtAssetsRunOnce->run();