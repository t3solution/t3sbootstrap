# Changelog

Alle nennenswerten Änderungen an `EXT:t3sbootstrap`.

---

## 5.3.51 — nachträglich: Navbar, RTE-Badges und Lightbox

Version unverändert, betrifft die folgenden Dateien:

```
ext_tables.sql
ext_conf_template.txt
Classes/Domain/Model/Config.php
Classes/DataProcessing/ConfigProcessor.php
Classes/Backend/Hooks/OutsourcedFiles.php
Classes/Backend/EventListener/RTE/FeatureToggles.php
Classes/UserFunction/ExtensionConfigurationInfo.php
Classes/Updates/NavbarBrandImageUpgradeWizard.php
Classes/Updates/RteBadgeSettingUpgradeWizard.php
Configuration/RTE/Default.yaml
Configuration/TypoScript/Page/_main.typoscript
Configuration/TCA/tx_t3sbootstrap_domain_model_config.php
Configuration/Sets/T3sbootstrap/constants.typoscript
Configuration/Sets/T3sbootstrapOptional/settings.definitions.yaml
Resources/Private/Language/locallang_db.xlf
Resources/Private/Language/de.locallang_db.xlf
Configuration/TypoScript/Lib/ContentElement.typoscript
Resources/Private/Partials/Content/Media/Type/Image.fluid.html
Resources/Private/Partials/Page/Jumbotron.fluid.html
Resources/Private/Partials/Page/Navbar/Navbar.fluid.html
Resources/Private/Partials/Page/Navbar/Plusicon.fluid.html
Resources/Public/JavaScript/ESM/rte_ckeditor/badge-plugin.js
Resources/Public/Prism/prism.js
Resources/Public/Prism/*.css
Resources/Public/Prism/README.md
```

> **Nach dem Update: Datenbankanalyse erforderlich.** Diese Änderung bringt drei
> neue Spalten in `tx_t3sbootstrap_domain_model_config` mit:
> `navbar_image_width`, `navbar_image_height`, `navbar_image_alt`. Ohne die
> Analyse lässt sich der Konfigurations-Datensatz nicht speichern.
>
> ```
> vendor/bin/typo3 extension:setup
> vendor/bin/typo3 cache:flush
> ```
>
> Alternativ im Install-Tool: *Analyze Database Structure*. Danach optional der
> UpgradeWizard **„Move the navbar brand image settings into the configuration
> record"** — er ist Bequemlichkeit, keine Voraussetzung.

### Brand-Logo: Maße und Alt-Text in den Konfigurations-Datensatz

Breite, Höhe und Alt-Text des Navbar-Logos lagen bisher **ausschließlich** in den
Site-Settings (`bootstrap.navbar.image.width` / `.height` / `.altText`), der Pfad
dagegen wahlweise im Datensatz (`navbar_image`) oder in der Site
(`bootstrap.navbar.image.defaultPath`). Diese Asymmetrie hatte eine unangenehme
Folge: Wer im Datensatz ein abweichendes Logo eintrug, bekam trotzdem die Maße
und den Alt-Text des Site-Logos verpasst — bei mehreren Siteroots je Site war ein
eigenes Logo damit nicht sauber einstellbar.

- Drei neue Felder im Tab **Brand (Logo)**: `navbar_image_width`,
  `navbar_image_height`, `navbar_image_alt`. Sie erscheinen nur, wenn unter
  „Brand Options" ein Logo gewählt ist (`displayCond` auf `image` / `imgText`)
- Für alle vier Werte gilt jetzt dieselbe Rangfolge wie zuvor schon für den Pfad:
  **Datensatz gewinnt, Site-Setting ist Rückfall.** Leer bzw. `0` heißt „nimm den
  Wert aus der Site"
- `Navbar.fluid.html` liest Maße und Alt-Text aus `config.navbar.imageWidth`,
  `…imageHeight` und `…imageAlt` statt aus `settings.navbar.image.*`
- Neuer `NavbarBrandImageUpgradeWizard` holt alle vier Werte je Siteroot aus der
  Site-Konfiguration in den Datensatz — nur dort, wo im Datensatz noch nichts
  steht, und nur wo die Site überhaupt einen Wert trägt. Der Pfad wandert
  mit, obwohl es `navbar_image` schon vorher gab: solange er nur in der Site
  steht, bleibt das Logo über zwei Orte verteilt und die Site-Settings lassen
  sich nicht abräumen
