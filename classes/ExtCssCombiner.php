<?php

namespace ExtAssets;

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

	protected $arrLessOptions = array();

	protected $arrLessImportDirs = array();

	protected $cache = true;

	public function __construct(\Model\Collection $objCss, $arrReturn = array(), $blnCache)
	{
		parent::__construct();

		$this->start = microtime(true);
		$this->cache = $blnCache;

		$this->loadDataContainer('tl_extcss');

		while ($objCss->next()) {
			$this->arrData[] = $objCss->row();
		}

		$this->variablesSrc = 'variables-' . $this->title . '.less';
		
		$this->mode = $this->cache ? 'none' : 'static';

		$this->arrReturn = $arrReturn;

		$this->objUserCssFile = new \File($this->getSrc($this->title . '.css'));;

		// rewrite if group css is empty or created/updated recently
		if ($this->objUserCssFile->size == 0 || $this->lastUpdate > $this->objUserCssFile->mtime) {
			$this->rewrite          = true;
			$this->rewriteBootstrap = true;
			$this->cache            = false;
		}

		$this->uriRoot = (TL_ASSETS_URL ? TL_ASSETS_URL : \Environment::get('url')) . '/assets/css/';

		$this->arrLessOptions = array
		(
			'compress'  => !\Config::get('bypassCache'),
			'cache_dir' => TL_ROOT . '/assets/css/lesscache',

		);

		if (!$this->cache) {
			$this->objLess = new \Less_Parser($this->arrLessOptions);

			$this->addBootstrapVariables();
			$this->addFontAwesomeVariables();
			$this->addFontAwesomeCore();
			$this->addFontAwesomeMixins();
			$this->addFontAwesome();
			$this->addBootstrapMixins();
			$this->addBootstrapAlerts();
			$this->addBootstrap();
			$this->addBootstrapUtilities();
			$this->addBootstrapType();

			if ($this->addElegantIcons) {
				$this->addElegantIconsVariables();
				$this->addElegantIcons();
			}

			// HOOK: add custom assets
			if (isset($GLOBALS['TL_HOOKS']['addCustomAssets']) && is_array($GLOBALS['TL_HOOKS']['addCustomAssets'])) {
				foreach ($GLOBALS['TL_HOOKS']['addCustomAssets'] as $callback) {
					$this->import($callback[0]);
					$this->$callback[0]->$callback[1]($this->objLess, $this->arrData, $this);
				}
			}

			$this->addCustomLessFiles();

			$this->addCssFiles();
		} else {
			// remove custom less files as long as we can not provide mixins and variables in cache mode
			unset($GLOBALS['TL_USER_CSS']);

			// always add bootstrap
			$this->addBootstrap();
		}
	}

	public function getUserCss()
	{
		$arrReturn = $this->arrReturn;

		$strCss = $this->objUserCssFile->getContent();

		if (($this->rewrite || $this->rewriteBootstrap)) {
			try {
				$this->objLess->SetImportDirs($this->arrLessImportDirs);
				$strCss = $this->objLess->getCss();
				$this->objUserCssFile->write($strCss);
			} catch (\Exception $e) {
				echo '<pre>';
				echo $e->getMessage();
				echo '</pre>';
			}
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

		if ($this->debug) {
			print '<pre>';
			print_r('ExtCssCombiner execution time: ' . (microtime(true) - $this->start) . ' seconds');
			print '</pre>';
		}

		return $arrReturn;
	}


	protected function addCssFiles()
	{
		$objFiles = ExtCssFileModel::findMultipleByPids($this->ids, array('order' => 'FIELD(pid, ' . implode(",", $this->ids) . '), sorting DESC'));
		
		if ($objFiles === null) {
			return false;
		}

		while ($objFiles->next()) {
			$objFileModel = \FilesModel::findByPk($objFiles->src);

			if ($objFileModel === null) {
				continue;
			}
			
			if (!file_exists(TL_ROOT . '/' . $objFileModel->path)) {
				continue;
			}

			$objFile = new \File($objFileModel->path);

			if ($objFile->size == 0) {
				continue;
			}

			if ($this->isFileUpdated($objFile, $this->objUserCssFile)) {
				$this->rewrite = true;
			}

			$this->objLess->parseFile($objFile->value);
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

			if (is_array($this->variablesOrderSRC)) {
				$strCss = str_replace('../variables.less', $this->variablesSrc, $strCss);
			}

			// remove pr
			if (!$this->addBootstrapPrint) {
				$strCss = str_replace('@import "../print.less";', '//@import "../print.less";', $strCss);
			}

			$objTarget->write($strCss);
			$objTarget->close();

			$objParser = new \Less_Parser($this->arrLessOptions);
			$objParser->parseFile($objTarget->value);

			$objOut = new \File($objOut->value);
			$objOut->write($objParser->getCss());
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

		$strVariables = array();

		if ($objFile->size > 0) {
			$strVariables = $objFile->getContent();
		}

		if (!is_array($this->variablesOrderSRC)) {
			return;
		}

		$objTarget = new \File($this->getBootstrapCustomSrc($this->variablesSrc));

		// overwrite bootstrap variables with custom variables
		$objFilesModels = \FilesModel::findMultipleByUuids($this->variablesOrderSRC);

		if ($objFilesModels !== null) {
			while ($objFilesModels->next()) {
				$objFile = new \File($objFilesModels->path);

				if ($this->isFileUpdated($objFile, $objTarget)) {
					$this->rewrite          = true;
					$this->rewriteBootstrap = true;
					$strVariables .= "\n" . $objFile->getContent();
				} else {
					$strVariables .= "\n" . $objFile->getContent();
				}
			}
		}

		if ($this->rewriteBootstrap) {
			$objTarget->write($strVariables);
		}

		$this->objLess->parse($strVariables);
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
					if (!file_exists(TL_ROOT . '/' . BOOTSTRAPLESSDIR . '/' . $strFile)) {
						continue;
					}

					$objMixinFile = new \File(BOOTSTRAPLESSDIR . '/' . $strFile);
					$this->objLess->parseFile($objMixinFile->value);
				}
			}

			return;
		}


		if ($objFile->size > 0) {
			$this->objLess->parseFile($objFile->value);
		}
	}

	/**
	 * alerts.less must not be changed, no hash check
	 */
	protected function addBootstrapAlerts()
	{
		$objFile = new \File($this->getBootstrapSrc('alerts.less'));

		if ($objFile->size > 0) {
			$this->objLess->parseFile($objFile->value);
		}
	}


	protected function addBootstrapUtilities()
	{
		$arrUtilities = array
		(
			'utilities.less',
			'responsive-utilities.less',
			'forms.less',
			'buttons.less',
			'alerts.less',
			'grid.less',
		);

		foreach ($arrUtilities as $strFile) {
			$objFile = new \File($this->getBootstrapSrc($strFile));

			if ($objFile->size > 0) {
				$this->objLess->parseFile($objFile->value);
			}
		}
	}

	protected function addBootstrapType()
	{
		$objFile = new \File($this->getBootstrapSrc('type.less'));

		if ($objFile->size > 0) {
			$this->objLess->parseFile($objFile->value);
		}
	}

	protected function addFontAwesomeVariables()
	{
		$this->objLess->parseFile($this->getFontAwesomeLessSrc('variables.less'));
	}

	protected function addFontAwesomeCore()
	{
		$this->objLess->parseFile($this->getFontAwesomeLessSrc('core.less'));
	}

	protected function addFontAwesomeMixins()
	{
		$this->objLess->parseFile($this->getFontAwesomeLessSrc('mixins.less'));
	}

	protected function addFontAwesome()
	{
		$objFile = new \File($this->getFontAwesomeCssSrc('font-awesome.css'), true);

		$strCss = $objFile->getContent();
		$strCss = str_replace("../fonts", '/' . rtrim(FONTAWESOMEFONTDIR, '/'), $strCss);

		$this->objLess->parse($strCss);
	}

	protected function addElegantIconsVariables()
	{
		$this->objLess->parseFile($this->getElegentIconsLessSrc('variables.less'));
	}

	protected function addElegantIcons()
	{
		$objFile = new \File($this->getElegentIconsCssSrc('elegant-icons.css'), true);

		$strCss = $objFile->getContent();
		$strCss = str_replace("../fonts", '/' . rtrim(ELEGANTICONSFONTDIR, '/'), $strCss);

		$this->objLess->parse($strCss);
	}

	protected function addCustomLessFiles()
	{
		if (!is_array($GLOBALS['TL_USER_CSS']) || empty($GLOBALS['TL_USER_CSS'])) {
			return false;
		}

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
				$this->arrLessImportDirs[$objFile->dirname] = $objFile->dirname;
			}

			$this->objLess->parseFile($objFile->value);

			unset($GLOBALS['TL_USER_CSS'][$key]);
		}
	}

	protected function isFileUpdated(\File $objFile, \File $objTarget)
	{
		return ($objFile->size > 0 && ($this->rewrite || $this->rewriteBootstrap || $objFile->mtime > $objTarget->mtime));
	}

	public function __get($strKey)
	{
		switch ($strKey) {
			case 'title':
				return standardize(\String::restoreBasicEntities(implode('-', $this->getEach('title'))));
			case 'addBootstrapPrint':
			case 'addFontAwesome':
				return max($this->getEach($strKey));
			case 'addElegantIcons':
				return max($this->getEach($strKey));
			case 'variablesSRC':
			case 'variablesOrderSRC':
				return $this->getEach($strKey);
			case 'ids':
				return $this->getEach('id'); // must be id
			case 'lastUpdate':
				return max($this->getEach('tstamp')); // return max tstamp from css groups
		}

		if (isset($this->arrData[$strKey])) {
			return $this->arrData[$strKey];
		}

		return parent::__get($strKey);
	}

	public function getEach($strKey)
	{
		$return = array();

		foreach ($this->arrData as $key => $value) {
			$value = $value[$strKey];

			$varUnserialized = @unserialize($value);

			if (is_array($varUnserialized)) {
				// flatten array
				$return = array_merge($return, $varUnserialized);
				continue;
			}

			$return[] = $value;
		}

		return $return;
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

	protected function getFontAwesomeCssSrc($src)
	{
		return FONTAWESOMECSSDIR . $src;
	}

	protected function getFontAwesomeLessSrc($src)
	{
		return FONTAWESOMELESSDIR . $src;
	}

	protected function getFontAwesomeCustomSrc($src)
	{
		return FONTAWESOMELESSCUSTOMDIR . $src;
	}

	protected function getElegentIconsCssSrc($src)
	{
		return ELEGANTICONSCSSDIR . $src;
	}

	protected function getElegentIconsLessSrc($src)
	{
		return ELEGANTICONSLESSDIR . $src;
	}

}
