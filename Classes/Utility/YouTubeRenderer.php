<?php
namespace T3SBS\T3sbootstrap\Utility;

/*
 * This file is part of the TYPO3 extension t3sbootstrap.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\FileInterface;
use TYPO3\CMS\Core\Resource\FileReference;
use TYPO3\CMS\Core\Resource\OnlineMedia\Helpers\OnlineMediaHelperInterface;
use TYPO3\CMS\Core\Resource\OnlineMedia\Helpers\OnlineMediaHelperRegistry;

/**
 * YouTube renderer class
 */
class YouTubeRenderer implements FileRendererInterface
{
	/**
	 * @var OnlineMediaHelperInterface
	 */
	protected $onlineMediaHelper;

	/**
	 * Returns the priority of the renderer
	 * This way it is possible to define/overrule a renderer
	 * for a specific file type/context.
	 * For example create a video renderer for a certain storage/driver type.
	 * Should be between 1 and 100, 100 is more important than 1
	 *
	 * @return int
	 */
	public function getPriority()
	{
		return 1;
	}

	/**
	 * Check if given File(Reference) can be rendered
	 *
	 * @param FileInterface $file File of FileReference to render
	 * @return bool
	 */
	public function canRender(FileInterface $file)
	{
		return ($file->getMimeType() === 'video/youtube' || $file->getExtension() === 'youtube') && $this->getOnlineMediaHelper($file) !== false;
	}

	/**
	 * Get online media helper
	 *
	 * @param FileInterface $file
	 * @return bool|OnlineMediaHelperInterface
	 */
	protected function getOnlineMediaHelper(FileInterface $file)
	{
		if ($this->onlineMediaHelper === null) {
			$orgFile = $file;
			if ($orgFile instanceof FileReference) {
				$orgFile = $orgFile->getOriginalFile();
			}
			if ($orgFile instanceof File) {
				$this->onlineMediaHelper = OnlineMediaHelperRegistry::getInstance()->getOnlineMediaHelper($orgFile);
			} else {
				$this->onlineMediaHelper = false;
			}
		}
		return $this->onlineMediaHelper;
	}

	/**
	 * Render for given File(Reference) html output
	 *
	 * @param FileInterface $file
	 * @param array $events
	 * @return string
	 */
	public function render(FileInterface $file, $events, $idOnly=false)
	{
		if ($file instanceof FileReference) {
			$orgFile = $file->getOriginalFile();
		} else {
			$orgFile = $file;
		}

		$videoId = $this->getOnlineMediaHelper($file)->getOnlineMediaId($orgFile);
		$mute = $events['videoMute'] ? 'true' : 'false';
		$showControls = $events['videoControls'] ? 'true' : 'false';
		$autoPlay = $events['videoAutoPlay'] ? 'true' : 'false';
		$ratio = $events['videoRatio'];
		$quality = $events['videoQuality'];

		$addEvents = $events['videoLoop'] ? ', loop: true' : '';
		$addEvents .= $events['videoVol'] ? ', vol: '.$events['videoVol'] : '';
		$addEvents .= $events['videoLogo'] ? '' : ', showYTLogo: false';
		$addEvents .= $events['videoRaster'] ? ', addRaster: true' : '';
		$addEvents .= $events['videoGaTrack'] ? '' : ', gaTrack: false';

		if ($idOnly) {
			return 	$videoId;
		}

		return "{videoURL:'".$videoId."', containment:'#dom".$events['uid']."', showControls:".$showControls.", ratio:'".$ratio."', quality: '".$quality."', opacity:1, autoPlay:".$autoPlay.", mute:".$mute.", startAt:0".$addEvents."}";

	}

}
