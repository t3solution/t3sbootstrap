# TYPO3 Extension `t3sbootstrap`

[![Latest Stable Version](https://poser.pugx.org/t3sbs/t3sbootstrap/v/stable)](https://packagist.org/packages/t3sbs/t3sbootstrap)
[![Monthly Downloads](https://poser.pugx.org/t3sbs/t3sbootstrap/d/monthly)](https://packagist.org/packages/t3sbs/t3sbootstrap)
[![Total Downloads](https://poser.pugx.org/t3sbs/t3sbootstrap/downloads)](https://packagist.org/packages/t3sbs/t3sbootstrap)
[![License](https://poser.pugx.org/t3sbs/t3sbootstrap/license)](https://packagist.org/packages/t3sbs/t3sbootstrap)
[![Donate](https://img.shields.io/badge/Donate-PayPal-green.svg)](https://www.paypal.me/t3sbootstrap)

A startup extension for TYPO3 that brings the full power of **Bootstrap 5** — classes, components, layouts and utilities — directly into the TYPO3 backend, **out of the box**.

Editors get rich, ready-to-use Bootstrap content elements. Integrators get a clean, configurable foundation so they can stop reinventing the wheel for every project.

🌐 **Live demos, documentation & tutorials:** <https://www.t3sbootstrap.de/>

---

## Table of Contents

- [Highlights](#highlights)
- [Requirements](#requirements)
- [Installation](#installation)
  - [Via Composer (recommended)](#via-composer-recommended)
  - [Via TYPO3 Extension Repository (TER)](#via-typo3-extension-repository-ter)
- [Setup after installation](#setup-after-installation)
- [Configuration](#configuration)
- [The t3sbootstrap ecosystem](#the-t3sbootstrap-ecosystem)
- [Documentation & Demos](#documentation--demos)
- [Support the project](#support-the-project)
- [License](#license)

---

## Highlights

- **Bootstrap 5 out of the box** — all components, utilities and grid classes ready to use
- **Rich content elements** — extended FSC elements plus Bootstrap-specific ones (cards, carousels, accordions, tabs, modals, …)
- **Backend configuration module** — global and per-page settings, no TypoScript required for the basics
- **Container-based layouts** — built on `EXT:container` for clean, structured content
- **Site Sets support** — uses the modern TYPO3 v13+ Site Settings approach (recommended over legacy "static templates")
- **Dark mode, breakpoints, utility colors** — configurable from the backend module
- **CDN by default, local assets optional** — load CSS/JS from CDN or move them into your own site package
- **Extendable via companion extensions** — sitepackage, swiper slider, iconpack and more (see [ecosystem](#the-t3sbootstrap-ecosystem))

## Requirements

| Component         | Version            |
|-------------------|--------------------|
| TYPO3             | `>= 14.3` |
| `container`       | required |
| PHP               | as required by your TYPO3 version |

> **Always check the latest requirements** in `composer.json` of the [current release](https://github.com/t3solution/t3sbootstrap/releases) — supported TYPO3 versions evolve with each major release.

## Installation

### Via Composer (recommended)

In your Composer-based TYPO3 project root, run:

```bash
composer require t3sbs/t3sbootstrap
```

Then activate the extension and flush the caches:

```bash
vendor/bin/typo3 extension:setup
vendor/bin/typo3 cache:flush
```

### Via TYPO3 Extension Repository (TER)

1. Install the required dependencies first: [`container`](https://extensions.typo3.org/extension/container) and [`content_defender`](https://extensions.typo3.org/extension/content_defender).
2. Download and install `t3sbootstrap` via the **Extension Manager** in the TYPO3 backend.
3. Flush all caches.

## Setup after installation

After installation, complete these steps to get a working frontend:

1. **Site Settings (TYPO3 v13+, recommended)**
   Include the **"T3S Bootstrap - MAIN SETTINGS"** in your site configuration. Also include **"Fluid Styled Content"** there and remove it from the legacy *Static Template* include.
   👉 More info: <https://www.t3sbootstrap.de/dokumentation/installation>

2. **Configuration module**
   Open the **"T3sb"** backend module and configure global defaults (navbar, footer, breakpoints, colors, etc.) for your site.

3. **Assets: CDN vs. local**
   By default, Bootstrap CSS & JS are loaded via CDN. For production it's recommended to serve them locally — either from `fileadmin/` or, ideally, from the companion site package `EXT:t3sb_package`.

4. **Scheduler task (optional but recommended)**
   When using a local site package, enable the corresponding Scheduler task (Console command) to keep generated assets in sync.

## Configuration

`t3sbootstrap` offers a wide range of options via **Extension Configuration** (Settings → Extension Configuration → `t3sbootstrap`), TypoScript, and the website settings, as well as in the backend module

For the full list, see the [official documentation](https://www.t3sbootstrap.de/dokumentation).

## The t3sbootstrap ecosystem

`t3sbootstrap` is the core, but it plays nicely with a family of companion extensions:

| Extension          | Purpose                                                                 |
|--------------------|-------------------------------------------------------------------------|
| `t3sb_package`     | Recommended **site package** to host local assets, custom CSS/SCSS, FontAwesome Pro, etc. |
| `t3s_swiper`       | [Swiper slider](https://github.com/t3solution/t3s_swiper) content element, based on Content Blocks |
| `iconpack` / `iconpack_fontawesome` | Icon picker integration (replacement for `rte_ckeditor_fontawesome`) |

## Documentation & Demos

- **Main site (docs + live demos):** <https://www.t3sbootstrap.de/>
- **Installation guide:** <https://www.t3sbootstrap.de/dokumentation/installation>
- **Packagist:** <https://packagist.org/packages/t3sbs/t3sbootstrap>
- **GitHub:** <https://github.com/t3solution/t3sbootstrap>
- **Issues & feature requests:** <https://github.com/t3solution/t3sbootstrap/issues>

## Support the project

`t3sbootstrap` is developed and maintained in spare time. If you use it in commercial projects or simply find it useful, please consider supporting its development:

[![Donate](https://img.shields.io/badge/Donate-PayPal-green.svg)](https://www.paypal.me/t3sbootstrap)

Bug reports, pull requests and constructive feedback are very welcome.

## License

This extension is released under the **GNU General Public License v2.0 or later** (GPL-2.0-or-later), in line with the TYPO3 Core. See [LICENSE.txt](LICENSE.txt) for details.

Bootstrap itself is © the Bootstrap Authors and released under the MIT license.
