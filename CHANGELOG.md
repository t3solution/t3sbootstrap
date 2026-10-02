# Changelog

Alle nennenswerten Änderungen an `EXT:t3sbootstrap`.

---

## 5.3.52 — Überzeile, Hintergrundvideo, Seitentitel, Sprungmarken, Masonry-Filter

Betrifft die folgenden Dateien:

```
ext_tables.sql
Classes/Domain/Model/Config.php
Classes/DataProcessing/ConfigProcessor.php
Classes/Utility/BackgroundImageUtility.php
Configuration/TCA/tx_t3sbootstrap_domain_model_config.php
Configuration/TCA/Overrides/tt_content_newCType.php
Configuration/TypoScript/Page/Template.typoscript
Configuration/Sets/T3sbootstrap/constants.typoscript
Configuration/Sets/T3sbootstrapImage/settings.definitions.yaml
Resources/Private/Language/locallang_db.xlf
Resources/Private/Language/de.locallang_db.xlf
Resources/Private/Partials/Content/Field.fluid.html
Resources/Private/Partials/Content/Header/All.fluid.html
Resources/Private/Partials/FluidStyledContent/Header/All.fluid.html
Resources/Private/Partials/Page/Jumbotron.fluid.html
Resources/Private/Partials/Page/Title.fluid.html
Resources/Private/Partials/Page/Breadcrumb.fluid.html
Resources/Private/Partials/Page/ExpandedContent/Top.fluid.html
Resources/Private/Partials/Page/ExpandedContent/Bottom.fluid.html
Resources/Private/Templates/Container/BackgroundWrapper.fluid.html
Resources/Private/Templates/Container/CollapsibleContainer.fluid.html
Resources/Private/Templates/Container/FourColumns.fluid.html
Resources/Private/Templates/Container/RowColumns.fluid.html
Resources/Private/Templates/Container/SixColumns.fluid.html
Resources/Private/Templates/Container/ThreeColumns.fluid.html
Resources/Private/Templates/Container/TwoColumns.fluid.html
ext_conf_template.txt
Classes/Command/Anchor.php
Classes/Service/AnchorService.php
Classes/Updates/AnchorUpgradeWizard.php
Classes/Helper/DefaultHelper.php
Classes/Wrapper/BackgroundWrapper.php
Classes/EventListener/AssetRenderer/IsInline.php
Configuration/TCA/Overrides/tt_content_container.php
Configuration/TypoScript/Content/_main.typoscript
Configuration/FlexForms/Container/BackgroundWrapper.xml
Resources/Private/Language/locallang_be.xlf
Resources/Private/Language/de.locallang_be.xlf
Resources/Private/Partials/MainAssets.fluid.html
Resources/Private/Partials/FunctionAssets.fluid.html
Resources/Private/Backend/ContainerPreview/**/two_columns/Preview.fluid.html
Classes/Wrapper/MasonryWrapper.php
Configuration/FlexForms/Container/MasonryWrapper.xml
Resources/Private/Templates/Container/MasonryWrapper.fluid.html
Resources/Public/JavaScript/MasonryFilter.js
Resources/Public/JavaScript/shuffle.mjs
Classes/DataProcessing/BootstrapProcessor.php
Configuration/TypoScript/Page/_main.typoscript
Configuration/TypoScript/Page/Register.typoscript
Configuration/TypoScript/Lib/ContentElement.typoscript
```

> **Nach dem Update: Datenbankanalyse erforderlich.** Es kommen fünf Spalten
> dazu — `tt_content.tx_t3sbootstrap_supraheader_class` sowie in
> `tx_t3sbootstrap_domain_model_config` die vier Felder `jumbotron_bgvideo`,
> `jumbotron_bgvideo_autoplay`, `jumbotron_bgvideo_loop` und
> `jumbotron_bgvideo_overlay`.
>
> ```
> vendor/bin/typo3 extension:setup
> vendor/bin/typo3 cache:flush
> ```
>
> Danach den Konfigurations-Datensatz einmal speichern: das schreibt die
> erzeugten TypoScript-Dateien neu, sonst fehlen die neuen Konstanten.

