<?php

declare(strict_types=1);

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

namespace TYPO3\CMS\Core\LinkHandling;

/**
 * Resolves emails
 */
class EmailLinkHandler implements LinkHandlingInterface
{

    /**
     * Returns the link to an email as a string
     *
     * @param array $parameters
     * @return string
     */
    public function asString(array $parameters): string
    {
        return 'mailto:' . $parameters['email'];
    }

    /**
     * Returns the email address without the "mailto:" prefix
     * in the 'email' property of the array.
     *
     * @param array $data
     * @return array
     */
    public function resolveHandlerData(array $data): array
    {
        if (stripos($data['email'], 'mailto:') === 0) {
            return ['email' => substr($data['email'], 7)];
        }
        return ['email' => $data['email']];
    }
}
