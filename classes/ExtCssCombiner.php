<?php

namespace ExtAssets;

use Contao\File;
use Contao\Environment;

require_once TL_ROOT . "/system/modules/extassets/classes/vendor/lessphp/lessc.inc.php";
require_once TL_ROOT . "/system/modules/extassets/classes/vendor/php-css-splitter/src/Splitter.php";

class ExtCssCombiner extends \Frontend
{

	protected $rewrite = false;

	protected $rewriteBootstrap = false;

	protected $arrData = array();

	protected $arrCss = array();

	protected $arrReturn = array();

	protected $mode = 'static';

	protected $variablesSrc;

	public static $userCssKey = 'usercss';

	public static $bootstrapCssKey = 'bootstrap';

	public static $bootstrapPrintCssKey = 'bootstrap-print';

	public static $bootstrapResponsiveCssKey = 'bootstrap-responsive';

	public static $fontAwesomeCssKey = 'font-awesome';

	protected $objUserCssFile; // Target File of combined less output

	protected $uriRoot;

	public $debug = false;

	protected $objLess;

	public function __construct(ExtCssModel $objCss, $arrReturn = array())
	{
		parent::__construct();
		$this->loadDataContainer('tl_extcss');
		$this->arrData = $objCss->row();

		$this->mode = $GLOBALS['TL_CONFIG']['bypassCache'] ? 'none' : 'static';

		$this->arrReturn = $arrReturn;

		$this->variablesSrc = 'variables-' . $this->title . '.less';

		$this->objUserCssFile = new \File($this->getSrc($this->title . '.css'));;

		// rewrite if group css is empty // created recently
		if ($this->objUserCssFile->size == 0) {
			$this->rewrite          = true;
			$this->rewriteBootstrap = true;
		}

		$this->uriRoot = (TL_ASSETS_URL ? TL_ASSETS_URL : Environment::get('url')) . '/assets/css/';

		$this->objLess = new \lessc();

		if ($this->debug) {
			$this->rewrite          = true;
			$this->rewriteBootstrap = true;
		}

		if ($this->addBootstrap) {
			$this->addBootstrapVariables();
			$this->addFontAwesomeCore();
			$this->addBootstrapMixins();
			$this->addBootstrapAlerts();
			$this->addBootstrap();
			$this->addBootstrapUtilities();
			$this->addBootstrapType();
		}

		if ($this->addFontAwesome) {
			$this->addFontAwesomeVariables();
			$this->addFontAwesomeIcons();
			$this->addFontAwesomeMixins();
			$this->addFontAwesome();
		}

		$this->addCustomLessFiles();

		$this->addCssFiles();
	}

	public function getUserCss()
	{
		$arrReturn = $this->arrReturn;

		$strCss = $this->objUserCssFile->getContent();

		if (is_array($this->arrCss) && !empty($this->arrCss) && ($this->rewrite || $this->rewriteBootstrap)) {
			$strCss = $this->objLess->compile(implode("\n", $this->arrCss));
			$this->objUserCssFile->write($strCss);
		}

		$splitter = new \CssSplitter\Splitter();
		$count    = $splitter->countSelectors($strCss);

		// IE 6 - 9 has a limit of 4096 selectors
		if ($count > 0) {
			$parts = ceil($count / 4095);
			for ($i = 1; $i <= $parts; $i++) {
				$objFile = new \File("assets/css/$this->title-part-{$i}.css");
				$objFile->write($splitter->split($strCss, $i));
				$objFile->close();

				$arrReturn[self::$userCssKey][] = array
				(
					'src'  => $objFile->value,
					'type' => 'all', // 'all' is required by print media css
					'mode' => '', // mustn't be static, otherwise contao will aggregate the files again (splitting not working)
					'hash' => version_compare(VERSION, '3.4', '>=') ? $this->objUserCssFile->mtime : $this->objUserCssFile->hash,
				);
			}

		} else {
			$arrReturn[self::$userCssKey][] = array
			(
				'src'  => $this->objUserCssFile->value,
				'type' => 'all', // 'all' is required by print media css
				'mode' => $this->mode,
				'hash' => version_compare(VERSION, '3.4', '>=') ? $this->objUserCssFile->mtime : $this->objUserCssFile->hash,
			);
		}

		return $arrReturn;
	}


	protected function addCssFiles()
	{
		$objFiles = ExtCssFileModel::findMultipleByPid($this->id);

		if ($objFiles === null) return false;

		while ($objFiles->next()) {
			$objFileModel = \FilesModel::findByPk($objFiles->src);

			if ($objFileModel === null) continue;

			if (!file_exists(TL_ROOT . '/' . $objFileModel->path)) continue;

			$objFile = new \File($objFileModel->path);

			if ($objFile->size == 0) continue;

			if ($this->isFileUpdated($objFile, $this->objUserCssFile) || $this->rewrite) {
				$this->rewrite = true;
			}

			$this->arrCss[$objFileModel->id] = $objFile->getContent();
		}
	}

