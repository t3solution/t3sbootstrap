<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\Command;

use Symfony\Component\Console\Command\Command;

class CommandBase extends Command
{

	
	public function rmDir(string $path): void
	{
		if (!is_dir($path)) {
			return;
		}
		$files = new \RecursiveIteratorIterator(
			new \RecursiveDirectoryIterator($path, \FilesystemIterator::SKIP_DOTS),
			\RecursiveIteratorIterator::CHILD_FIRST
		);
		foreach ($files as $file) {
			$file->isDir() ? rmdir($file->getRealPath()) : unlink($file->getRealPath());
		}
		rmdir($path);
	}


}
