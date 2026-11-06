<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\Service;

use TYPO3\CMS\Core\SingletonInterface;

/**
 * Maps the cdn version settings onto the files t3sbootstrap:cdnToLocal writes.
 *
 * Pure on purpose - no filesystem, no network, no state. The same map drives the
 * download, the "is anything missing" check and --dry-run, so the file list exists
 * exactly once.
 *
 * settings.bootstrap.cdn holds more than versions: ten *Integrity keys with SRI
 * hashes plus enable, customScss, bootswatch and googlefonts. Only the keys named
 * below describe downloads - never iterate this array generically.
 */
final class CdnAssetMap implements SingletonInterface
{
    /**
     * @param array<string, mixed> $cdnSettings settings.bootstrap.cdn
     * @return array<string, array{url: string, extend: bool}> path below the asset root => source
     */
    public function build(array $cdnSettings): array
    {
        $map = [];

        foreach ($cdnSettings as $key => $version) {
            if (!is_scalar($version) || (string)$version === '') {
                continue;
            }

            foreach ($this->filesFor((string)$key, (string)$version) as $relPath => $source) {
                $map[$relPath] = $source;
            }
        }

        return $map;
    }

    /**
     * The local file names are part of the contract: TypoScript references them in
     * includeCSS/includeJS. "popper.js" holding popper.min.js is deliberate.
     *
     * "extend" strips the source map comment with /*# instead of //# - set for the
     * two files that carry a css source map.
     *
     * @return array<string, array{url: string, extend: bool}>
     */
    private function filesFor(string $key, string $version): array
    {
        return match ($key) {
            'bootstrap' => [
                'T3SB-CSS/bootstrap.min.css' => [
                    'url' => 'https://cdn.jsdelivr.net/npm/bootstrap@' . $version . '/dist/css/bootstrap.min.css',
                    'extend' => true,
                ],
                'T3SB-JS/bootstrap.min.js' => [
                    'url' => 'https://cdn.jsdelivr.net/npm/bootstrap@' . $version . '/dist/js/bootstrap.min.js',
                    'extend' => false,
                ],
                'T3SB-JS/bootstrap.bundle.min.js' => [
                    'url' => 'https://cdn.jsdelivr.net/npm/bootstrap@' . $version . '/dist/js/bootstrap.bundle.min.js',
                    'extend' => false,
                ],
            ],
            'popperjs' => [
                'T3SB-JS/popper.js' => [
                    'url' => 'https://cdnjs.cloudflare.com/ajax/libs/popper.js/' . $version . '/umd/popper.min.js',
                    'extend' => false,
                ],
            ],
            'jquery' => [
                'T3SB-JS/jquery.min.js' => [
                    'url' => 'https://code.jquery.com/jquery-' . $version . '.min.js',
                    'extend' => false,
                ],
            ],
            'lazyload' => [
                'T3SB-JS/lazyload.min.js' => [
                    'url' => 'https://cdn.jsdelivr.net/npm/vanilla-lazyload@' . $version . '/dist/lazyload.min.js',
                    'extend' => false,
                ],
            ],
            'animate' => [
                'T3SB-CSS/animate.compat.css' => [
                    'url' => 'https://cdnjs.cloudflare.com/ajax/libs/animate.css/' . $version . '/animate.compat.css',
                    'extend' => false,
                ],
            ],
            'baguetteBox' => [
                'T3SB-CSS/baguetteBox.min.css' => [
                    'url' => 'https://cdnjs.cloudflare.com/ajax/libs/baguettebox.js/' . $version . '/baguetteBox.min.css',
                    'extend' => false,
                ],
                'T3SB-JS/baguetteBox.min.js' => [
                    'url' => 'https://cdnjs.cloudflare.com/ajax/libs/baguettebox.js/' . $version . '/baguetteBox.min.js',
                    'extend' => false,
                ],
            ],
            'halkabox' => [
                'T3SB-CSS/halkaBox.min.css' => [
                    'url' => 'https://cdn.jsdelivr.net/npm/halkabox@' . $version . '/dist/halkaBox.min.css',
                    'extend' => true,
                ],
                'T3SB-JS/halkaBox.min.js' => [
                    'url' => 'https://cdn.jsdelivr.net/npm/halkabox@' . $version . '/dist/halkaBox.min.js',
                    'extend' => false,
                ],
            ],
            'glightbox' => [
                'T3SB-CSS/glightbox.min.css' => [
                    'url' => 'https://cdn.jsdelivr.net/npm/glightbox@' . $version . '/dist/css/glightbox.min.css',
                    'extend' => false,
                ],
                'T3SB-JS/glightbox.min.js' => [
                    'url' => 'https://cdn.jsdelivr.net/npm/glightbox@' . $version . '/dist/js/glightbox.min.js',
                    'extend' => false,
                ],
            ],
            'masonry' => [
                'T3SB-JS/masonry.pkgd.min.js' => [
                    'url' => 'https://cdnjs.cloudflare.com/ajax/libs/masonry/' . $version . '/masonry.pkgd.min.js',
                    'extend' => false,
                ],
            ],
            'jarallax' => [
                'T3SB-JS/jarallax.min.js' => [
                    'url' => 'https://unpkg.com/jarallax@' . $version . '/dist/jarallax.min.js',
                    'extend' => false,
                ],
                'T3SB-JS/jarallax-video.min.js' => [
                    'url' => 'https://unpkg.com/jarallax@' . $version . '/dist/jarallax-video.min.js',
                    'extend' => false,
                ],
            ],
            'swiper' => [
                'T3SB-CSS/swiper-bundle.min.css' => [
                    'url' => 'https://unpkg.com/swiper@' . $version . '/swiper-bundle.min.css',
                    'extend' => false,
                ],
                'T3SB-JS/swiper-bundle.min.js' => [
                    'url' => 'https://unpkg.com/swiper@' . $version . '/swiper-bundle.min.js',
                    'extend' => false,
                ],
            ],
            default => [],
        };
    }
}
