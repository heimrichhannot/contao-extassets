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

use Contao\File;

use Template;
use FrontendTemplate;
use ExtCssModel;
use ExtCssFileModel;

require TL_ROOT . "/system/modules/extassets/classes/vendor/lessphp/lessc.inc.php";

/**
 * Class ExtCss
 *
 * @copyright  Heimrich & Hannot GmbH
 * @author     r.kaltofen@heimrich-hannot.de
 * @package    Devtools
 */
class ExtCss extends \Frontend
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

	protected function __construct()
	{
		parent::__construct();
	}

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

		while($objCss->next())
		{
			$less = false;

			$objFiles = ExtCssFileModel::findMultipleByPid($objCss->id);

			if($objFiles === null) continue;

			if($objCss->addBootstrap)
			{
				$less = true;

				$variables = "assets/bootstrap/less/variables.less";

				if($this->fileExists($variables))
				{
					$css .= file_get_contents(TL_ROOT .'/' . $variables) . "\n";
				}

				// overwrite bootstrap variables by custom
				if($objCss->bootstrapVariablesSRC)
				{
					$objFile = \FilesModel::findByPk($objCss->bootstrapVariablesSRC);

					if($this->fileExists($objFile->path))
					{
						// store variables in new file for bootstrap import
						$css .= $this->getFileContent($objFile->path) . "\n";

						$version = md5($css);

						$newVariables = 'variables-' . $objCss->title . '.less';
						$newVariablesSRC = 'assets/bootstrap/less/' . $newVariables;

						$this->filePutContent($newVariablesSRC, $css);
					}
				}

				$mixins = "assets/bootstrap/less/mixins.less";

				if($this->fileExists($mixins))
				{
					$css .= $this->getFileContent($mixins) . "\n";
				}
			}

			while($objFiles->next())
			{
				$objFile = \FilesModel::findByPk($objFiles->src);

				if(!$this->fileExists($objFile->path)) continue;

				$css .= $this->getFileContent($objFile->path) . "\n";

				if($objFile->extension == 'less') $less = true;
			}

			// TODO: Refactor Css Generation
			$target = 'assets/css/' . $objCss->title . '.css';

			if($less)
			{
				$less = new \lessc();
				$css = $less->compile($css, $objCss->title);
			}

			$rewrite = true;
			$version = md5($css);

			if($this->fileExists($target))
			{
				$targetFile = new \File($target);
				$rewrite = !($version == $targetFile->hash);
			}

			if($rewrite)
			{
				$this->filePutContent($target, $css);
			}

			// TODO: add css minimizer option for extcss group
			$mode = $GLOBALS['TL_CONFIG']['bypassCache'] ? 'none' : 'static';

			$arrCss[] = "$target|screen|$mode|$version";
		}

		// HOOK: add custom css
		if (isset($GLOBALS['TL_HOOKS']['parseExtCss']) && is_array($GLOBALS['TL_HOOKS']['parseExtCss']))
		{
			foreach ($GLOBALS['TL_HOOKS']['parseExtCss'] as $callback)
			{
				$arrCss = static::importStatic($callback[0])->$callback[1]($arrCss);
			}
		}

		if($objCss->addBootstrap)
		{
			$arrCss = $this->addTwitterBootstrap($arrCss, $objCss, $newVariables);
		}

		$GLOBALS['TL_USER_CSS']	= (is_array($GLOBALS['TL_USER_CSS']) ? $GLOBALS['TL_USER_CSS'] : array()) + $arrCss;
	}

	/*
	 * TODO:
	* - install via runonce
	* - refactor css compiling in custom method (together with parseExtCss)
	*/
	public function addTwitterBootstrap($arrCss, $objCss, $variablesSRC='')
	{
		if($objCss->bootstrapResponsive)
		{
			$in = "assets/bootstrap/less/responsive.less";
			$out = "assets/css/bootstrap-responsive.css";

			$arrDevices = deserialize($objCss->bootstrapResponsiveDevices);

			$inCss = $this->getFileContent($in);

			if($variablesSRC)
			{
				$inCss = str_replace('variables.less', $variablesSRC, $inCss);
			}

			if(is_array($arrDevices) && !empty($arrDevices))
			{
				$arrOptions = $GLOBALS['TL_DCA']['tl_extcss']['fields']['bootstrapResponsiveDevices']['options'];

				$arrRemove = array_diff($arrOptions, $arrDevices);

				if(is_array($arrRemove) && !empty($arrRemove))
				{
					foreach($arrRemove as $device)
					{
						switch($device)
						{
							case 'large':
								$inCss = str_replace('@import "responsive-1200px-min.less";', '//@import "responsive-1200px-min.less";', $inCss);
							break;
							case 'tablet':
								$inCss = str_replace('@import "responsive-768px-979px.less";', '//@import "responsive-768px-979px.less";', $inCss);
							break;
							case 'phone':
								$inCss = str_replace('@import "responsive-767px-max.less";', '//@import "responsive-767px-max.less";', $inCss);
							break;
						}
					}

					$newIn = "assets/bootstrap/less/bootstrap-responsive-" . $objCss->title .".less";

					if($this->filePutContent($newIn, $inCss) !== false)
					{
						$in = $newIn;
					}
				}
			}

			$arrCss = $this->addVendorAsset($arrCss, $in ,$out);
		}

		$in = "assets/bootstrap/less/bootstrap.less";
		$out = "assets/css/bootstrap.css";


		// overwrite variables with custom variables
		if($variablesSRC)
		{
			$inCss = $this->getFileContent($in);
			$inCss = str_replace('variables.less', $variablesSRC, $inCss);
			$newIn = "assets/bootstrap/less/bootstrap-" . $objCss->title .".less";

			if($this->filePutContent($newIn, $inCss) !== false)
			{
				$in = $newIn;
			}
		}


		$arrCss = $this->addVendorAsset($arrCss, $in ,$out);

		return $arrCss;
	}

	protected function addVendorAsset($arrCss, $in, $out)
	{
		if(!$this->fileExists($in)) return $arrCss;

		$less = \lessc::ccompile(TL_ROOT . '/' . $in, TL_ROOT . '/' . $out);

		$objOut = new \File($out);

		// TODO: add css minimizer option for extcss group
		$mode = $GLOBALS['TL_CONFIG']['bypassCache'] ? 'none' : 'static';

		array_unshift($arrCss, "$out|screen|$mode|$objOut->hash");

		return $arrCss;
	}

	protected function fileExists($src)
	{
		return file_exists(TL_ROOT . '/' . $src);
	}

	protected function getFileContent($src)
	{
		return file_get_contents(TL_ROOT . '/' . $src);
	}

	protected function filePutContent($out, $content)
	{
		return file_put_contents(TL_ROOT . '/' . $out, $content);
	}
}
