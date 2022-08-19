<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\Command;

use Symfony\Component\Console\Command\Command;

/*
 * This file is part of the TYPO3 extension t3sbootstrap.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */
class CommandBase extends Command
{

	/**
	* remove dirs
	*/
	public function rmDir(string $path) : int
	{
		if (!is_dir ($path)) {
			return -1;
		}
		$dir = @opendir ($path);
			if (!$dir) {
			return -2;
		}
		while ($entry = @readdir($dir)) {
			if ($entry == '.' || $entry == '..') continue;
			if (is_dir ($path.'/'.$entry)) {
				$res = self::rmDir ($path.'/'.$entry);
				if ($res == -1) {
					@closedir ($dir);
					return -2;
				} else if ($res == -2) {
					@closedir ($dir);
					return -2;
				} else if ($res == -3) {
					@closedir ($dir);
					return -3;
				} else if ($res != 0) {
					@closedir ($dir);
					return -2;
				}
			} else if (is_file ($path.'/'.$entry) || is_link ($path.'/'.$entry)) {
				$res = @unlink ($path.'/'.$entry);
				if (!$res) {
					@closedir ($dir);
					return -2;
				}
			} else {
				@closedir ($dir);
				return -3;
			}
		}
	
		@closedir ($dir);
		$res = @rmdir ($path);
	
		if (!$res) {
			return -2;
		}
	
		return 0;
	}

}
