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
		$this->loadTwitterBootstrap();
	}

	protected function loadTwitterBootstrap()
	{

		if(!is_dir(ExtAssets::getAssetFolder(ExtAssets::$twitterBootstrap, true)))
		{
			$this->Files->mkdir(ExtAssets::getAssetFolder(ExtAssets::$twitterBootstrap));
		}

		// The assets/images folder must be writable for image*()
		if (!is_writable(ExtAssets::getAssetFolder(ExtAssets::$twitterBootstrap, true)))
		{
			$this->Files->chmod(ExtAssets::getAssetFolder(ExtAssets::$twitterBootstrap), 0777);
		}

	}
}

$objExtAssetsRunOnce = new ExtAssetsRunOnce();
$objExtAssetsRunOnce->run();