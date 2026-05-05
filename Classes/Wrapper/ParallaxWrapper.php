<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\Wrapper;

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Resource\FileRepository;
use TYPO3\CMS\Core\Resource\StorageRepository;
use TYPO3\CMS\Core\Resource\FileInterface;
use T3SBS\T3sbootstrap\Utility\VideoRenderer;

class ParallaxWrapper implements SingletonInterface
{
	private const MIME_TYPE_MAP = [
		'video/mp4'  => 'mp4',
		'video/webm' => 'webm',
		'video/ogv'  => 'ogv',
	];

	public function __construct(
		private readonly FileRepository    $fileRepository,
		private readonly StorageRepository $storageRepository,
		private readonly VideoRenderer     $videoRenderer,
	) {}

	public function getProcessedData(array $processedData, array $flexconf): array
	{
		// @extensionScannerIgnoreLine
		$defaultStorage = $this->storageRepository->getDefaultStorage();
		$processedData['defaultStorage'] = $defaultStorage?->getStorageRecord()['name'] ?? '';

		$file = $processedData['files'][0] ?? null;
		$processedData['file'] = $file;

		if (!$file instanceof FileInterface) {
			return $processedData;
		}

		if ($file->getType() === 4) {
			$processedData = $this->processVideo($processedData, $file);
		} else {
			$processedData['parallaxImage'] = $file;
		}

		$processedData['width']       = $flexconf['width'] ?? 'auto';
		$processedData['speedFactor'] = $flexconf['speedFactor'] ?: 1;
		$processedData['addHeight']   = !empty($flexconf['addHeight']) ? (int)$flexconf['addHeight'] : 0;
		$processedData['no-mobile']   = !empty($flexconf['mobile']) ? '/iPad|iPhone|iPod|Android/' : '-';

		return $processedData;
	}

	private function processVideo(array $processedData, FileInterface $file): array
	{
		$processedData['video'] = true;
		$mimeType  = $file->getMimeType();
		$extension = $file->getExtension();

		if ($mimeType === 'video/youtube' || $extension === 'youtube') {
			$processedData['youtube']        = true;
			$processedData['videoAutoPlay']  = $file->getProperties()['autoplay'];
			$processedData['videoId']        = $this->videoRenderer->render($file);

			return $processedData;
		}

		if ($mimeType === 'video/vimeo' || $extension === 'vimeo') {
			$processedData['vimeo']          = true;
			$processedData['videoAutoPlay']  = $file->getProperties()['autoplay'];
			$processedData['videoId']        = $this->videoRenderer->render($file);

			return $processedData;
		}

		// Lokales Video
		$processedData['local']    = true;
		$processedData['mimeType'] = self::MIME_TYPE_MAP[$mimeType] ?? '';

		return $processedData;
	}
}