	protected function addBootstrap()
	{
		$objFile   = new \File($this->getBootstrapSrc('bootstrap.less'));
		$objTarget = new \File($this->getBootstrapCustomSrc('bootstrap-' . $this->title . '.less'));
		$objOut    = new \File($this->getSrc('bootstrap-' . $this->title . '.css'), true);

		//$this->objLess->addImportDir($objFile->dirname);

		if ($this->rewriteBootstrap || !$objOut->exists()) {
			$strCss = $objFile->getContent();

			$strCss = str_replace('@import "', '@import "../', $strCss);


			if ($this->bootstrapVariablesSRC) {
				$strCss = str_replace('../variables.less', $this->variablesSrc, $strCss);
			}

			// remove pr
			if (!$this->addBootstrapPrint) {
				$strCss = str_replace('@import "../print.less";', '//@import "../print.less";', $strCss);
			}

			$objTarget->write($strCss);
			$objTarget->close();

			$lessc  = new \lessc();
			$strCss = $lessc->compileFile(TL_ROOT . '/' . $objTarget->value, TL_ROOT . '/' . $objOut->value);

			$objOut = new \File($objOut->value);
		}

		$this->arrReturn[self::$bootstrapCssKey][] = array
		(
			'src'  => $objOut->value,
			'type' => 'all', // 'all' is required for .hidden-print class, not 'screen'
			'mode' => $this->mode,
			'hash' => version_compare(VERSION, '3.4', '>=') ? $objOut->mtime : $objOut->hash,
		);
	}

	/**
	 * variables.less must not be changed
	 * use custom bootstrapVariablesSRC to change variables
	 */
	protected function addBootstrapVariables()
	{
		$objFile = new \File($this->getBootstrapSrc('variables.less'));

		if ($objFile->size > 0) {
			$this->arrCss['variables'] = $objFile->getContent();
		}

		if (!$this->bootstrapVariablesSRC) return false;

		$objTarget = new \File($this->getBootstrapCustomSrc($this->variablesSrc));

		// overwrite bootstrap variables with custom variables
		$objFileModel = \FilesModel::findByPk($this->bootstrapVariablesSRC);

		if ($objFileModel !== false) {
			$objFile = new \File($objFileModel->path);

			if ($this->isFileUpdated($objFile, $objTarget)) {
				$this->rewrite          = true;
				$this->rewriteBootstrap = true;
				$this->arrCss['variables'] .= "\n" . $objFile->getContent();
			} else {
				$this->arrCss['variables'] .= "\n" . $objFile->getContent();
			}
		}

		if ($this->rewriteBootstrap) {
			$objTarget->write($this->arrCss['variables']);
		}
	}

	/**
	 * mixins.less must not be changed, no hash check
	 */
	protected function addBootstrapMixins()
	{
		$objFile = new \File($this->getBootstrapSrc('mixins.less'));

		if (str_replace('v', '', BOOTSTRAPVERSION) >= '3.2.0') {
			preg_match_all('/@import "(.*)";/', $objFile->getContent(), $arrImports);

			if (is_array($arrImports[1])) {
				foreach ($arrImports[1] as $strFile) {
					if (!file_exists(TL_ROOT . '/' . BOOTSTRAPLESSDIR . '/' . $strFile)) continue;

					$objMixinFile = new \File(BOOTSTRAPLESSDIR . '/' . $strFile);
					$this->arrCss['mixins'] .= $objMixinFile->getContent();
				}
			}

			return;
		}


		if ($objFile->size > 0) {
			$this->arrCss['mixins'] = $objFile->getContent();
		}
	}

	/**
	 * alerts.less must not be changed, no hash check
	 */
	protected function addBootstrapAlerts()
	{
		$objFile = new \File($this->getBootstrapSrc('alerts.less'));

		if ($objFile->size > 0) {
			$this->arrCss['alerts'] = $objFile->getContent();
		}
	}


	protected function addBootstrapUtilities()
	{
		$objFile = new \File($this->getBootstrapSrc('utilities.less'));

		if ($objFile->size > 0) {
			$this->arrCss['utilities'] = $objFile->getContent();
		}
	}

	protected function addBootstrapType()
	{
		$objFile = new \File($this->getBootstrapSrc('type.less'));

		if ($objFile->size > 0) {
			$this->arrCss['type'] = $objFile->getContent();
		}
	}

