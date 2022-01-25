<?php
namespace T3SBS\T3sbootstrap\ViewHelpers;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\MathUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * This file is part of the TYPO3 extension t3sbootstrap.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */
class PageBrowsingViewHelper extends AbstractViewHelper
{
	use CompileWithRenderStatic;

	/**
	 * As this ViewHelper renders HTML, the output must not be escaped.
	 *
	 * @var bool
	 */
	protected $escapeOutput = false;

	/**
	 * @var string
	 */
	protected static $prefixId = 'tx_indexedsearch';

	/**
	 * Initialize arguments
	 */
	public function initializeArguments()
	{
		parent::initializeArguments();
		$this->registerArgument('maximumNumberOfResultPages', 'int', '', true);
		$this->registerArgument('numberOfResults', 'int', '', true);
		$this->registerArgument('resultsPerPage', 'int', '', true);
		$this->registerArgument('currentPage', 'int', '', false, 0);
		$this->registerArgument('freeIndexUid', 'int', '');
	}


	public static function renderStatic(array $arguments, \Closure $renderChildrenClosure, RenderingContextInterface $renderingContext): string
	{
		$maximumNumberOfResultPages = $arguments['maximumNumberOfResultPages'];
		$numberOfResults = $arguments['numberOfResults'];
		$resultsPerPage = $arguments['resultsPerPage'];
		$currentPage = $arguments['currentPage'];
		$freeIndexUid = $arguments['freeIndexUid'];

		if ($resultsPerPage <= 0) {
			$resultsPerPage = 10;
		}
		$pageCount = (int)ceil($numberOfResults / $resultsPerPage);
		// only show the result browser if more than one page is needed
		if ($pageCount === 1) {
			return '';
		}

		// Check if $currentPage is in range
		$currentPage = MathUtility::forceIntegerInRange($currentPage, 0, $pageCount - 1);

		$content = '';
		// prev page
		// show on all pages after the 1st one
		if ($currentPage > 0) {
			$label = LocalizationUtility::translate('displayResults.previous', 'IndexedSearch');
			$content .= '<li>' . self::makecurrentPageSelector_link($label, $currentPage - 1, $freeIndexUid) . '</li>';
		}
		// Check if $maximumNumberOfResultPages is in range
		$maximumNumberOfResultPages = MathUtility::forceIntegerInRange($maximumNumberOfResultPages, 1, $pageCount, 10);
		// Assume $currentPage is in the middle and calculate the index limits of the result page listing
		$minPage = $currentPage - (int)floor($maximumNumberOfResultPages / 2);
		$maxPage = $minPage + $maximumNumberOfResultPages - 1;
		// Check if the indexes are within the page limits
		if ($minPage < 0) {
			$maxPage -= $minPage;
			$minPage = 0;
		} elseif ($maxPage >= $pageCount) {
			$minPage -= $maxPage - $pageCount + 1;
			$maxPage = $pageCount - 1;
		}
		$pageLabel = '';
		for ($a = $minPage; $a <= $maxPage; $a++) {
			$label = trim($pageLabel . ' ' . ($a + 1));
			$label = self::makecurrentPageSelector_link($label, $a, $freeIndexUid);
			if ($a === $currentPage) {
				$content .= '<li class="tx-indexedsearch-browselist-currentPage page-item"><strong>' . $label . '</strong></li>';
			} else {
				$content .= '<li class="page-item">' . $label . '</li>';
			}
		}
		// next link
		if ($currentPage < $pageCount - 1) {
			$label = LocalizationUtility::translate('displayResults.next', 'IndexedSearch');
			$content .= '<li>' . self::makecurrentPageSelector_link($label, ($currentPage + 1), $freeIndexUid) . '</li>';
		}
		return '<ul class="tx-indexedsearch-browsebox pagination">' . $content . '</ul>';
	}

	/**
	 * Used to make the link for the result-browser.
	 * Notice how the links must resubmit the form after setting the new currentPage-value in a hidden formfield.
	 *
	 * $str String to wrap in <a> tag
	 * $p currentPage value
	 * $freeIndexUid List of integers pointing to free indexing configurations to search. -1 represents no filtering, 0 represents TYPO3 pages only, any number above zero is a uid of an indexing configuration!
	 */
	protected static function makecurrentPageSelector_link(string $str, int $p, string $freeIndexUid): string
	{
		$onclick = 'document.getElementById(' . GeneralUtility::quoteJSvalue(self::$prefixId . '_pointer') . ').value=' . GeneralUtility::quoteJSvalue($p) . ';';
		if ($freeIndexUid !== null) {
			$onclick .= 'document.getElementById(' . GeneralUtility::quoteJSvalue(self::$prefixId . '_freeIndexUid') . ').value=' . GeneralUtility::quoteJSvalue($freeIndexUid) . ';';
		}
		$onclick .= 'document.getElementById(' . GeneralUtility::quoteJSvalue(self::$prefixId) . ').submit();return false;';
		return '<a class="page-link" href="#" onclick="' . htmlspecialchars($onclick) . '">' . htmlspecialchars($str) . '</a>';
	}
}
