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
- **CDN by default, local assets optional** — load CSS/JS from CDN or serve them locally from `typo3temp/assets/`
- **Self-contained** — no companion site package required; generated files are restored from the database when missing
- **Extendable via companion extensions** — theme builder, swiper slider, iconpack and more (see [ecosystem](#the-t3sbootstrap-ecosystem))

## Requirements

| Component        | Version   |
|------------------|-----------|
| TYPO3            | `^14.3`   |
| PHP              | `>= 8.2`  |
| `b13/container`  | `^4.1`    |

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

1. Install the required dependency first: [`container`](https://extensions.typo3.org/extension/container).
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
   By default, Bootstrap CSS & JS are loaded via CDN. For production, serve them locally: run the
   Scheduler task **"T3SB CDN to local"** (`vendor/bin/typo3 t3sbootstrap:cdnToLocal`), then switch
   off *Enable CDN* in the site settings. The files end up in `typo3temp/assets/t3sbootstrap/`.

4. **Custom SCSS (optional)**
   With *Enable CDN* switched off you can activate *Enable Custom SCSS* and run
   `vendor/bin/typo3 t3sbootstrap:customScss` with the root page ID. Bootstrap variables and your
   own SCSS are then editable in the T3SB backend module.

> **Deployments:** the CDN downloads are the only files that cannot be rebuilt from the database.
> If `typo3temp/` is not carried over between releases, run `t3sbootstrap:cdnToLocal` again after
> each deployment. The backend module points this out when local delivery is configured and the
> files are missing.

## Configuration

`t3sbootstrap` offers a wide range of options via **Extension Configuration** (Settings → Extension Configuration → `t3sbootstrap`), TypoScript, and the website settings, as well as in the backend module

For the full list, see the [official documentation](https://www.t3sbootstrap.de/dokumentation).

### RTE feature switches

Which items the t3sbootstrap RTE offers is configurable — no forked YAML preset needed.

**Installation-wide:** Settings → Extension Configuration → `t3sbootstrap` → category **RTE**.
One switch per toolbar item (`rteAlert`, `rteColumns`, `rteMargins`, `rteBadge`, `rteColor`,
`rteCodeBlock`, `rteTable`, `rteSourceEditing`, …) and one per remaining group of the Styles
dropdown (`rteStyleButtons`, `rteStyleTables`). Everything is on by default, so an existing
installation sees no change.

**Per page tree:** Page TSconfig wins over the Extension Configuration.

```typoscript
RTE.t3sbootstrap.features {
    columns = 0
    sourceEditing = 0
    color = 0
}
```

Switching a feature off hides the button — it does not touch content. The CKEditor plugin stays
loaded, so existing alerts, columns and code blocks remain editable and are written back
unchanged. Style groups behave the same way: their element/class pairs move to the General HTML
Support whitelist before the entry leaves the dropdown, so classes already set in the content
survive the round trip.

## The t3sbootstrap ecosystem

`t3sbootstrap` is the core, but it plays nicely with a family of companion extensions:

| Extension          | Purpose                                                                 |
|--------------------|-------------------------------------------------------------------------|
| `t3sbootstrap_builder` | [Visual Bootstrap 5.3 theme builder](https://github.com/t3solution/t3sbootstrap_builder) as a backend module — around 200 variables with live preview. Version 1.0.2 requires t3sbootstrap `>= 5.3.50`. |
| `t3s_swiper`       | [Swiper slider](https://github.com/t3solution/t3s_swiper) content element, based on Content Blocks |
| `iconpack` / `iconpack_fontawesome` | Icon picker integration (replacement for `rte_ckeditor_fontawesome`) |

> `t3sb_package` is **no longer needed** as of 5.3.50. Existing installations can keep it
> installed; it is simply not written to any more.

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

## Author & Commercial Support

t3sbootstrap is developed and maintained by **Helmut Hackbarth** — freelance
TYPO3 developer at [t3solution](https://www.t3solution.de/).

## License

This extension is released under the **GNU General Public License v2.0 or later** (GPL-2.0-or-later), in line with the TYPO3 Core. See [LICENSE.txt](LICENSE.txt) for details.

Bootstrap itself is © the Bootstrap Authors and released under the MIT license.
