<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (C) 2005-2013 Leo Feyer
 *
 * @package   Extassets
 * @author    r.kaltofen@heimrich-hannot.de
 * @license   GNU/LGPL
 * @copyright Heimrich & Hannot GmbH
 */


/**
 * Namespace
 */
namespace ExtAssets;


/**
 * Class ExtCss
 *
 * @copyright  Heimrich & Hannot GmbH
 * @author     r.kaltofen@heimrich-hannot.de
 * @package    Devtools
 */
class ExtCss extends ExtAssets
{

	/**
	 * Singleton
	 */
	private static $instance = null;

	/**
	 * Get the singleton instance.
	 *
	 * @return \ExtCSS\ExtCSS
	 */
	public static function getInstance()
	{
		if (self::$instance == null)
		{
			self::$instance = new ExtCss();

			// remember cookie FE_PREVIEW state
			$fePreview = \Input::cookie('FE_PREVIEW');

			// set into preview mode
			\Input::setCookie('FE_PREVIEW', true);

			// request the BE_USER_AUTH login status
			static::setDesignerMode(self::$instance->getLoginStatus('BE_USER_AUTH'));

			// restore previous FE_PREVIEW state
			\Input::setCookie('FE_PREVIEW', $fePreview);
		}
		return self::$instance;
	}

	/**
	 * If is in live mode.
	 */
	protected $blnLiveMode = false;


	/**
	 * Cached be login status.
	 */
	protected $blnBeLoginStatus = null;


	/**
	 * The variables cache.
	 */
	protected $arrVariables = null;

	/**
	 * Get productive mode status.
	 */
	public static function isLiveMode()
	{
		return static::getInstance()->blnLiveMode
		? true
		: false;
	}


	/**
	 * Set productive mode.
	 */
	public static function setLiveMode($liveMode = true)
	{
		static::getInstance()->blnLiveMode = $liveMode;
	}


	/**
	 * Get productive mode status.
	 */
	public static function isDesignerMode()
	{
		return static::getInstance()->blnLiveMode
		? false
		: true;
	}


	/**
	 * Set designer mode.
	 */
	public static function setDesignerMode($designerMode = true)
	{
		static::getInstance()->blnLiveMode = !$designerMode;
	}

	
	public static function observeCssGroupFolder($groupId)
	{
		$objCss = ExtCssModel::findByPk($groupId);

		if($objCss === null || $objCss->observeFolderSRC == '') return false;
		
		$objObserveModel = \FilesModel::findByUuid($objCss->observeFolderSRC);
		
		if($objObserveModel === null || !is_dir(TL_ROOT . '/' . $objObserveModel->path)) return false;
		
		$lastUpdate = filemtime(TL_ROOT . '/' . $objObserveModel->path);
		
		// check if folder content has updated
		if($lastUpdate <= $objObserveModel->tstamp) return false;
		
		$objCssFiles = ExtCssFileModel::findBy(array('pid'), $dc->id);
		
		$objCssFilesModel = \FilesModel::findMultipleByUuids($objCssFiles->fetchEach('src'));
		
		$arrOldFileNames = $objCssFilesModel->fetchEach('name');
		
		$arrFileNames = scan(TL_ROOT . '/' . $objObserveModel->path);
		
		$arrDiff = array_diff($arrFileNames, $arrOldFileNames);
		
		// exclude bootstrap variables src
		$objVariablesModel = \FilesModel::findByUuid($objCss->bootstrapVariablesSRC);

		$variablesKey = array_search($objVariablesModel->name, $arrDiff);
		
		if($variablesKey !== false)
		{
			unset($arrDiff[$variablesKey]);
		}
		
		if(!empty($arrDiff))
		{
			// add new files
			foreach($arrDiff as $key => $name)
			{
				// create Files Model
				$objFile = new \File($objObserveModel->path . '/' . $name);
				$objFile->close();
				
				$objFileModel = new \ExtCssFileModel();
				$objFileModel->pid = $groupId;
				$objFileModel->tstamp = time();
				$objFileModel->sorting = 4294967295;
				$objFileModel->src = $objFile->getModel()->uuid;
				$objFileModel->save();
			}
		}
		
		return true;
	}