- Die vier Site-Settings **bleiben bestehen**, tragen im Settings-Editor aber
  jetzt `DEPRECATED — …` im Label. Sie zu entfernen würde jede Installation, die
  dort einen Logo-Pfad gesetzt hat, beim Update auf das Bootstrap-Logo
  zurückwerfen. Ohne den Wizard ändert sich an der Ausgabe deshalb nichts

Neue Methode `OutsourcedFiles::rewriteFiles(int $rootPageId)`: Der Wizard schreibt
per QueryBuilder direkt in die Tabelle, also an DataHandler und Frontend-Middleware
vorbei. `ensureFilesExist()` greift aber nur bei *fehlenden* Dateien, der
DataHandler-Hook nur beim Speichern im Backend — die abgeleiteten Konstanten wären
hinter der Datenbank zurückgeblieben und der Wizard damit still wirkungslos
gewesen.

### Neu — RTE: Prism-Themes liegen jetzt in der Extension

Die acht Theme-Ordner unter `Resources/Public/Prism/` gehören ab jetzt zum
Auslieferungsumfang — vorher verwies `ext_conf_template.txt` auf Verzeichnisse,
die niemand hatte. Neu dazugekommen ist **Funky**, damit sind alle acht Themes
verfügbar, die der Downloader auf prismjs.com anbietet.

Jedes Bundle ist PrismJS 1.30.0 mit denselben zwölf Sprachen:

```
markup css clike javascript bash json markup-templating php scss sql typoscript yaml
```

Abweichend vom Downloader liegt die **`prism.js` nur einmal** da, nicht in jedem
Theme-Ordner: sie hängt allein an der Sprachauswahl, nicht am Theme, und war in
allen acht Paketen byte-gleich. Acht Kopien derselben 39 KB wären knapp 300 KB
ohne Gegenwert gewesen. Das Verzeichnis ist deshalb flach, und der Dateiname
eines Themes ist genau der Wert, den die Extension-Konfiguration speichert:

```
Resources/Public/Prism/prism.js
Resources/Public/Prism/Default.css   Dark.css   Coy.css   Funky.css
Resources/Public/Prism/Okaidia.css   SolarizedLight.css   TomorrowNight.css   Twilight.css
```

Im TypoScript lädt jetzt eine Bedingung die Engine — `not in ['0', '1', '']`,
also „irgendein Theme ist gewählt" —, und acht kurze Blöcke wählen das
Stylesheet. Ein `Resources/Public/Prism/README.md` beschreibt, wie die Bundles
gebaut werden, damit das nächste Theme keine Archäologie erfordert.

Eine Feinheit beim Nachbauen aus dem npm-Paket: die minifizierte
`prism-funky.css` bringt als einzige noch das CSS des Diff-Highlight-Plugins mit,
das hier nichts zu suchen hat (kein Plugin ausgewählt) — es ist entfernt.

### Neu — RTE: Badges als eigenes Toolbar-Dropdown

Die 16 Einträge „Badge \*" und „Pill Badge \*" sind aus dem Styles-Dropdown
verschwunden und stecken jetzt in einem eigenen Toolbar-Eintrag **Badge** —
ein Dropdown für beides, oben die acht normalen Badges, unten die acht Pills,
darüber „No badge" zum Entfernen. Damit schrumpft das Styles-Dropdown von 57 auf
41 Einträge.

**Das Markup ist unverändert** — `<span class="badge text-bg-primary">` bzw. mit
zusätzlichem `rounded-pill`. Bestehende Inhalte brauchen deshalb keine
Migration: sie werden beim Öffnen erkannt, sind über das neue Dropdown änderbar
und werden unverändert zurückgeschrieben.

Neues Plugin `Resources/Public/JavaScript/ESM/rte_ckeditor/badge-plugin.js`. Es
ist ein echtes Editor-Feature statt zweier Style-Gruppen, und das hat einen
handfesten Grund: beim Styles-Feature hängt das Überleben der Klassen daran,
dass der Eintrag im Dropdown steht — schaltet man die Gruppe ab, müssen die
Klassen erst in die HTML-Whitelist gerettet werden. Hier stehen sie im Schema und
werden in beide Richtungen konvertiert, überleben also auch in Presets, deren
General-HTML-Support-Whitelist nur `<div>` abdeckt.

Eine Feinheit beim Upcast: Die Pill-Varianten sind mit höherer Priorität
registriert als die normalen. Ein `<span class="badge rounded-pill text-bg-primary">`
trägt auch die beiden Klassen, auf die der einfache Matcher passt — ohne die
Reihenfolge würde aus jeder Pill ein normales Badge und `rounded-pill` bliebe als
Rest für den General HTML Support übrig.

