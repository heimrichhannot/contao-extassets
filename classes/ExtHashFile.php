<?php

namespace ExtAssets;

class ExtHashFile extends \File
{
	protected static $hashExtension = 'md5';

	public function __construct($strFile, $blnDoNotCreate=false)
	{
		$strFile = $strFile . '.' . self::$hashExtension;
		parent::__construct($strFile, $blnDoNotCreate);
	}

	public function getHash()
	{
		return trim($this->getContent());
	}
}