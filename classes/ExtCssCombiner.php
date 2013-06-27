<?php

namespace ExtAssets;

use Contao\File;
use ExtCssFileModel;
use ExtHashFile;

require TL_ROOT . "/system/modules/extassets/classes/vendor/lessphp/lessc.inc.php";

class ExtCssCombiner extends \Frontend
{

	protected $rewrite = false;

	protected $rewriteBootstrap = false;

	protected static $cssDir = 'assets/css/';

	protected static $bootstrapDir = 'assets/bootstrap/less/';

	protected $arrCss = array();

	protected $arrReturn = array();

	protected $mode = 'static';

	protected $variablesSrc;

	public $debug = false;

	public function __construct(ExtCssModel $objCss)
	{
		parent::__construct();
		$this->loadDataContainer('tl_extcss');
		$this->arrData = $objCss->row();
		$this->mode = $GLOBALS['TL_CONFIG']['bypassCache'] ? 'none' : 'static';

		$this->checkModelUpdate();

		$this->variablesSrc = 'variables-' . $this->title . '.less';

		if($this->debug)
		{
			$this->rewrite = true;
			$this->rewriteBootstrap = true;
		}

		if($this->addBootstrap)
		{
			$this->addBootstrapVariables();
			$this->addBootstrapMixins();
			$this->addBootstrap();

			if($this->bootstrapResponsive)
			{
				$this->addBootstrapResponsive();
			}
		}

		$this->addCssFiles();
	}

	public function getUserCss()
	{
		$arrReturn = $this->arrReturn;

		$objFile = new \File($this->getSrc($this->title . '.css'));

		if(is_array($this->arrCss) && !empty($this->arrCss) && ($this->rewrite || $this->rewriteBootstrap))
		{
			$lessc = new \lessc();
			$strCss = $lessc->compile(implode("\n", $this->arrCss));
			$objFile->write($strCss);
		}

		$arrReturn[] = sprintf('%s|screen|%s|%s', $objFile->value, $this->mode, $objFile->hash);

		return $arrReturn;
	}


	protected function addCssFiles()
	{
		$objFiles = ExtCssFileModel::findMultipleByPid($this->id);

		if($objFiles === null) return false;

		while($objFiles->next())
		{
			$objFileModel = \FilesModel::findByPk($objFiles->src);

			if($objFileModel === null) continue;

			$objFile = new \File($objFileModel->path);

			if($objFile->size == 0) continue;

			$objHash = new \ExtHashFile($objFileModel->path);

			$strHash = $objHash->getHash();

			if($this->isFileUpdated($objFile, $strHash) || $this->rewrite)
			{
				$this->rewrite = true;
				$objHash->write($objFile->hash);
			}

			$this->arrCss[$objFileModel->id] = $objFile->getContent();
		}
	}

	protected function addBootstrap()
	{
		$objFile = new \File($this->getBootstrapSrc('bootstrap.less'));
		$objTarget = new \File($this->getBootstrapSrc('bootstrap-' . $this->title .  '.less'));
		$objOut = new \File($this->getSrc('bootstrap-' . $this->title .  '.css'), true);

		if($this->rewriteBootstrap || !$objOut->exists())
		{
			$strCss = $objFile->getContent();
			$strCss = str_replace('variables.less', $this->variablesSrc, $strCss);
			$objTarget->write($strCss);

			$strCss = \lessc::ccompile(TL_ROOT . '/' . $objTarget->value, TL_ROOT . '/' . $objOut->value);
			$objOut = new \File($objOut->value);
		}

		$this->arrReturn[] = sprintf('%s|screen|%s|%s', $objOut->value, $this->mode, $objOut->hash);
	}