#### Umbenannte Einstellung

`rteStyleBadges` → **`rteBadge`** (Page-TSconfig: `styleBadges` → `badge`).

Der alte Schlüssel **gewinnt weiterhin, solange er in der gespeicherten
Konfiguration steht.** Das ist bewusst andersherum als es klingt:
`synchronizeExtConfTemplateWithLocalConfiguration()` trägt beim Update jeden
neuen Schlüssel aus `ext_conf_template.txt` mit seinem Vorgabewert ein — nach dem
Update steht dort also `rteBadge = 1`, ohne dass jemand das entschieden hätte.
Würde der neue Schlüssel gewinnen, wäre die alte Entscheidung still
überschrieben.

Der alte Schlüssel verschwindet auf zwei Wegen: durch den neuen
`RteBadgeSettingUpgradeWizard`, oder sobald das Formular der
Extension-Konfiguration einmal gespeichert wird — der Install-Tool-Controller
ersetzt die gesamte Konfiguration durch das, was das aus `ext_conf_template.txt`
gebaute Formular abschickt.

`FeatureToggles` kennt dafür zwei neue Schlüssel je Feature, `legacyExtconf` und
`legacyFeature`, die auch für künftige Umbenennungen taugen.

### Behoben — Background-Wrapper im Jumbotron ohne Container blieb unsichtbar

Ein Background-Wrapper mit lokalem Video im Jumbotron wurde nicht angezeigt, sobald
für den Jumbotron **kein Container** gewählt war. Kein Fehler in der Konsole, kein
leerer Bereich — die Sektion war schlicht **0 Pixel breit**.

Die Kette in `Page/Jumbotron.fluid.html`: `.jumbotron` ist ein Flex-Container, der
Wrapper mit `alignItem` darin also ein Flex-Item **ohne eigene Breite** — er
schrumpft auf seinen Inhalt. Darunter hängt `jumbotron-content.w-100`, dessen
100 % sich auf eine Breite beziehen, die es zu dem Zeitpunkt noch nicht gibt. Und
das einzige, was Breite beisteuern könnte, ist beim lokalen Video die `figure` —
die Bootstrap über `.ratio` **absolut positioniert**, womit sie zur Breite des
Elternteils nichts beiträgt. Alles rechnet sich auf 0 herunter.

Sichtbar wird das nur ohne Container, weil sonst der Container-`<div>` die Breite
setzt. Behoben mit `w-100` an diesem einen Wrapper — genau so, wie es die zweite
Variante weiter oben im selben Partial schon macht.

Am Live-Objekt gemessen und gegengeprüft: Sektion und Video gehen von 0 × 0 auf
1280 × 311 bzw. 1280 × 720, die Höhe entspricht dem eingestellten
Seitenverhältnis 37:9.

### Behoben — Lightbox ignorierte jeden Bildzuschnitt

