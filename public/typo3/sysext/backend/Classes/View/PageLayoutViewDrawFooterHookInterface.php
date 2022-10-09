<?php

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

namespace TYPO3\CMS\Backend\View;

/**
 * Interface for classes which hook into PageLayoutView and do additional
 * tt_content_drawFooter processing.
 */
interface PageLayoutViewDrawFooterHookInterface
{
    /**
     * Preprocesses the preview footer rendering of a content element.
     *
     * @param \TYPO3\CMS\Backend\View\PageLayoutView $parentObject Calling parent object
     * @param array $info Processed values
     * @param array $row Record row of tt_content
     */
    public function preProcess(PageLayoutView &$parentObject, &$info, array &$row);
}