	/**
	 * variables.less must not be changed
	 * use custom bootstrapVariablesSRC to change variables
	 */
	protected function addBootstrapVariables()
	{
		$objFile = new \File($this->getBootstrapSrc('variables.less'));
		$objTarget = new \File($this->getBootstrapSrc($this->variablesSrc));

		if($objFile->size > 0)
		{
			$this->arrCss['variables'] = $objFile->getContent();
		}

		// overwrite bootstrap variables with custom variables
		if($this->bootstrapVariablesSRC)
		{
			$objFileModel = \FilesModel::findByPk($this->bootstrapVariablesSRC);

			if($objFileModel !== false)
			{
				$objFile = new \File($objFileModel->path);
				$objHash = new ExtHashFile($objFileModel->path);


				$strHash = $objHash->getHash();

				if($this->isFileUpdated($objFile, $strHash))
				{
					$this->rewrite = true;
					$this->rewriteBootstrap = true;
					$this->arrCss['variables'] .= "\n" . $objFile->getContent();
					$objHash->write($objFile->hash);
				} else {
					$this->arrCss['variables'] .= "\n" . $objFile->getContent();
				}
			}
		}

		if($this->rewriteBootstrap)
		{
			$objTarget->write($this->arrCss['variables']);
		}
	}

	/**
	 * mixins.less must not be changed, no hash check
	 */
	protected function addBootstrapMixins()
	{
		$objFile = new \File($this->getBootstrapSrc('mixins.less'));

		if($objFile->size > 0)
		{
			$this->arrCss['mixins'] = $objFile->getContent();
		}
	}


	protected function addBootstrapResponsive()
	{
		$objFile = new \File($this->getBootstrapSrc('responsive.less'));
		$strTarget = 'bootstrap-responsive-' . $this->title . '.less';
		$objTarget = new \File($this->getBootstrapSrc($strTarget));
		$objOutput = new \File($this->getSrc('bootstrap-responsive-' . $this->title . '.css'), true);

		$arrDevices = deserialize($this->bootstrapResponsiveDevices);

		$strCss = $objFile->getContent();

		if($this->rewriteBootstrap || $objOutput->size == 0)
		{
			$strCss = str_replace('variables.less', $this->variablesSrc, $strCss);

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
								$strCss = str_replace('@import "responsive-1200px-min.less";', '//@import "responsive-1200px-min.less";', $strCss);
								break;
							case 'tablet':
								$strCss = str_replace('@import "responsive-768px-979px.less";', '//@import "responsive-768px-979px.less";', $strCss);
								break;
							case 'phone':
								$strCss = str_replace('@import "responsive-767px-max.less";', '//@import "responsive-767px-max.less";', $strCss);
								break;
						}
					}

				}
				
				$objTarget->write($strCss);
				$objTarget->close();
				

				\lessc::ccompile(TL_ROOT . '/' . $objTarget->value, TL_ROOT . '/' . $objOutput->value);
				$objOutput = new \File($objOutput->value);
			}
		}

		$this->arrReturn[] = sprintf('%s|screen|%s|%s', $objOutput->value, $this->mode, $objOutput->hash);
	}

	/**
	 * If ExtCss Model has changed set:
	 * - $this->rewrite
	 * - $this->rewriteBootstrap
	 * to true. Bootstrap and default Files update will be forced.
	 */
	protected function checkModelUpdate()
	{
		$objFile = new ExtHashFile($this->getSrc($this->title));

		$strHash = $objFile->getHash();

		// hash not set yet, file recently created new
		if(empty($strHash) || $this->tstamp > $strHash)
		{
			$this->rewrite = true;
			$this->rewriteBootstrap = true;
			$objFile->write($this->tstamp);
		}
	}

	protected function isFileUpdated(\File $objFile, $strHash)
	{
		return($objFile->size > 0 && ($this->rewrite || $this->rewriteBootstrap || empty($strHash) || $objFile->hash != $strHash));
	}

	public function __get($strKey)
	{
		if (isset($this->arrData[$strKey]))
		{
			return $this->arrData[$strKey];
		}

		return parent::__get($strKey);
	}

	protected function getSrc($src)
	{
		return self::$cssDir . $src;
	}

	protected function getBootstrapSrc($src)
	{
		return self::$bootstrapDir . $src;
	}

}