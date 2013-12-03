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

	protected function initBootstrap()
	{
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
				$objFolder = new \Folder('assets/bootstrap-' . BOOTSTRAPVERSION);
				$objFolder->renameTo(rtrim(BOOTSTRAPDIR, '/'));
			}
		}

		// custom bootstrap file directory
		if(!is_dir(TL_ROOT . '/' . BOOTSTRAPLESSCUSTOMDIR))
		{
			new \Folder(BOOTSTRAPLESSCUSTOMDIR);

			// Store the index.html file
			$objFile = new \File('templates/index.html', true);
			$objFile->copyTo($dir . '/index.html');
		}
	}

	protected function initFontAwesome()
	{
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
				$objFolder = new \Folder('assets/Font-Awesome-' . FONTAWESOMEVERSION);
				$objFolder->renameTo(rtrim(FONTAWESOMEDIR, '/'));
			}
		}
	}
}