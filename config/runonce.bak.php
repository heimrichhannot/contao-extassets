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
        $this->updateDB();
        $this->updateFrameworks();
    }

    protected function updateDB()
    {
        \Database::getInstance()->prepare('UPDATE `tl_extcss_file` SET  `sorting` = 2147483648 WHERE sorting = 4294967295');
    }

    protected function updateFrameworks()
    {
        ExtAssets::initBootstrap(true);
        ExtAssets::initFontAwesome(true);
    }
}

$objExtAssetsRunOnce = new ExtAssetsRunOnce();
$objExtAssetsRunOnce->run();