### Hintergrundvideo im Jumbotron

Ein lokales Video aus „Medien" kann jetzt als Jumbotron-Hintergrund laufen — im
gewöhnlichen Jumbotron mit Seitenverhältnis genauso wie in der Vollbild-Section.
Neu in der Konfiguration unter *Jumbotron → Background* die Palette
**Background Video** mit vier Feldern: Schalter, Autoplay, Endlosschleife und
Abdunklung in Prozent.

Ansätze dafür gab es schon, nur konnten sie nicht funktionieren.
`localFullHeightBgVideo` verlangte drei Dinge gleichzeitig — Quelle `page`, die
**erste** Datei in `pages.media` mit MIME `video/mp4` und zusätzlich
„Full height section" — und schrieb dann `src="{bgSlides.0}"`. Dort steht aber
das Ergebnis von `getJumbotronBgImage()`, also CSS und keine Datei-Adresse. Die
neue Erkennung sucht die erste Datei mit einem `video/`-Typ an beliebiger Stelle
der Liste und nimmt ihre öffentliche Adresse.

Auf jeder Ebene der Rootline hat das Video Vorrang vor dem Bild. Ohne diese
Reihenfolge erbte eine Seite mit Video das Bild ihres Vorfahren, weil die
Bildsuche zuerst fündig wurde. Online-Medien (YouTube, Vimeo) fallen von selbst
heraus: sie tragen in FAL keinen `video/`-Typ — und ein eingebettetes Video
lässt sich ohnehin nicht stumm unter einen Text legen.

Ohne Autoplay bekommt das Video Bedienelemente. Damit der Zeiger sie erreicht,
trägt der Wrapper dann `has-controls`; Links und Schaltflächen im Kopf bleiben
trotzdem anklickbar.

### Überzeile: eigene Klasse, und sie erscheint wieder zuverlässig

`tx_t3sbootstrap_supraheader_class` ist neu — dieselbe Auswahlliste wie bei der
Überschrift, als Kopie ihrer TCA-Definition statt als zweite gepflegte Liste.
Die Linien-Varianten (`h-line-*`) sind ausgenommen: sie sind auf die Größe einer
Überschrift abgestimmt. Text und Klasse stehen in einer Palette nebeneinander.

Zwei Fehler dazu:

* `Content/Field.fluid.html` gab Leerraum aus, wenn ein Feld leer war. In Fluid
  ist eine Zeichenkette aus Leerzeichen wahr — es entstand ein leeres
  `<p class="supraheader">`. Die Datei ist jetzt einzeilig, und die Werte laufen
  durch `f:format.trim`.
