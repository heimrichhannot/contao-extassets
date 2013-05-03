<?php


namespace ExtAssets;

class ExtAssets extends \Frontend
{

	public static $twitterBootstrap = 'twitterbootstrap';

	public static function getAssetFolder($type, $root = false)
	{
		switch($type)
		{
			case self::$twitter:
				$path = 'assets/bootstrap';
			break;
			default:
				return false;
		}

		return ($root ? TL_ROOT . '/' : '') . $path;
	}
}