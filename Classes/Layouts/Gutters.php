<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\Layouts;

use TYPO3\CMS\Core\SingletonInterface;

class Gutters implements SingletonInterface
{

	/**
	 * Class on the .row that replaces Bootstrap's vertical gutter technique with row-gap.
	 * The rule lives in Partials/MainAssets.fluid.html.
	 */
	public const ROW_GAP_CLASS = 't3sbs-row-gap';

	/**
	 * Returns the $processedData
	 */
	public function getGutters(array $processedData, array $flexconf): array
	{
		$horizontalGutters = !empty($flexconf['horizontalGutters']) ? trim((string)$flexconf['horizontalGutters']) : '';
		$verticalGutters = !empty($flexconf['verticalGutters']) ? trim((string)$flexconf['verticalGutters']) : '';

		$classes = array_filter([$horizontalGutters, $verticalGutters]);

		if ( $verticalGutters ) {
			# Bootstrap builds the vertical gutter from a negative margin-top on .row and a
			# positive one on every column. At the end of a page that sticks out below the
			# row, which the Bootstrap docs answer with a wrapper carrying .overflow-hidden.
			#
			# That wrapper turns every ancestor into a scroll container, and position: sticky
			# sticks to the nearest one - inside a box that does not scroll, so .sticky-top in
			# a column stopped working as soon as a vertical gutter was set.
			#
			# row-gap does the same spacing without a negative margin. Nothing sticks out,
			# so there is nothing to clip and no wrapper is needed.
			$classes[] = self::ROW_GAP_CLASS;
		}

		$processedData['gutters'] = $classes === [] ? '' : ' '.implode(' ', $classes);

		# Kept for templates and own overrides that still ask for it - always empty now.
		$processedData['extraWrapperClass'] = '';

		return $processedData;
	}

}
