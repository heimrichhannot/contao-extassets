<?php

namespace ExtAssets;

class ExtAutomator extends \Automator
{
	public function purgeLessCache()
	{
		// Purge the folder
		$objFolder = new \Folder(BOOTSTRAPLESSCUSTOMDIR);
		$objFolder->purge();

		// Restore the index.html file
		$objFile = new \File('templates/index.html', true);
		$objFile->copyTo(BOOTSTRAPLESSCUSTOMDIR . 'index.html');

		// Recreate the less files
		$this->import('ExtCss');
		$this->ExtCss->updateExtCss();

		// Recreate css files
		$this->import('StyleSheets');
		$this->StyleSheets->updateStylesheets();

		// Also empty the page cache so there are no links to deleted scripts
		$this->purgePageCache();

		// Add a log entry
		$this->log('Purged the less cache', 'ExtAssets purgeLessCache()', TL_CRON);
	}
}