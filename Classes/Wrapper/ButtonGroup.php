<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\Wrapper;

use TYPO3\CMS\Core\SingletonInterface;

class ButtonGroup implements SingletonInterface
{
    public function getProcessedData(array $processedData, array $flexconf): array
    {
        // Sicherstellen dass class initialisiert ist
        $processedData['class']        = $processedData['class'] ?? '';
        $processedData['visiblePart']  = '';

        // Basis-Klasse: vertikal oder horizontal
        $processedData['class'] .= !empty($flexconf['vertical'])
            ? ' btn-group-vertical'
            : ' btn-group';

        // Größe: FlexForm speichert 'null' als String wenn kein Wert gewählt
        if (!empty($flexconf['btnsize']) && $flexconf['btnsize'] !== 'null') {
            $processedData['class'] .= ' ' . $flexconf['btnsize'];
        }

        $processedData['buttonGroupClass'] = $flexconf['align'] ?? '';

        if (!empty($flexconf['fixedPosition'])) {
            $processedData['buttonGroupClass'] .=
                ' d-none fixedGroupButton fixedPosition fixedPosition-' . $flexconf['fixedPosition'];

            // rotate gilt nur wenn ein Wert gesetzt ist
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
                $processedData['visiblePart']  = $flexconf['visiblePart']
                    ? (int)$flexconf['visiblePart']
                    : 37;
                $processedData['slideIn']      = $flexconf['slideIn'];
            }
        }

        return $processedData;
    }
}