`tx_t3sbootstrap_zoom_orig` („Originalbild für Lightbox verwenden") war ohne
Wirkung — und zwar, weil das, was die Option abschalten soll, ohnehin nie
stattfand.

In `lib.contentElement.settings.media.popup` stand `crop =`: eine gesetzte, aber
leere Eigenschaft. `ContentObjectRenderer::getCropAreaFromFileReference()` prüft
mit `isset($fileArray['crop'])`, ob TypoScript den Zuschnitt vorgibt — **nicht,
ob dort etwas drinsteht**. Ein leeres `crop =` genügt also, damit der Zuschnitt
der Dateireferenz übersprungen wird. Die Lightbox zeigte immer das ungeschnittene
Bild, ganz gleich was im Bildeditor eingestellt war und ganz gleich, wie die
Option stand.

Ersetzt durch `crop.data = file:current:crop` — die Schreibweise, die
EXT:fluid_styled_content selbst verwendet. Der `ClickEnlarge`-ViewHelper setzt
die zu öffnende Datei per `setCurrentFile()`, damit entscheidet die Option jetzt
wieder selbst:

- **aus** → die Dateireferenz, also mit ihrem Zuschnitt
- **an** → das Originalbild; ein `File` trägt keine `crop`-Eigenschaft, der
  Zuschnitt entfällt

> **Sichtbare Änderung:** Wer Bilder zuschneidet und die Lightbox nutzt, bekommt
> ab jetzt den Zuschnitt auch in der Lightbox zu sehen — bisher öffnete sie immer
> das volle Bild. Wer das so beibehalten will, aktiviert je Element
> „Originalbild für Lightbox verwenden".

Dazu in `Media/Type/Image.fluid.html` ein Rückfall: `originalFile` gibt es nur auf
einer Dateireferenz. Kommen die Bilder aus einer Dateisammlung oder einem Ordner,
liefert der `GalleryProcessor` blanke `File`-Objekte — der Ausdruck blieb leer und
der ViewHelper bekam `null` für ein Pflichtargument.

### Behoben — Navbar mit Plus-Icon

Bei aktivem `navbar_plusicon` rutschte der Plus-Button auf eine eigene Zeile unter
den Menüpunkt, blieb ungestylt und trug zusätzlich den Bootstrap-Caret (`+▾`).

Ursache war ein Fluid-Ausdruck in `Plusicon.fluid.html`:

```
@media (min-width: {settings.navbar.{settings.config.navbarBreakpointWidth}}px)
```

`settings.config.navbarBreakpointWidth` ist bereits der **Pixelwert** (bei „lg"
also 992), gesucht wurde damit `settings.navbar.992` — ein Schlüssel, den es nicht
gibt; `settings.navbar` hält `sm`/`md`/`lg`/`xl`/`xxl`. Heraus kam
`@media (min-width: px)`, eine ungültige At-Regel, die jeder Browser samt ihrem
**gesamten Inhalt** verwirft. Damit fiel die komplette Desktop-Darstellung des
Plus-Icons weg — inklusive der Regel, die den Caret ausblendet.

Die Bedingung nimmt jetzt den Breakpoint-Schlüssel (`navbarBreakpoint`) und fällt
auf 768 zurück, falls der Breakpoint auf „no" steht.

---

## 5.3.51

### Neu — RTE

- Toolbar-Item **Columns**: 2, 3 oder 4 Bootstrap-Spalten direkt im Editor
- Toolbar-Item **Margin**: Dropdown für `mt-1…mt-5` / `mb-1…mb-5`, oben und unten
  unabhängig voneinander
- **Code Block** (`<pre><code>`) mit 13 Sprachen inkl. TypoScript, YAML und SCSS,
  dazu **Inline-Code** für einzelne Wörter im Satz
- **18 Feature-Schalter** in der Extension-Konfiguration, Kategorie „RTE", pro
  Seitenbaum übersteuerbar per Page-TSconfig:

  ```
  RTE.t3sbootstrap.features {
      columns = 0
      codeBlock = 0
  }
  ```

  Ein abgeschalteter Schalter blendet nur den Toolbar-Eintrag aus. Das CKEditor-
  Plugin bleibt geladen, bestehende Inhalte bleiben editierbar und werden beim
  Speichern nicht verändert.
- Zweites Preset **`t3sbootstrap_extended`**: gleicher Editor, aber die General-
  HTML-Support-Whitelist behält CSS-Klassen auf den üblichen Textelementen.
  Opt-in, weil die Whitelist zugleich als Paste-Filter dient — zu aktivieren mit
  `RTE.default.preset = t3sbootstrap_extended`
- Enter bzw. Backspace steigt aus Alert- und Spalten-Blöcken aus, statt am Ende
  des Feldes in einer Sackgasse zu landen

### Neu — sonstiges

- Option **Speaking ID**: blendet das Feld „Anchor" (`tx_t3sbootstrap_anchor`)
  im Backend ein. Die Option steuert ausschließlich die Sichtbarkeit des Feldes —
  gerendert wird der Anchor-`<span>` unverändert für jedes Element, das im
  Section-Index steht und einen Wert im Feld hat, also auch bei abgeschalteter
  Option. Andernfalls verlören bestehende Seiten ihre Anker, während die
  Section-Menü-Links der Navbar weiter darauf zeigen (One-Page-Layout)
- `icon-link` und `icon-link icon-link-hover` sind im Link-Wizard wählbar. Die
  Hover-Animation läuft jetzt auch mit EXT:iconpack (`<i>`, `<svg>`, `<img>`,
  `[aria-hidden="true"]`), nicht nur mit Bootstraps eigenem `.bi`
- Extension-Konfiguration: sprechende Tab-Labels statt kleingeschriebener
  Kategorienamen; `flexformDir` von „Basic" nach „FlexForm" verschoben

### Behoben

- `.t3sbs-anchor` liegt jetzt `position: absolute`. Innerhalb einer `.row` war
  der Span ein Geschwister der Spalten, wurde von `row-cols-*` und dem CSS-Grid
  als Spalte mitgezählt und verschob das Layout um eins

---

## 5.3.51 — nachträglich eingespielte Bugfixes

Version unverändert, betrifft die folgenden Dateien:

```
Classes/Wrapper/BackgroundWrapper.php
Classes/DataProcessing/GalleryProcessor.php
Classes/ViewHelpers/Backend/InfoViewHelper.php
Classes/Controller/ConfigController.php
Classes/Command/CdnToLocal.php
Classes/Command/CustomScss.php
Classes/Updates/IconpackTitleUpgradeWizard.php
Classes/Updates/IconpackHeaderUpgradeWizard.php
Resources/Private/Language/locallang.xlf
Resources/Private/Language/de.locallang.xlf
```

### Fatal Errors im Frontend

- **Background-Wrapper mit lokalem Video:** `trim(null)` beim ersten Speichern.
  Das ganze `localvideo`-Sheet hängt an `displayCond → isLocalVideo`, und diese
  Bedingung prüft die bereits gespeicherte assets-Relation. Beim ersten Speichern
  wurden die Felder daher nie gerendert und nie geschrieben. Betrifft ebenso
  `loop`, `mute`, `shift`, `alignVideoItem` und `localControls`
- **Galerie aus Dateisammlung oder Ordner:** `floor(null)`, weil `$mediaWidth`
  nur innerhalb eines `instanceof FileReference` gesetzt wurde — Dateisammlungen
  liefern aber `File`. Die Prüfung deckt jetzt `FileInterface` ab
- **Masonry-Wrapper ohne FlexForm:** `strpos(null, …)` in `getMansoryColumns()`

### Falsche Ausgabe

- **Seitenverhältnis im Background-Wrapper war invertiert.** Der Value Picker des
  FlexForms speichert Höhe zuerst (`37by9` hat den Wert `9/37`), die Formel
  rechnete aber Breite/Höhe — aus 24,3 % wurden 411 %. `resolveAspectRatio()`
  normalisiert jetzt alle Schreibweisen (`9/37`, `37/9`, `16/9`, `37:9`, `37by9`,
  `37x9`) auf dasselbe `WxH` wie `ConfigProcessor` und `BootstrapProcessor`

### Sicherheit

- **Stored XSS im Seitenmodul.** `InfoViewHelper` hat `$escapeOutput = false`, die
  Ausgabe läuft durch `f:format.raw()`, und „Extra Class" / „Header Extra Class"
  sind freie Eingabefelder. Alle aus einem Record stammenden Werte werden jetzt
  mit `htmlspecialchars()` behandelt
- **Config-Export und -Import ohne Rechteprüfung.** Das Modul ist mit
  `access => user` registriert, die PID kam ungeprüft aus der URL. Jetzt Prüfung
  über `doesUserHaveAccess()` (`PAGE_SHOW` für Export, `PAGE_EDIT` für Import),
  Voll-Export nur für Admins, und die Siteroot-Auswahl zeigt nur erlaubte Seiten

### Datenverlust in den CLI-Commands

- **`t3sbootstrap:cdnToLocal`** löschte die Zieldatei vor dem Download und schrieb
  bei einem 404 den `false`-Rückgabewert von `getURL()` weiter. Jetzt wird zuerst
  heruntergeladen, dann über eine temporäre Datei atomar getauscht; ein
  fehlgeschlagener Download liefert `Command::FAILURE`. Fehlt `ext-zip`, werden
  die lokalen Google-Fonts nicht mehr gelöscht, sondern nur gewarnt
- **`t3sbootstrap:customScss`** löschte die Bootstrap-Quellen vor dem Download —
  bei falscher Version oder nicht erreichbarem GitHub blieb nichts übrig und der
  SCSS-Compiler nahm das Frontend mit einem fehlenden `@import` mit. Jetzt
  Staging-Verzeichnis mit Rückfallebene. Zusätzlich: Null-Prüfung auf den
  Config-Datensatz und auf den Bootswatch-Download
- **Iconpack-UpgradeWizards** schrieben ein bloßes `fa7:` und leerten das
  Quellfeld, wenn kein Icon-Name erkannt wurde, und überschrieben bereits
  gesetzte Icons. Die Erkennung nimmt jetzt den ersten `fa-*`-Token, der kein
  Style, Modifier oder Größenkürzel ist; ohne Treffer bleibt der Datensatz
  unangetastet

---

## 5.3.50

Siehe Release-Historie.
