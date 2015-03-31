<?php


namespace ExtAssets;

class ExtAssets extends \Frontend
{

	public function __construct()
	{
		parent::__construct();

		$this->initBootstrap();
		$this->initFontAwesome();
	}

	public static function initBootstrap($replace=false)
	{
        if($replace && is_dir(TL_ROOT . '/' . BOOTSTRAPDIR))
        {
            $objFolder = new \Folder(TL_ROOT . '/' . BOOTSTRAPDIR);
            $objFolder->delete();
        }

		// download boostrap if folder doesn't exist
		if(!is_dir(TL_ROOT . '/' . BOOTSTRAPDIR))
		{
			$objArchive = new \File('system/tmp/bootstrap.zip');
			$objArchive->write(file_get_contents('https://github.com/twbs/bootstrap/archive/' . BOOTSTRAPVERSION . '.zip'));
			$objArchive->close();

			$zip = new \ZipArchive();
			$res = $zip->open(TL_ROOT . '/' . 'system/tmp/bootstrap.zip');

			if ($res === TRUE)
			{
				$zip->extractTo(TL_ROOT . '/assets');
				$objFolder = new \Folder('assets/' . $zip->getNameIndex(0)); // get font-aweseome folder name inside zip archive
				$objFolder->renameTo(rtrim(BOOTSTRAPDIR, '/'));
			}
		}

		// custom bootstrap file directory
		if(!is_dir(TL_ROOT . '/' . BOOTSTRAPLESSCUSTOMDIR))
		{
			new \Folder(BOOTSTRAPLESSCUSTOMDIR);

			// Store the index.html file
			$objFile = new \File('templates/index.html', true);
			$objFile->copyTo(BOOTSTRAPLESSCUSTOMDIR . '/index.html');
		}
	}

    public static function initFontAwesome($replace=false)
	{
        if($replace && is_dir(TL_ROOT . '/' . FONTAWESOMEDIR))
        {
            $objFolder = new \Folder(TL_ROOT . '/' . FONTAWESOMEDIR);
            $objFolder->delete();
        }

		// download font-awesome if folder doesn't exist
		if(!is_dir(TL_ROOT . '/' . FONTAWESOMEDIR))
		{
			$objArchive = new \File('system/tmp/font-awesome.zip');
			$objArchive->write(file_get_contents('https://github.com/FortAwesome/Font-Awesome/archive/' . FONTAWESOMEVERSION . '.zip'));
			$objArchive->close();

			$zip = new \ZipArchive();
			$res = $zip->open(TL_ROOT . '/' . 'system/tmp/font-awesome.zip');

			if ($res === TRUE)
			{
				$zip->extractTo(TL_ROOT . '/assets');
				$objFolder = new \Folder('assets/' . $zip->getNameIndex(0)); // get font-aweseome folder name inside zip archive
				$objFolder->renameTo(rtrim(FONTAWESOMEDIR, '/'));
			}
		}
	}
}