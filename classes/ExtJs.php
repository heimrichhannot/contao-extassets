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
 * Namespace
 */
namespace ExtAssets;

class ExtJs extends ExtAssets
{

	/**
	 * Singleton
	 */
	private static $instance = null;

	/**
	 * Get the singleton instance.
	 *
	 * @return \ExtJs\ExtJs
	 */
	public static function getInstance()
	{
		if (self::$instance == null)
		{
			self::$instance = new ExtJs();
		}
		return self::$instance;
	}

	public function hookReplaceDynamicScriptTags($strBuffer)
	{
		global $objPage;

		if(!$objPage) return $strBuffer;

		$objLayout = \LayoutModel::findByPk($objPage->layout);

		if(!$objLayout) return $strBuffer;

		// the dynamic script replacement array
		$arrReplace = array();

		$this->parseExtJs($objLayout, $arrReplace);

		return $strBuffer;
	}

	protected function parseExtJs($objLayout, &$arrReplace)
	{
		$arrJs = array();

		$objJs = ExtJsModel::findMultipleByIds(deserialize($objLayout->extjs));

		if($objJs === null) return false;

		while($objJs->next())
		{
			$objFiles = ExtJsFileModel::findMultipleByPid($objJs->id);

			if($objFiles === null) continue;

            $js = '';

			while($objFiles->next())
			{
				$objFile = \FilesModel::findByPk($objFiles->src);
				if($objFile === null || !file_exists(TL_ROOT .'/'. $objFile->path)) continue;
				$js .= file_get_contents($objFile->path) . "\n";
			}

			// TODO: Refactor Js Generation
			$target = 'assets/js/' . $objJs->title . '.js';

			$rewrite = true;
			$version = md5($js);

			if(file_exists(TL_ROOT . '/' . $target))
			{
				$targetFile = new \File($target);
				$rewrite = !($version == $targetFile->hash);
			}

			if($rewrite)
			{
				file_put_contents(TL_ROOT . '/' . $target, $js);
			}


			$arrJs[] = "$target|none";
		}

		// HOOK: add custom css
		if (isset($GLOBALS['TL_HOOKS']['parseExtJs']) && is_array($GLOBALS['TL_HOOKS']['parseExtJs']))
		{
			foreach ($GLOBALS['TL_HOOKS']['parseExtJs'] as $callback)
			{
				$arrJs = static::importStatic($callback[0])->$callback[1]($arrJs);
			}
		}

		if($objJs->addBootstrap)
		{
			$this->addTwitterBootstrap();
		}

		// inject extjs before other plugins, otherwise bootstrap may not work
		$GLOBALS['TL_JAVASCRIPT'] = is_array($GLOBALS['TL_JAVASCRIPT']) ? array_merge($GLOBALS['TL_JAVASCRIPT'], $arrJs) : $arrJs;
		
	}

	/*
	 * TODO:
	* - install via runonce
	*/
	public function addTwitterBootstrap()
	{
		$in = BOOTSTRAPJSDIR . 'bootstrap.js';

		if(!file_exists(TL_ROOT . '/' . $in)) return false;

		// index 0 = jQuery
		array_insert($GLOBALS['TL_JAVASCRIPT'], 1, array('bootstrap' => "$in|none"));
	}

}