	protected function addFontAwesomeVariables()
	{
		$objFile   = new \File($this->getFontAwesomeSrc('variables.less'));
		$objTarget = new \File($this->getFontAwesomeCustomSrc($this->variablesSrc), true);

		if ($objFile->size > 0) {
			$this->arrCss['variables-fontawesome'] = $objFile->getContent();
			// change font path
			$this->arrCss['variables-fontawesome'] = str_replace("../fonts", '/' . rtrim(FONTAWESOMEFONTDIR, '/'), $this->arrCss['variables-fontawesome']);
		}

		if (!$objTarget->exists() || $objTarget->size == 0) {
			\File::putContent($this->getFontAwesomeCustomSrc($this->variablesSrc), $this->arrCss['variables-fontawesome']);
		}
	}

	protected function addFontAwesomeCore()
	{
		$objFile = new \File($this->getFontAwesomeSrc('core.less'));

		if ($objFile->size > 0) {
			$this->arrCss['core-fontawesome'] = $objFile->getContent();
		}
	}

	protected function addFontAwesomeMixins()
	{
		$objFile = new \File($this->getFontAwesomeSrc('mixins.less'));

		if ($objFile->size > 0) {
			$this->arrCss['mixins-fontawesome'] = $objFile->getContent();
		}
	}

	/**
	 * Font-Awesome 4.0 support for less icons
	 */
	protected function addFontAwesomeIcons()
	{
		$objFile = new \File($this->getFontAwesomeSrc('icons.less'));

		if ($objFile->size > 0) {
			$this->arrCss['icons-fontawesome'] = $objFile->getContent();
		}
	}

	protected function addFontAwesome()
	{
		$objFile   = new \File($this->getFontAwesomeSrc('font-awesome.less'));
		$objTarget = new \File($this->getFontAwesomeCustomSrc('font-awesome-' . $this->title . '.less'));
		$objOut    = new \File($this->getSrc('font-awesome-' . $this->title . '.css'), true);

		if ($this->rewrite || !$objOut->exists() || $objTarget->size == 0 || $objOut->size == 0) {
			$strCss = $objFile->getContent();


			$strCss = str_replace('@import "', '@import "../', $strCss);
			$strCss = str_replace('../variables.less', $this->variablesSrc, $strCss);

			$objTarget->write($strCss);
			$objTarget->close();

			$lessc  = new \lessc();
			$strCss = $lessc->compileFile(TL_ROOT . '/' . $objTarget->value, TL_ROOT . '/' . $objOut->value);

			$objOut = new \File($objOut->value);
		}

		$this->arrReturn[self::$fontAwesomeCssKey][] = array
		(
			'src'  => $objOut->value,
			'type' => 'all',
			'mode' => $this->mode,
			'hash' => version_compare(VERSION, '3.4', '>=') ? $objOut->mtime : $objOut->hash,
		);
	}

	protected function addCustomLessFiles()
	{
		if (!is_array($GLOBALS['TL_USER_CSS']) || empty($GLOBALS['TL_USER_CSS'])) return false;

		foreach ($GLOBALS['TL_USER_CSS'] as $key => $css) {
			$arrCss = trimsplit('|', $css);

			$objFile = new \File($arrCss[0]);

			if ($this->isFileUpdated($objFile, $this->objUserCssFile)) {
				$this->rewrite = true;
			}

			$strContent = $objFile->getContent();

			// replace variables.less by custom variables.less
			$hasImports = preg_match_all('!@import(\s+)?(\'|")(.+)(\'|");!U', $strContent, $arrImport);

			if ($hasImports) {
				$this->objLess->addImportDir($objFile->dirname);
			}

			$this->arrCss[$key] = $strContent;

			unset($GLOBALS['TL_USER_CSS'][$key]);
		}
	}

	protected function isFileUpdated(\File $objFile, \File $objTarget)
	{
		return ($objFile->size > 0 && ($this->rewrite || $this->rewriteBootstrap || $objFile->mtime > $objTarget->mtime));
	}

	public function __get($strKey)
	{
		if (isset($this->arrData[$strKey])) {
			return $this->arrData[$strKey];
		}

		return parent::__get($strKey);
	}

	protected function getSrc($src)
	{
		return CSSDIR . $src;
	}

	protected function getBootstrapSrc($src)
	{
		return BOOTSTRAPLESSDIR . $src;
	}

	protected function getBootstrapCustomSrc($src)
	{
		return BOOTSTRAPLESSCUSTOMDIR . $src;
	}

	protected function getFontAwesomeSrc($src)
	{
		return FONTAWESOMELESSDIR . $src;
	}

	protected function getFontAwesomeCustomSrc($src)
	{
		return FONTAWESOMELESSCUSTOMDIR . $src;
	}

}
