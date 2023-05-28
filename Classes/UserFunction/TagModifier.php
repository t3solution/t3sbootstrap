<?php declare(strict_types=1);

/*
 * This file is part of the composer package buepro/typo3-pizpalue.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace T3SBS\T3sbootstrap\UserFunction;

use TYPO3\CMS\Core\Html\RteHtmlParser;

class TagModifier
{
    /**
     * The email link handler validates the email address before returning to the editor.
     * To be able to use constants as place holders for email addresses some at-text (e.g. @a)
     * can be appended to the constant (e.g. ###myEmail###@a). This method removes the at-text.
     *
     * @param string|array $tag
     */
    public function fixATagHrefAttribute($tag, RteHtmlParser $parser): string
    {

        $href = is_string($tag) ? $tag : (string)$tag[0];
        if (strpos($href, 'mailto:') === 0 && ($pos = (int)strpos($href, '###@')) > 0) {
            $href = preg_replace('/###(@[^?]*)/', '###', $href) ?? $href;
        }
        return $href;
    }
}