	/**
	 * Add viewport if bootstrap responsive is enabled
	 * @param PageModel $objPage
	 * @param LayoutModel $objLayout
	 * @param PageRegular $objThis
	 */
	public function hookGetPageLayout($objPage, &$objLayout, $objThis)
	{
		$objCss = ExtCssModel::findMultipleBootstrapByIds(deserialize($objLayout->extcss));

		if($objCss === null) return false;

		$blnXhtml = ($objPage->outputFormat == 'xhtml');

		$GLOBALS['TL_HEAD'][] = '<meta name="viewport" content="width=device-width,initial-scale=1.0"' . ($blnXhtml ? ' />' : '>') . "\n";
	}

	/**
	 * Update all Ext Css Files
	 * @return boolean
	 */
	public function updateExtCss()
	{
		$objCss = ExtCssModel::findAll();

		if($objCss === null) return false;

		while($objCss->next())
		{
			$combiner = new ExtCssCombiner($objCss->current(), $arrReturn);

			$arrReturn = $combiner->getUserCss();
		}
	}

	public function hookReplaceDynamicScriptTags($strBuffer)
	{
		global $objPage;

		if(!$objPage) return $strBuffer;

		$objLayout = \LayoutModel::findByPk($objPage->layout);

		if(!$objLayout) return $strBuffer;

		// the dynamic script replacement array
		$arrReplace = array();

		$this->parseExtCss($objLayout, $arrReplace);

		return $strBuffer;
	}

	protected function parseExtCss($objLayout, &$arrReplace)
	{
		$arrCss = array();

		$objCss = ExtCssModel::findMultipleByIds(deserialize($objLayout->extcss));

		if($objCss === null) return false;

		$arrReturn = array();

		while($objCss->next())
		{
			static::observeCssGroupFolder($objCss->id);
			
			$start = time();

			$combiner = new ExtCssCombiner($objCss->current(), $arrReturn);

			$arrReturn = $combiner->getUserCss();

			// HOOK: add custom css
			if (isset($GLOBALS['TL_HOOKS']['parseExtCss']) && is_array($GLOBALS['TL_HOOKS']['parseExtCss']))
			{
				foreach ($GLOBALS['TL_HOOKS']['parseExtCss'] as $callback)
				{
					$arrCss = static::importStatic($callback[0])->$callback[1]($arrCss);
				}
			}
		}

		$arrUserCss = array();


		// TODO: Refactor equal logic…
		// at first collect bootstrap to prevent overwrite of usercss
		if(isset($arrReturn[ExtCssCombiner::$bootstrapCssKey]) && is_array($arrReturn[ExtCssCombiner::$bootstrapCssKey]))
		{
			$arrHashs = array();

			foreach($arrReturn[ExtCssCombiner::$bootstrapCssKey] as $arrCss)
			{
				if(in_array($arrCss['hash'], $arrHashs)) continue;
				$arrUserCss[] = sprintf('%s|%s|%s|%s', $arrCss['src'], $arrCss['type'], $arrCss['mode'], $arrCss['hash']);
				$arrHashs[] = $arrCss['hash'];
			}
		}

		// add font awesome if checked
		if(isset($arrReturn[ExtCssCombiner::$fontAwesomeCssKey]) && is_array($arrReturn[ExtCssCombiner::$fontAwesomeCssKey]))
		{
			$arrHashs = array();

			foreach($arrReturn[ExtCssCombiner::$fontAwesomeCssKey] as $arrCss)
			{
				if(in_array($arrCss['hash'], $arrHashs)) continue;
				$arrUserCss[] = sprintf('%s|%s|%s|%s', $arrCss['src'], $arrCss['type'], $arrCss['mode'], $arrCss['hash']);
				$arrHashs[] = $arrCss['hash'];
			}
		}

		// collect all usercss
		if(isset($arrReturn[ExtCssCombiner::$userCssKey]) && is_array($arrReturn[ExtCssCombiner::$userCssKey]))
		{
			foreach($arrReturn[ExtCssCombiner::$userCssKey] as $arrCss)
			{
				$arrUserCss[] = sprintf('%s|%s|%s|%s', $arrCss['src'], $arrCss['type'], $arrCss['mode'], $arrCss['hash']);
			}
		}

		if($GLOBALS['TL_CONFIG']['bypassCache'])
		{
			$GLOBALS['TL_USER_CSS'] = array_merge(is_array($GLOBALS['TL_USER_CSS']) ? $GLOBALS['TL_USER_CSS'] : array(), $arrUserCss);
		}
		else
		{
			$GLOBALS['TL_USER_CSS'] = array_merge($arrUserCss, is_array($GLOBALS['TL_USER_CSS']) ? $GLOBALS['TL_USER_CSS'] : array());
		}
		
		

	}
}
