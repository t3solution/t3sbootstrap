<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\Wrapper;

use TYPO3\CMS\Core\SingletonInterface;

class ButtonGroup implements SingletonInterface
{
    public function getProcessedData(array $processedData, array $flexconf): array
    {
        // Make sure class is initialised
        $processedData['class']        = $processedData['class'] ?? '';
        $processedData['visiblePart']  = '';

        // Base class: vertical or horizontal
        $processedData['class'] .= !empty($flexconf['vertical'])
            ? ' btn-group-vertical'
            : ' btn-group';

        // Size: the flexform stores 'null' as a string when nothing is chosen
        if (!empty($flexconf['btnsize']) && $flexconf['btnsize'] !== 'null') {
            $processedData['class'] .= ' ' . $flexconf['btnsize'];
        }

        $processedData['buttonGroupClass'] = $flexconf['align'] ?? '';

        if (!empty($flexconf['fixedPosition'])) {
            $processedData['buttonGroupClass'] .=
                ' d-none fixedGroupButton fixedPosition fixedPosition-' . $flexconf['fixedPosition'];

            // rotate only counts while a value is set
            if (!empty($flexconf['rotate'])) {
                $processedData['class'] .= ' rotateFixedPosition rotate-' . $flexconf['rotate'];
            }

            $processedData['fixedButton'] = true;

            if (
                !empty($flexconf['slideIn'])
                && !empty($flexconf['vertical'])
                && $flexconf['fixedPosition'] === 'right'
            ) {
                $processedData['class']       .= ' slideInButton';
                // slideIn being set does not mean visiblePart exists
                $processedData['visiblePart']  = !empty($flexconf['visiblePart'])
                    ? (int)$flexconf['visiblePart']
                    : 37;
                $processedData['slideIn']      = $flexconf['slideIn'];
            }
        }

        return $processedData;
    }
}