* Die Überzeile verschwand, sobald die Überschrift leer blieb. Sie steht aber
  oft allein über einem Element („Schritt 2 von 4" braucht nichts darunter).
  Sie zählt jetzt mit, wenn es um die Frage geht, ob es überhaupt einen Kopf
  gibt — in beiden Header-Partials und in den sieben Container-Templates, die
  ihre eigene Bedingung mitbringen.

### Seitentitel und Jumbotron-Hintergrund

* `page_titlecontainer` wurde nie ausgewertet. Der Wert wird jetzt im
  `ConfigProcessor` aufgelöst und in `Page/Title.fluid.html` angewendet; die
  Stellen, die den Titel bereits in einem Container rendern (Jumbotron,
  Breadcrumb, ExpandedContent), setzen `pageTitleSkipContainer` und vermeiden so
  den zweiten Container.
* `page_titlealign` kennt die Werte `right` und `left`; Bootstrap 5 schreibt
  `text-end` und `text-start`. Das wird jetzt abgebildet.
* Die Beschriftungen von `jumbotron_alignitem` waren irreführend: dort bedeutet
  `right` unten und `left` oben, weil `align-items-*` auf die Querachse wirkt.
* Der Jumbotron-Hintergrund nimmt nur noch Dateien, aus denen sich ein
  CSS-Hintergrundbild bauen lässt (`onlyImages()`); ein Video oder PDF in
  „Medien" ließ die Suche vorher auf der falschen Datei abbrechen. Der
  Bild-Slider entsteht nicht mehr, wenn es gar keine Bilder gibt.
* **Erweiterter Inhalt mit „content slide" blieb leer.** Ob oben oder unten
  überhaupt etwas auszugeben ist, entscheidet `hasContent()`. Geprüft wurde nur
  die aktuelle Seite — bei aktivem Slide liegt der Inhalt aber womöglich weiter
  oben in der Rootline. Der Bereich entfiel dann vollständig, obwohl geerbter
  Inhalt vorhanden war. Jetzt wird bei aktivem Slide die ganze Rootline geprüft.
* Neu: `jumbotronBgimageIgnoreDoktypes`. Seitentypen, deren „Medien" ein
  Teaserbild sind und kein Hintergrund, überschreiben den Bilderstapel des
  Rootbereichs nicht mehr. Neben der Konstante für Integratoren gibt es eine Liste, in die sich
  andere Extensions in ihrer `ext_localconf.php` eintragen können.

### Sprungmarken und Abstände

Der Sprung auf einen Anker landete zu hoch. Gerechnet wurde mit der Höhe der
fixierten Navbar — ein zusätzlich am oberen Rand klebender Breadcrumb blieb
unberücksichtigt. `t3sbMeasureStickyTop()` in `MainAssets.fluid.html` misst jetzt
die **tiefste Unterkante** aller oben fixierten Elemente (`position: fixed` oder
`sticky`), nicht die Summe ihrer Höhen: bei einer 70 px hohen Navbar und einem
Breadcrumb, der bei `top: 0` bis 123 px reicht, sind 123 px verdeckt, nicht 193.

Das Ergebnis liegt als `window.t3sbStickyTop` bereit und wird als
`scroll-padding-top` an `<html>` geschrieben. Damit gilt derselbe Wert für den
nativen Sprung, für `:target` und für `scrollIntoView()` — vorher rechnete jede
Stelle für sich. `t3sbScrollToAnchor()` in `FunctionAssets.fluid.html` nutzt ihn
ebenfalls und fällt auf die bloße Navbar-Höhe zurück, wenn `MainAssets` fehlt.
Der eigene Zuschlag kommt aus `sectionmenu_anchor_offset` der T3SB-Konfiguration;
die Beschreibung des Feldes ist entsprechend erweitert.

`content_margin_top` wirkte nur in der Hauptspalte. `DefaultHelper` prüft jetzt
`colPos = 0 OR colPos > 199` und lässt eine bereits vorhandene `m*-`-Klasse
unangetastet.

**Innerhalb eines Wrappers greift der Abstand nicht** — Hintergrund-Wrapper,
Accordion, Modal, Card-Wrapper und die übrigen aus `BootstrapProcessor::TX_CONTAINER`.
Ein Wrapper bringt seinen eigenen Innenabstand mit; das erste Element darin schöbe
sonst eine Lücke zwischen Wrapperkante und Inhalt. Die **Spalten-Container**
(`TX_CONTAINER_GRID`: 2, 3, 4, 6 Spalten, Row Columns) behalten ihn, dort stehen die
Elemente untereinander und brauchen den Rhythmus. Entschieden wird am `parentCType`,
den `getDefaults()` ohnehin übergeben bekommt.

Der Wrapper selbst bekommt den Abstand weiter — er steht in der Hauptspalte. Trägt er
eine Überschrift, geht er auf den **Header** statt auf die Section
(`marginTopOnHeader` aus `BackgroundWrapper`).

**Im Footer und im Jumbotron greift er ebenfalls nicht.** Jumbotron (`colPos 3`),
Footer-Spalte (`4`) und erweiterter Inhalt (`20`/`21`) fielen schon durch die Prüfung
`colPos = 0 OR colPos > 199` heraus. Der Footer als **eigene Seite** nicht — dessen
Elemente liegen in `colPos 0` wie auf jeder anderen Seite. `getDefaults()` bekommt
dafür jetzt `$containerConfig` übergeben und vergleicht `footerPid` mit der `pid` des
Elements; `getContainerClass()` hatte dieses Merkmal längst, nur kam es nie an.

### Background-Wrapper, Vorschau, Speaking ID

Neue Checkbox **Header inside** (`headerInside` im sDEF-Sheet von
`BackgroundWrapper.xml`): Überschrift und Überzeile liegen dann **in** der
`section.background-image` statt darüber. Der Kopf wird in `SectionInner` und
`SectionOverlayInner` ausgegeben, und die Zählbedingungen der Section
berücksichtigen ihn, damit sie nicht an einem leeren Spaltenzähler scheitern.

* **Container-Vorschau fehlte innerhalb eines Wrappers.** Die Vorschau-Templates
  sind über `page.tsconfig` als `templates.typo3/cms-backend.…` registriert und
  greifen nur im View, den TYPO3 selbst baut. `B13\Container\Backend\Preview\GridRenderer`
  zeichnet verschachtelte Raster mit einem eigenen View aus
  `getGridPartialPaths($CType)` — ohne Registrierung fällt der auf die Defaults
  von `EXT:container` zurück. Alle 21 Container hängen ihren Pfad jetzt per
  `addGridPartialPath()` an (anhängen statt setzen: Fluid sucht rückwärts, die
  Pfade von `EXT:backend` und `EXT:container` bleiben als Rückfall).
* **Inline-Assets: leere oder halbe Dateien.** `IsInline::inline2TempFile()`
  schrieb direkt an den Zielnamen. Brach der Schreibvorgang ab, blieb eine
  unvollständige Datei liegen und wurde nie erneuert, weil der Name aus dem
  md5 des Inhalts entsteht — gleicher Name, gleicher Inhalt. Geschrieben wird
  jetzt in eine temporäre Datei und atomar umbenannt; eine vorhandene Datei mit
  falscher Größe wird ersetzt.
* „EExtra-Class" im Backend — ein verirrtes `E` im Vorschau-Partial der
  Zwei-Spalten-Container.

**Speaking ID: der Schalter wirkte nur halb.** Die Extension-Option blendet das
Feld `tx_t3sbootstrap_anchor` im Formular ein und aus. Der Upgrade Wizard
*Generate the missing „Speaking ID"* füllte die Anker aber unabhängig davon, und
die Spalte blieb als Slug-Feld registriert, so dass Kopien den Anker des
Originals mitnahmen. Beides fragt die Option jetzt ab; bei ausgeschalteter Option
wird die Spalte gar nicht erst ins TCA aufgenommen.

Gespeicherte Anker bleiben erhalten und werden weiter ausgegeben — das ist
Absicht: die Kopplung an den Schalter hat in 5.3.51 bestehenden Onepage-Seiten
die Anker weggezogen, während das Sektionsmenü weiter darauf verlinkte. Zum
Leeren gibt es den neuen Modus:

```
vendor/bin/typo3 t3sbootstrap:anchor --mode clear            # Trockenlauf
vendor/bin/typo3 t3sbootstrap:anchor --mode clear --execute
```

Dazu: `SlugHelper` erzeugt für ein Element ohne Überschrift den Ersatzwert
`default-<md5>`. Der steht nicht mehr im Quelltext — `tt_content.stdWrap.prepend`
verwirft ihn in einem verschachtelten `stdWrap`, der vor `required` läuft.

### Kategorie-Filter im Masonry-Wrapper

Der Masonry-Wrapper kann seine Kacheln nach **Kategorie** filtern. Die Option
*Filter nach Kategorie (shuffle.js)* im FlexForm blendet über dem Raster eine
Schaltflächenreihe ein — eine je Kategorie, dazu „Alle".

Die Kategorien werden **von Hand ausgewählt** (`shuffleCategories`, Mehrfachauswahl
auf `sys_category`); ihre Reihenfolge ist die Reihenfolge der Schaltflächen. Beschriftet
wird die Reihe über `shuffleLabel` und `shuffleAllLabel`. Gelesen wird die Zuordnung
direkt aus `sys_category_record_mm` für die Kinder des Wrappers — eine gewählte
Kategorie ohne ein einziges Element dort wird weggelassen, eine Schaltfläche zu null
Treffern ist nur im Weg.

**shuffle.js ersetzt masonry.js**, solange der Filter aktiv ist: beide ordnen die Kacheln
an und würden um dieselben Positionen streiten. `masonry.pkgd` wird dann gar nicht
geladen.

Die Bibliothek liegt in der Extension (`Resources/Public/JavaScript/shuffle.mjs`) —
kein CDN-Eintrag und kein Integrity-Hash, der mitgepflegt werden will. Eingebunden wird nur `MasonryFilter.js`; shuffle.mjs steht
als Pfad in `data-t3sb-shuffle-src` und wird per `import()` nachgeladen, weil Shuffle 7
ausschließlich als ES-Modul vorliegt. Schlägt das fehl, filtern die Schaltflächen über
`hidden` weiter — ohne Animation, aber vollständig.

Die Filterwerte stehen in `data-t3sb-categories` an der Zelle und in
`data-t3sb-filter-value` an der Schaltfläche — eigenes CSS und eigene Skripte können
sich daran halten.

Fehlt die Konfiguration, sagt der Wrapper es im Frontend — keine Kategorie gewählt,
Wrapper leer oder kein Element mit einer der gewählten Kategorien. Lautlos verschwinden
ist genau die Art Verhalten, die man später lange sucht.

### Behoben — TypoScript-Conditions warfen Syntaxfehler

Im Log stand bei jedem Aufruf:

```
TypoScript condition [traverse(site("configuration"), "settings/bootstrap/disable/jquery")
== false ||  == 0] could not be parsed: Unexpected token "operator" of value "=="
```

Die Konstante war nicht unauflösbar, sie löste sich zu einem **Leerstring** auf.
`bootstrap.disable.jquery` ist in `settings.definitions.yaml` als `type: bool`
deklariert, und Site-Settings behalten beim Flatten ihren PHP-Typ. Der
Condition-Substitutor setzt den Wert per String-Cast ein: `true` wird zu `1`,
`false` zu `` — und ungequotet bleibt dann `|| == 0` stehen.

Der Fehler trat also genau dann auf, wenn jemand jQuery **einschaltete**.

Schwerer als das Log-Rauschen wog die Folge: der Core fängt den `SyntaxError`
und setzt das Verdikt auf `false`. Damit fiel der gesamte Block weg — auch der
`traverse()`-Teil, der korrekt `true` geliefert hätte. **jQuery ließ sich nicht
aktivieren.**

Die Konstante steht jetzt in Anführungszeichen; `"" == "0"` ist eine gültige,
falsche Aussage statt eines Parse-Fehlers, und `traverse()` entscheidet wieder.

Vier weitere Conditions derselben Bauart sind mitgezogen —
`backgroundImageEnable`, `lightboxSelection` (zweimal) und `ext.news` (zweimal).
Das sind generierte TypoScript-Konstanten aus `t3sbconstants.typoscript`, also
immer Strings und im Normalbetrieb unkritisch. Fehlt diese Datei aber — frische
Installation, kein Konfigurations-Datensatz, CLI —, bleibt `{$…}` als Literal
stehen und es knallt genauso.

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
