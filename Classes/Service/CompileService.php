<?php

declare(strict_types=1);

namespace T3SBS\T3sbootstrap\Service;

use T3SBS\T3sbootstrap\Parser\ParserInterface;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use Psr\Http\Message\ServerRequestInterface;

/*
 * This file is part of the TYPO3 extension t3sbootstrap.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */
class CompileService
{
    /**
     * @var string
     */
    protected $tempDirectory = 'typo3temp/assets/t3sbootstrap/css/';

    /**
     * @var string
     */
    protected $tempDirectoryRelativeToRoot = '../../../../';

    /**
     * @throws \Exception
     */
    public function getCompiledFile(ServerRequestInterface $request, string $file): ?string
    {
        $absoluteFile = GeneralUtility::getFileAbsFileName($file);

        // Ensure cache directory exists
        if (!file_exists(Environment::getPublicPath() . '/' . $this->tempDirectory)) {
            GeneralUtility::mkdir_deep(Environment::getPublicPath() . '/' . $this->tempDirectory);
        }

        // Settings
        $settings = [
            'file' => [
                'absolute' => $absoluteFile,
                'relative' => $file,
                'info' => pathinfo($absoluteFile)
            ],
            'cache' => [
                'tempDirectory' => $this->tempDirectory,
                'tempDirectoryRelativeToRoot' => $this->tempDirectoryRelativeToRoot,
            ],
            'options' => [
                'override' => false,
                'sourceMap' => false,
                'compress' => true
            ],
            'variables' => []
        ];

        // Parser
        if (!empty($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/t3sbootstrap/css']['parser'])
            && is_array($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/t3sbootstrap/css']['parser'])
        ) {
            foreach ($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/t3sbootstrap/css']['parser'] as $className) {
                $parser = GeneralUtility::makeInstance($className);
                if ($parser instanceof ParserInterface
                    && !empty($settings['file']['info']['extension'])
                    && $parser->supports($settings['file']['info']['extension'])
                ) {
                    try {
                        return $parser->compile($file, $settings);
                    } catch (\Exception $e) {
                        throw $e;
                    }
                }
            }
        }

        return null;
    }
    
}
