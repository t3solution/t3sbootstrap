<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\Updates;

/**
 * Reads a Font Awesome class list and builds the iconpack notation from it.
 * Shared by the three Iconpack upgrade wizards so they cannot drift apart.
 */
final class IconpackClassParser
{
    /**
     * Style tokens, long and short form. The first one in the value wins;
     * a value without any style is treated as solid, as in Font Awesome 4.
     */
    private const STYLES = [
        'fa-solid' => 'solid',
        'fas' => 'solid',
        'fa-regular' => 'regular',
        'far' => 'regular',
        'fa-brands' => 'brands',
        'fab' => 'brands',
        'fa-light' => 'light',
        'fal' => 'light',
        'fa-thin' => 'thin',
        'fat' => 'thin',
        'fa-duotone' => 'duotone',
        'fad' => 'duotone',
    ];

    private const MODIFIERS = [
        'fa-fw' => 'fixed',
        'fa-border' => 'border',
        'fa-spin' => 'spin',
    ];

    /**
     * Every token is matched as a whole word. Comparing with str_contains()
     * made "fa-fast-forward" look like the style "fas".
     *
     * @return array{style: string, name: string, size: string, fixed: bool, border: bool, spin: bool}
     */
    public static function parse(string $value): array
    {
        $result = ['style' => '', 'name' => '', 'size' => '', 'fixed' => false, 'border' => false, 'spin' => false];

        foreach (preg_split('/\s+/', trim($value)) ?: [] as $token) {
            if ($token === '') {
                continue;
            }
            if (isset(self::STYLES[$token])) {
                $result['style'] = $result['style'] !== '' ? $result['style'] : self::STYLES[$token];
                continue;
            }
            if (isset(self::MODIFIERS[$token])) {
                $result[self::MODIFIERS[$token]] = true;
                continue;
            }
            if (preg_match('/^fa-(xs|sm|lg|\d{1,2}x)$/', $token, $matches)) {
                $result['size'] = $matches[1];
                continue;
            }
            // "fa" alone is the Font Awesome 4 base class, never the icon
            if ($token === 'fa' || !str_starts_with($token, 'fa-')) {
                continue;
            }
            if ($result['name'] === '') {
                $result['name'] = substr($token, 3);
            }
        }

        return $result;
    }

    /**
     * Returns the iconpack notation, or '' when no icon name was found - such a
     * record must stay untouched instead of being written as a bare "fa7:".
     */
    public static function toIconfig(string $value): string
    {
        $parsed = self::parse($value);

        if ($parsed['name'] === '') {
            return '';
        }

        $iconfig = 'fa7:' . ($parsed['style'] !== '' ? $parsed['style'] : 'solid') . ',' . $parsed['name'];

        if ($parsed['size'] !== '') {
            $iconfig .= ',size:' . $parsed['size'];
        }
        if ($parsed['border']) {
            $iconfig .= ',decoration:border';
        }
        if ($parsed['spin']) {
            $iconfig .= ',transform:spin';
        }
        if ($parsed['fixed']) {
            $iconfig .= ',fixed:true';
        }

        return $iconfig;
    }
}
