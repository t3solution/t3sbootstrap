<?php
namespace T3SBS\T3sbootstrap\Utility;

/*
 * This file is part of the TYPO3 extension t3sbootstrap.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Resource\FileInterface;

/**
 * Class FileRendererInterface
 */
interface FileRendererInterface extends \TYPO3\CMS\Core\SingletonInterface {

	/**
	 * Returns the priority of the renderer
	 * This way it is possible to define/overrule a renderer
	 * for a specific file type/context.
	 *
	 * For example create a video renderer for a certain storage/driver type.
	 *
	 * Should be between 1 and 100, 100 is more important than 1
	 *
	 * @return int
	 */
	public function getPriority();

	/**
	 * Check if given File(Reference) can be rendered
	 *
	 * @param FileInterface $file File or FileReference to render
	 * @return bool
	 */
	public function canRender(FileInterface $file);

	/**
	 * Render for given File(Reference) HTML output
	 *
	 * @param FileInterface $file
	 * @param array $events
	 * @return string
	 */
	public function render(FileInterface $file, $events);
}