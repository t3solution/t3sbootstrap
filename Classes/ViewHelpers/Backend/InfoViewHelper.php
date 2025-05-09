<?php

declare(strict_types=1);

namespace T3SBS\T3sbootstrap\ViewHelpers\Backend;

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;

/**
 * This file is part of the TYPO3 extension t3sbootstrap.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */
class InfoViewHelper extends AbstractViewHelper
{
	protected $escapeOutput = false;

	public function initializeArguments(): void
	{
		$this->registerArgument('pageContainer', 'string', 'page Container', true);
		$this->registerArgument('backendLayout', 'string', 'backend Layout', true);
		$this->registerArgument('record', 'array', 'record', true);
	}

	public function render(): string
	{
		$configurationManager = GeneralUtility::makeInstance(ConfigurationManager::class);	
		$ts = $configurationManager->getConfiguration(ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT);
		$config = $ts['page.']['10.']['settings.']['config.'];
		$backendLayout = !empty($this->arguments['backendLayout']) ? $this->arguments['backendLayout'] : '';
		$record	  = !empty($this->arguments['record']) ? $this->arguments['record'] : '';
		$info = '';

		if ( $record['hidden'] === 0 ) {
	
			$pageContainer 					= !empty($this->arguments['pageContainer']) ? $this->arguments['pageContainer'] : '';
			$container 						= !empty($record['tx_t3sbootstrap_container']) ? $record['tx_t3sbootstrap_container'] : '';
			$jumbotronContainer 			= !empty($config['jumbotronContainer']) ? $config['jumbotronContainer'] : '';
			$footerContainer				= !empty($config['footerContainer']) ? $config['footerContainer'] : '';
			$expandedContentTopContainer	= !empty($config['expandedcontentContainertop']) ? $config['expandedcontentContainertop'] : '';
			$expandedContentBottomContainer	= !empty($config['expandedcontentContainerbottom']) ? $config['expandedcontentContainerbottom'] : '';
	
			$extraClass 	= $record['tx_t3sbootstrap_extra_class'];
			$frame 			= $record['frame_class'] === 'default' ? '': $record['frame_class'];
			$layout 		= $record['layout'] === 0 ? '' : $record['layout'];
			$colPos 		= $record['colPos'];
			$oneColLayout 	= $backendLayout === 'OneCol' || $backendLayout === 'OneCol_Extra' ? TRUE : FALSE;
	
			if (!empty($container)) {

				if ($record['CType'] === 'background_wrapper') {
					$info .= '<strong>Container (inside):</strong> '.$container.' ';
				} else {
						
					if ($colPos === 0) {
						if ($oneColLayout) {
							if (!empty($pageContainer)) {
								$container = '<span class="text-danger">' . $container . '</span>';
							}
						} else {
							$container = '<span class="text-danger">' . $container . '</span>';	
						}
					}
					if ($colPos === 3) {
						if (!empty($jumbotronContainer) && $jumbotronContainer !== 'none') {
							$container = '<span class="text-danger">' . $container . '</span>';
						}
					}
					if ($colPos === 4) {
						if (!empty($footerContainer) && $footerContainer !== 'none') {
							$container = '<span class="text-danger">' . $container . '</span>';
						}
					}
					if ($colPos === 20) {
						if (!empty($expandedContentTopContainer)) {
							$container = '<span class="text-danger">' . $container . '</span>';
						}
					}
					if ($colPos === 21) {
						if (!empty($expandedContentBottomContainer)) {
							$container = '<span class="text-danger">' . $container . '</span>';
						}	
					}
				
					$info .= '<strong>Container:</strong> '.$container.' ';
				}

			}

			if (!empty($extraClass)) {
				if (!empty($info)) {$info .= ' | ';}
				$info .= '<strong>Extra Class:</strong> '.$extraClass.' ';
			}
			if (!empty($layout)) {
				if (!empty($info)) {$info .= ' | ';}
				$info .= '<strong>Layout:</strong> '.$layout.' ';
			}
			if (!empty($frame)) {
				if (!empty($info)) {$info .= ' | ';}
				$info .= '<strong>Frame:</strong> '.$frame.' ';
			}
	
	
			if ( $GLOBALS['BE_USER']->isAdmin() ) {
			
				$css = $record['tx_t3sbootstrap_css'];
				$js = $record['tx_t3sbootstrap_js'];
			
				if (!empty($css) || !empty($js)) {
				
					if (!empty($info)) {$info .= ' | ';}
					$info .= '<span class="text-danger">Contains custom ';
				
					if (!empty($css) && !empty($js)) {
						$info .= 'CSS & JS';
					}
					if (!empty($css)) {
						$info .= 'CSS';
					}
					if (!empty($js)) {
						$info .= 'JS';
					}
					$info .= '</span> ';
				}
			}
	
		} 	

		return $info;
	}
}
