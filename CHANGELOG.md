# Changelog

Alle nennenswerten Änderungen an `EXT:t3sbootstrap`.

---

## 5.3.56 — Klassen im RTE, Vererbung des Backend-Layouts

Betrifft die folgenden Dateien:

```
CHANGELOG.md
composer.json
ext_conf_template.txt
ext_emconf.php
releaseNote.pdf
Classes/Backend/EventListener/RTE/FeatureToggles.php
Configuration/RTE/Default.yaml
Configuration/TSConfig/Page.tsconfig
Resources/Public/JavaScript/ESM/rte_ckeditor/classes-plugin.js   (neu)
```

### Neu — Werkzeug „Klassen" in der Toolbar

Das Spalten-Werkzeug hebt seit jeher jede Klasse auf, die es nicht selbst setzt,
und schreibt sie unverändert zurück — eintragen ließ sich eine aber nur in der
Quelltextansicht. Das neue Werkzeug ist der Weg dorthin: ein Dropdown hinter
`Spalten`, zwei Gruppen für die Zeile und die einzelne Spalte, jeder Eintrag ein
Schalter. Geschrieben wird in dasselbe Modellattribut `extraClasses`; es entsteht
kein neues Element und kein zusätzliches Markup.

Außerhalb einer Spalten-Zeile ist der Knopf aus, der Tooltip sagt warum und nennt
sonst die gesetzten Klassen. Das Panel bleibt nach einem Schalter offen, weil
mehrere Klassen fast immer zusammen gesetzt werden. Einträge mit demselben
`exclusive`-Schlüssel ersetzen sich gegenseitig, so verdrängt `g-0` ein gesetztes
`g-3`. Die Breitenklassen stehen nicht zur Wahl und sind nicht entfernbar.

Die Auswahl steht als `t3sbClassPicker` in der RTE-YAML, nach `row` und `col`
getrennt, und ist bewusst eine Map und keine Liste: TYPO3 führt importierte YAML
mit `ArrayUtility::replaceAndAppendScalarValuesRecursive()` zusammen, hängt Listen
also an und ersetzt nur Schlüssel. Ein Sitepackage ändert damit einen einzelnen
Eintrag, entfernt ihn mit `null` oder ergänzt eigene. Ein Eintrag darf mehrere
Klassen tragen; sie werden gemeinsam gesetzt und gemeinsam entfernt.

Abschalten wie jedes andere T3SB-Werkzeug im RTE: Schalter `rteClasses` in der
Extension-Konfiguration unter *RTE*, pro Seitenbaum über
`RTE.t3sbootstrap.features` übersteuerbar. Das nimmt den Knopf aus der Toolbar
und die Liste aus der Editor-Konfiguration; bereits gesetzte Klassen bleiben im
Markup und werden weiterhin unverändert zurückgeschrieben.

Keine neuen Datenbankfelder, keine Migration — nach dem Update genügt Cache leeren.

### Behoben — „Backend-Layout (für Unterseiten)" wurde nicht vererbt

Wer auf einer Seite unter *Erscheinungsbild* ein **Backend-Layout für Unterseiten**
wählte, sah davon auf einer neu angelegten Unterseite nichts: Die stand weiterhin
auf „1 Spalte". Schuld war ein Vorgabewert im Page-TSconfig der Extension,
`TCAdefaults.pages.backend_layout = pagets__OneCol`. Er schrieb das Layout in das
**eigene** Feld jeder neuen Seite, und die Vererbung greift nur, solange dieses Feld
leer ist — im Backend löst `BackendLayoutView` erst das eigene Feld auf und steigt
nur bei leerem Wert die Rootline hoch, im Frontend überschreibt
`override.field = backend_layout` den geslideten Wert aus
`backend_layout_next_level`. Die Einstellung war also nicht kaputt, sie kam nie zum
Zug. Der Vorgabewert ist ersatzlos entfernt; ohne gesetztes Layout gilt weiterhin
`OneCol`.

**Bestehende Seiten ändern sich nicht:** bei ihnen steht `pagets__OneCol` real in
der Datenbank. Ein Upgrade-Wizard wäre nicht möglich, weil sich ein bewusst
gewähltes „1 Spalte" nicht von einem vorgegebenen unterscheiden lässt — wer die
Vererbung dort will, leert das Feld auf den betroffenen Seiten. **Dank an den
Melder**

---

## 5.3.55 — Inhalt von 5.3.54 unter neuer Nummer

Betrifft die folgenden Dateien:

```
CHANGELOG.md
composer.json
ext_emconf.php
releaseNote.pdf
```

### Behoben — 5.3.54 war über Composer nicht installierbar

Der Tag `v5.3.54` wurde nach der Veröffentlichung auf den fertigen Stand
umgehängt. Packagist hatte zu diesem Zeitpunkt den vorherigen Commit bereits
registriert und weist Änderungen an veröffentlichten stabilen Versionen ab.
Composer-Installationen von `5.3.54` erhielten deshalb den Code von 5.3.53.

Der vollständige Inhalt von 5.3.54 erscheint daher unter dieser Nummer. Wer
`5.3.54` per Composer geladen hat, aktualisiert auf `5.3.55`; am Code selbst
ändert sich gegenüber 5.3.54 nichts.

---

## 5.3.54 — Seitenweise abschalten, Einzug, senkrechte Überschrift

Betrifft die folgenden Dateien:

```
.gitignore   (neu)
composer.json
ext_conf_template.txt
ext_emconf.php
ext_tables.sql
Classes/Backend/EventListener/PageInfo.php
Classes/Backend/EventListener/RTE/FeatureToggles.php
Classes/Backend/EventListener/TCA/TcaCompilation.php
Classes/Backend/Form/DisplayCondition/SectionCondition.php   (neu)
Classes/Backend/Hooks/OutsourcedFiles.php
Classes/DataProcessing/BootstrapProcessor.php
Classes/DataProcessing/ConfigProcessor.php
Classes/ExpressionLanguage/T3sbConditionFunctionsProvider.php
Classes/Helper/ClassHelper.php
Classes/Helper/DefaultHelper.php
Classes/Updates/RteColorSettingUpgradeWizard.php   (neu)
Classes/UserFunction/TcaMatcher.php
Classes/Wrapper/BackgroundWrapper.php
Configuration/FlexForms/Container/BackgroundWrapper.xml
Configuration/RTE/Default.yaml
Configuration/Sets/T3sbootstrap/constants.typoscript
Configuration/Sets/T3sbootstrapVersion/settings.definitions.yaml
Configuration/TCA/Overrides/pages.php
Configuration/TCA/Overrides/tt_content_newCType.php
readMe.md
Resources/Private/Extensions/ke_search/Configuration/TypoScript/setup.typoscript
Resources/Private/Language/de.locallang.xlf
Resources/Private/Language/de.locallang_be.xlf
Resources/Private/Language/de.locallang_db.xlf
Resources/Private/Language/locallang.xlf
Resources/Private/Language/locallang_db.xlf
Resources/Private/Backend/ContainerPreview/.../four_columns/Preview.fluid.html
Resources/Private/Backend/ContainerPreview/.../toast_container/Preview.fluid.html
Resources/Private/Backend/ContainerPreview/.../two_columns/Preview.fluid.html
Resources/Private/Layouts/Content/Default.fluid.html
Resources/Private/Templates/Container/Default.fluid.html   (entfernt)
Resources/Private/Partials/Content/Field.fluid.html
Resources/Private/Partials/Content/Header/All.fluid.html
Resources/Private/Partials/Content/Header/Header.fluid.html
Resources/Private/Partials/Content/Header/SubHeader.fluid.html
Resources/Private/Partials/Content/Media/Carousel.fluid.html
Resources/Private/Partials/Content/Media/CopyrightNote.fluid.html   (neu)
Resources/Private/Partials/Content/Swiper/CustomOne.fluid.html
Resources/Private/Partials/Content/Swiper/CustomTwo.fluid.html
Resources/Private/Partials/Content/Swiper/Default.fluid.html
Resources/Private/Partials/Page/Jumbotron.fluid.html
Resources/Private/Partials/Page/Meta.fluid.html
Resources/Private/Partials/Page/Navbar/Assets.fluid.html
Resources/Private/Partials/Page/Title.fluid.html
Resources/Private/Templates/Content/Carousel.fluid.html
Resources/Private/Templates/Main.fluid.html
Resources/Public/Backend/Style/bestyles.css
Resources/Public/Backend/Style/rte.css
Resources/Public/JavaScript/ESM/rte_ckeditor/color-plugin.js   (neu)
Resources/Public/JavaScript/ESM/rte_ckeditor/table-base-plugin.js   (neu)
Resources/Public/Styles/t3sbootstrap.css
```

> Die `readMe.md` führt keine „What's new"-Abschnitte mehr: Was eine Version gebracht
> hat, steht in dieser Datei und in den Release-Notes, und an drei Orten driftet es
> auseinander.

> **Nach dem Update: Datenbankanalyse ausführen und Cache leeren.**
> `ext_tables.sql` bringt sieben neue Spalten in `pages` und zwei in `tt_content`
> mit. Ohne die Analyse bleiben der neue Reiter und die neuen Felder leer. Die TCA,
> die Sprachdateien und die kompilierten Fluid-Templates liegen im Cache.
>
> Einzug und senkrechte Überschrift sind beide **standardmäßig aus** — die Schalter
> `contentIndent` und `headerVertical` stehen in der Extension-Konfiguration.

### Neu — Reiter „T3SB" in den Seiteneigenschaften

Navbar, Meta-Zeile, Brotkrumen, Seitentitel, Jumbotron und Footer lassen sich
jetzt auf einer einzelnen Seite abschalten, ohne die Konfiguration der ganzen
Site anzufassen. Sechs Häkchen in einem eigenen Reiter:

```
tx_t3sbootstrap_navbar_disable      tx_t3sbootstrap_jumbotron_disable
tx_t3sbootstrap_meta_disable        tx_t3sbootstrap_pagetitle_disable
tx_t3sbootstrap_breadcrumb_disable  tx_t3sbootstrap_footer_disable
```

Der Reiter ist **standardmäßig aus**. Er erscheint erst, wenn in der
Extension-Konfiguration `pageOptOut` eingeschaltet wird — und ein einzelnes
Häkchen zeigt sich nur, solange der zugehörige Bereich im
T3SB-Konfigurations-Datensatz überhaupt aktiv ist. Wer die Navbar site-weit
abgeschaltet hat, bekommt dafür kein Häkchen zu sehen.

`ConfigProcessor::applyPageOptOut()` setzt die betroffenen Werte auf genau das,
was der Konfigurations-Datensatz speichern würde — Leerstring bei Auswahlfeldern,
`0` bei Häkchen —, und zwar bevor irgendein Bereich berechnet wird. Damit braucht
kein Template eine zweite Prüfung. Einzige Ausnahme ist die Meta-Zeile: sie liest
`settings.config.metaEnable` direkt aus dem TypoScript, deshalb bekommt sie einen
eigenen Schlüssel `config.pageOptOut.meta`.

Wird `pageOptOut` wieder ausgeschaltet, verschwindet nur der Reiter. Gespeicherte
Häkchen bleiben wirkungslos liegen, bis er wieder eingeschaltet wird.

Der Reiter hängt sich erst nach der TCA-Kompilierung an
(`TcaCompilation::addPageOptOutTab()`). `addToAllTCAtypes()` in den
TCA-Overrides erreicht nur die Seitentypen, die in diesem Moment schon
existieren; eine Extension, die später einen eigenen Typ mit vollständigem
`showitem` definiert, wirft den Reiter wieder hinaus. Nach der Kompilierung sind
alle Typen bekannt, und nichts überschreibt ihn mehr.

Seitentypen ohne eigene Ausgabe bleiben außen vor — Verweis (3), Shortcut (4),
Trenner (199), Ordner (254) und Papierkorb (255). Ein Häkchen zum Verbergen der
Navbar hätte dort keinen Sinn.

### Neu — Textfarben als eigenes Dropdown im RTE

Die sechs Bootstrap-Textfarben standen bisher zwölfmal in der Styles-Auswahl: einmal
als `Text Color *` für die Markierung, einmal als `Block Color *` für den Absatz.
Sie haben jetzt ein eigenes Werkzeug in der Toolbar, so wie die Badges seit 5.3.51.
Die Styles-Auswahl schrumpft dadurch von 41 auf 29 Einträge.

Das Dropdown führt beide Gruppen, getrennt durch einen Strich: oben `Text Primary`
bis `Text Warning` für die Markierung, darunter `Block Primary` bis `Block Warning`
für jeden berührten Absatz. `No color` räumt beides ab. Jede Zeile trägt einen
Farbpunkt — rund für die Markierung, eckig für den Absatz —, gespeist aus
`var(--bs-primary)` mit den Bootstrap-Werten als Rückfall.

Das Markup ist Zeichen für Zeichen dasselbe wie vorher, `<span class="text-primary">`
und `<p class="text-primary">`. Bestehende Inhalte brauchen deshalb keine Migration:
Sie werden beim Öffnen erkannt, lassen sich über das Dropdown ändern und gehen
unverändert zurück.

Für die Absätze steht dort ein eigener Downcast-Konverter statt
`attributeToAttribute`. Letzteres hätte beim Wechsel der Farbe die alte
`text-*`-Klasse am Absatz stehen lassen, weil es Klassen anfügt statt sie zu tauschen.

Die Einstellung heißt jetzt `rteColor` statt `rteStyleColors`, und der
`RteColorSettingUpgradeWizard` zieht einen gespeicherten Altwert um. Wer die Farben
abgeschaltet hatte, behält sie abgeschaltet — auch ohne den Wizard, solange der alte
Schlüssel in der Konfiguration steht.

### Neu — Textfarben sind im Editor jetzt auch zu sehen

`rte.css` bekommt die sechs Farbregeln. Bootstrap wird nicht in den Editor geladen,
deshalb blieb eine gesetzte Textfarbe dort bisher schwarz — man sah erst im Frontend,
was man getan hatte. Das galt schon für die alten Styles-Einträge.

### Neu — Einzug für die Inhaltselemente einer Seite

Eine Seite kann ihre Inhaltselemente einrücken, gemessen in Rasterspalten von 1 bis 6.
Das Feld steht im Reiter „T3SB" der Seiteneigenschaften, der Einzug greift ab dem
Breakpoint `lg` und nur im einspaltigen Backend-Layout — darunter und in mehrspaltigen
Layouts bleibt alles, wie es war.

Unterseiten erben den Wert, bis eine von ihnen einen eigenen setzt. Dafür läuft
`BootstrapProcessor::resolveContentIndent()` die Rootline von der aktuellen Seite nach
oben und nimmt den ersten gefüllten Wert. Leer heißt erben, `0` heißt ausdrücklich
keiner — damit lässt sich ein geerbter Einzug auf einem Zweig gezielt wieder abschalten.

Wer das nicht will, schaltet `contentIndentInherit` aus: Dann zählt allein die Seite,
auf der der Wert steht, und Unterseiten bekommen nichts. Die Auswahlliste verliert dabei
den Eintrag „Von der übergeordneten Seite erben" — es gibt nichts mehr zu erben —, und
der leere Wert heißt dort schlicht „Kein Einzug". Eine gespeicherte `0` bedeutet
dasselbe, der Schalter lässt sich also hin und her stellen, ohne dass ein Wert umgesetzt
werden muss.

Ein einzelnes Inhaltselement kann sich im Reiter „Erscheinungsbild" über
`tx_t3sbootstrap_no_indent` ausnehmen. Vier Element-Typen sind ohnehin ausgenommen,
weil sie über die ganze Breite gehören: Hintergrund- und Parallax-Wrapper sowie
Carousel- und Swiper-Container. Die übrigen Wrapper rücken mit ein.

Die Klasse `t3sb-indent-1` bis `t3sb-indent-6` sitzt an dem Element, das die ID trägt,
damit die Überschrift über dem Block mit umzieht. Bei einem Container druckt das Layout
sie als eigenes Attribut neben die ID, bei einer Button-Gruppe am Gruppen-Element —
sonst hängt sie an den übrigen Klassen des Elements.

Der Schalter heißt `contentIndent` und ist aus; `contentIndentInherit` steuert die
Vererbung und ist an. Den Einzug abzuschalten versteckt nur die Felder; gespeicherte
Werte bleiben stehen.

### Neu — Überschrift senkrecht ausgeben

Der Überschriftenblock eines Inhaltselements — Überschrift, Unterzeile und Überzeile —
lässt sich um 90 Grad drehen, nach oben oder nach unten gelesen. Das Feld „Vertical
header" steht im Reiter „Kopfzeilen", die Drehung gilt ab `lg`; darunter steht der Text
wieder normal.

`writing-mode: vertical-rl` erledigt die Drehung, `transform: rotate(180deg)` die
Leserichtung nach oben. Liegt die Überschrift über „Header inside" **im**
Hintergrund-Wrapper, wird sie dort senkrecht mittig gesetzt: `position: absolute` mit
`top: 50%` und einem Ausgleich über `translateY(-50%)`. Damit der Wrapper dafür der
Bezugsrahmen ist, setzt `BackgroundWrapper` in genau diesem Fall `position-relative` —
ohne einen positionierten Vorfahren landete die Überschrift im nächsten Element.

Das Feld hängt am Hintergrund-Wrapper mit „Header inside", und zwar in beide
Richtungen: Es erscheint im Formular nur dort — `TcaMatcher::headerInside()` als
`displayCond` — und gerendert wird die Drehung auch nur dort. Ohne Wrapper gäbe es
nichts, worin der herausgelöste Block zentriert werden könnte; ein Feld, das sichtbar
ist und nichts bewirkt, wäre die schlechtere Hälfte.

Die Bedingung liest den Wert aus dem Datensatz, den FormEngine übergibt, nicht aus der
Datenbank: `headerInside` hat jetzt `onChange = reload`, und dieser Neuaufbau trägt die
aktuellen Formularwerte, ohne zu speichern — das Häkchen wirkt damit sofort. Die
Datenbank bleibt der Rückfall für die Fälle, in denen das Feld noch nicht aufbereitet
vorliegt.

Der Schalter heißt `headerVertical` und ist aus. Ihn abzuschalten versteckt nur das
Feld; ein gespeicherter Wert wirkt wieder, sobald „Header inside" erneut an ist.

### Behoben — kein Backend-Layout im Workspace

Auf einer Seite mit Versionsdatensatz fehlte das Backend-Layout; auf einer
Root-Seite war die Spaltenaufteilung damit komplett weg.

FormEngine bearbeitet im Workspace den Versionsdatensatz und reicht genau dessen
`uid` an die Conditions weiter: `DatabaseEditRow` liest ihn über seine eigene uid,
ohne Workspace-Overlay, und `BackendLayoutView::determinePageId()` gibt diese uid
durch. Der Konfigurations-Datensatz hängt aber an der **Live**-Seite und hat nie
eine eigene Version — die Tabelle ist nicht workspace-fähig. Die Abfrage mit der
Versions-uid fand deshalb nichts, jedes `colPosList()` lieferte `false`.

Auf Unterseiten fiel es weniger auf, weil die Suche über die Rootline eine
Elternseite findet. Auf einer Root-Seite sitzt über dem Versionsdatensatz nur
uid 0, und es gibt nichts mehr aufzufangen.

Dieselbe Stolperstelle im Info-Block des Seitenmoduls: `PageInfo` lief die Rootline
über `RootlineUtility` ab, und die scheitert an einer Seite, die es nur im Workspace
gibt. Dort steht jetzt `BackendUtility::BEgetRootLine()`, das den Workspace kennt.

`resolveLivePageId()` nimmt jetzt `t3ver_oid`, sobald einer gesetzt ist. Die
Rootline-Suche wurde bei der Gelegenheit vereinfacht und in
`getParentPageIds()` ausgelagert.

### Behoben — Seitentitel lief randlos, wenn der Jumbotron keinen Container hat

`Jumbotron.fluid.html` setzte `pageTitleSkipContainer` unbesehen. Der Gedanke
dahinter war richtig: Der Jumbotron bringt normalerweise seinen eigenen Container
mit, ein zweiter darin wäre doppelte Polsterung und eine zweite Breitenbegrenzung.

Nur gilt das nicht immer. Ist für den Jumbotron kein Container eingestellt, hat er
auch keinen — und der Seitentitel lief dann über die volle Fensterbreite, während
alles andere eingerückt stand. Die Variable wird jetzt nur noch gesetzt, wenn
`{jumbotronContainer}` tatsächlich belegt ist.

### Behoben — zwei veraltete Integrity-Hashes im CDN-Betrieb

`bootstrap.cdn.bootstrapCssIntegrity` und `…JsIntegrity` standen in
`constants.typoscript` auf den Hashes einer älteren Bootstrap-Fassung, während
`settings.definitions.yaml` die richtigen führte. Beide Orte sind redundant: Wer
das Set „OTHER SETTINGS" nicht einbindet, bekommt die Konstanten — und damit zwei
falsche Hashes. Der Browser verweigert dann Bootstrap-CSS und -JS komplett, mit
einer SRI-Meldung in der Konsole als einzigem Hinweis.

Beide Werte sind gegen `bootstrap-5.3.8.tgz` nachgerechnet und angeglichen. Der
Bundle-Hash war bereits korrekt.

Vier weitere Werte standen an beiden Orten verschieden und sind auf den Stand der
Site-Settings gebracht: `cdn.swiper` (11 → 12), `image.maxWidthToast` (20 → 40),
`image.minimumWidth` (true → 0) und `backToTopTitle` („to tops" → „to top").

Zwei CDN-Versionen sind bei der Gelegenheit nachgezogen: `cdn.baguetteBox`
(1.11.1 → 1.13.0) und `cdn.glightbox` (3.2.0 → 3.3.1). Beide Pfade sind gegen das
jeweilige Paket geprüft — cdnjs liefert `baguetteBox.min.js` unter der neuen
Versionsnummer, und GLightbox behält `dist/css/` und `dist/js/`.

Die Beschreibungen von `cdn.jarallax` und `cdn.swiper` behaupteten, der gepinnte
Major sei zugleich der neueste. Das stimmt bei beiden nicht mehr — Jarallax steht
bei 3.x, Swiper bei 14.x. Sie sagen jetzt, was der Pin tut, ohne eine Zahl zu
nennen, die beim nächsten Release wieder falsch ist.

`cdn.glightbox` hat außerdem eine Beschreibung bekommen, weil der Wert leicht
überschätzt wird: Er wirkt nur im Kommando `t3sbootstrap:cdnToLocal`. Das
CDN-Markup lädt GLightbox ohne Versionsangabe, also immer die neueste Fassung.

### Behoben — Dropdown-Animation blieb dauerhaft aus

Slide-In und Fade wurden ignoriert; das Dropdown bekam statt `dd-animate-1`
die Klasse `removed-animate-1`.

Die Umbenennung ist gewollt — unterhalb des Navbar-Breakpoints soll keine
Animation laufen. Sie griff nur immer. Fehlt im Body das Attribut
`data-navbar-breakpointWidth`, fällt `MainAssets.fluid.html` auf `99999` zurück.
Für das Offcanvas ist das die sichere Annahme, für die Animation die falsche:
`vw < 99999` stimmt auf jedem Bildschirm.

Zwei Stellen sind nachgezogen. `OutsourcedFiles::resolveBreakpointWidth()` greift
jetzt auf die Bootstrap-Standardbreiten zurück, wenn die Site-Settings keine
liefern — das ist der Fall, sobald das Set „OTHER SETTINGS" fehlt, denn dort sind
sie definiert. Und `t3sbDropdownAnimate()` liest einen Breakpoint, den kein
Bildschirm erreicht, als „kein Breakpoint" statt als „immer mobil".

### Behoben — fehlendes Backend-Layout-Partial riss die ganze Seite ab

Ein Backend-Layout ohne passendes Partial unter `Partials/BackendLayouts/` warf
eine `InvalidPartialException` und nahm die komplette Seite mit — ein weißer
Bildschirm wegen eines falschen Werts in `pages.backend_layout`.

Das traf jedes selbstgebaute Layout ohne eigenes Partial, und es traf die
`_Extra`-Schlüssel älterer Installationen, bei denen der
`BackendlayoutUpgradeWizard` nie gelaufen war. `Main.fluid.html` fällt jetzt auf
`OneCol` zurück, statt abzubrechen.

### Behoben — „Gallery columns" stand bei jedem Inhaltselement

`tx_t3sbootstrap_inTextImgColumns` hatte als einziges Feld der Palette
„T3SB Image Settings" keine `displayCond`. Bei Elementen ohne Bilder fielen die
übrigen Felder weg, und übrig blieb ein nackter Kasten unter „Extended" — an
einer Stelle, an der das Feld ohnehin wirkungslos ist.

Es erscheint jetzt nur noch bei den sechs Bild-CTypes. Beschriftung und
Beschreibung kommen aus der Sprachdatei statt hartkodiert aus der TCA und heißen
jetzt „Breite des Bildblocks (Spalten)". Der alte Name legte eine Verwandtschaft
zu „Number of Columns" (`imagecols`) nahe, die es nicht gibt: Das Feld bestimmt
die Breite des umflossenen Bildblocks in Rasterspalten und wirkt ausschließlich
bei den Ausrichtungen „im Text, rechts" und „im Text, links".

### Behoben — fünf Beschriftungen blieben leer

`f:translate` gibt für einen Schlüssel, den es nicht gibt, den Leerstring aus — man
sieht also nichts und bekommt auch keinen Hinweis. Fünf Stellen traf das:

| Stelle | war | ist |
|---|---|---|
| Carousel, Link unter dem Text | `locallang.xlf:read-more` fehlte | *Read more* / *Weiterlesen* |
| Suchfeld, Platzhalter | `locallang.xlf:form.search` fehlte | *Search term* / *Suchbegriff* |
| Suchfeld, Schaltfläche | `locallang.xlf:form.submit` fehlte | *Search* / *Suchen* |
| Container-Vorschau, 2 und 4 Spalten | griff auf `flexform.cw.equalWidth` | greift auf `flexform.equalWidth` |
| Toast-Vorschau, Platzierung | suchte in `locallang_be.xlf` | sucht in `locallang_db.xlf` |

Die unteren beiden waren keine fehlenden Texte, sondern falsche Referenzen — die
Schlüssel gab es längst, nur unter anderem Namen beziehungsweise in der anderen Datei.

Dazu zwei Einträge, die nur auf Englisch vorlagen: *Expanded Content Top* und
*Background Images*. Beide Sprachdateien tragen jetzt dieselben 841 Schlüssel.

### Behoben — Meta-Zeile blieb leer, sobald nur Text hinterlegt war

Gemeldet für 5.3.53: Ist `meta_value` leer und `meta_text` gefüllt, erschien die
Meta-Zeile nicht. Die Bedingung in `Main.fluid.html`

```html
<f:if condition="{metaNavigation} || {config.meta.text}">
```

konnte nie wahr werden, weil **beide** Werte fehlten: `metaNavigation` erzeugt der
Menü-Prozessor nur aus `meta_value`, und `config.meta` wurde im `ConfigProcessor`
überhaupt nicht gebaut. Im `f:debug` tauchte deshalb keiner der beiden auf.

Der Schaden reichte weiter als der Bericht: `Partials/Page/Meta` liest jeden seiner
Werte aus `config.meta`. Auch mit gefülltem `meta_value` kam dort nichts an —
`{config.meta.container}` war leer, blieb damit ungleich `none` und druckte
`<div class=" d-flex justify-content-">` um die Zeile.

`ConfigProcessor::processMeta()` baut den Block jetzt. `meta_enable` trägt dabei die
Ausrichtung, so wie `navbar_enable` das Farbschema trägt — leer heißt aus, sonst
`start`, `end` oder `scroller`. Ein nicht gespeicherter Container wird zu `none`
statt zu einem leeren `class`-Attribut, und die eigene Klasse bekommt ihr führendes
Leerzeichen, weil das Partial sie als `nav-meta{class}` ausgibt.

Eine Falle steckt dabei in den generierten Konstanten: `OutsourcedFiles` schreibt ein
leeres Feld als `0`, in `settings.config` kommt also die Zeichenkette `"0"` an, nicht
`""`. Mit `??` wäre daraus `nav-meta 0` geworden — der Block prüft deshalb mit `?:`.

Der seitenweise Opt-out wirkt unverändert: `applyPageOptOut()` leert `metaEnable`,
bevor der Block gebaut wird.

### Behoben — leeres Markup für Unterzeile und Überschrift

Ein Inhaltselement mit Überschrift, aber ohne Unterzeile bekam trotzdem ein
`<p class="subheader">` — leer. Umgekehrt stand über einem Element, das nur eine
Unterzeile, ein Datum oder eine Überzeile trägt, ein leeres `<h2>`. Am auffälligsten
im Hintergrund-Wrapper, wo der Kopfblock über oder auf dem Bild sitzt.

Gemessen gibt `Partials/Content/Field` bei leerem Feld nicht den Leerstring zurück,
sondern **ein Byte: `\n`**. Fluid entfernt beim Einlesen eines Partials die
umschließenden `<html>`-Tags, behält aber, was dahinter steht — und das ist der
Zeilenumbruch am Dateiende. In Fluid ist eine Zeichenkette aus Leerraum TRUE, also:

```
{subheader}                      -> "\n"   -> f:if TRUE
{subheader -> f:format.trim()}   -> ""     -> f:if FALSE
```

`Header/All` prüft längst mit der getrimmten Fassung, reicht aber die ungetrimmte an
`Header/SubHeader` weiter, und dort stand `<f:if condition="{subheader}">`. Dieselbe
Falle hatte 5.3.52 schon beim Supraheader behoben — der Kommentar in `Content/Field`
warnt vor einem Umbruch *vor* dem `<f:if>`, nicht vor dem am Dateiende.

Beides ist jetzt zu: `Content/Field` endet ohne abschließenden Zeilenumbruch, und die
beiden Partials prüfen auf den getrimmten Wert, damit ein Editor, der den Umbruch
wieder einfügt, nichts kaputt macht. Ausgegeben wird weiter der ungetrimmte Wert —
`f:format.trim` escaped, ein `<br>` in der Überschrift käme sonst als Text heraus.

`Header/Header` hatte zusätzlich gar keine Prüfung: Es druckte das `<hN>` immer,
sobald `Header/All` den Block rendert, und das tut es, sobald *irgendeines* der vier
Felder gefüllt ist. Ein Icon ohne Text zählt dabei als Überschrift und wird weiter
ausgegeben.

### Behoben — kein Copyright-Hinweis in Carousel und Swiper

Ein Bild im Carousel zeigte seinen Copyright-Hinweis nicht; dasselbe Bild als
normales Inhaltselement schon. Im Swiper fehlte er ebenso.

Die Verwechslung liegt nahe, denn `Partials/Content/Media/Carousel.fluid.html`
hatte eine Section „Copyright", und die wurde dort auch gerendert — nur ist
dieses Partial für die Galerie-Variante „Carousel" in Textmedia, Mediaobject und
Card zuständig. Das Inhaltselement „Carousel" im Carousel-Container rendert
`Templates/Content/Carousel.fluid.html`, und dort fehlte die Ausgabe
vollständig: Das Wort „copyright" kam in der Datei nicht vor. In den drei
Swiper-Partials ebenso wenig.

Statt den Block jetzt viermal zu wiederholen, steht er in einem eigenen Partial
`Media/CopyrightNote`. Es erwartet `{file}` und wird von beiden Carousel-Dateien
sowie von `Swiper/Default`, `Swiper/CustomOne` und `Swiper/CustomTwo`
eingebunden — jeweils im `figure` hinter dem Bild und vor einer Bildunterschrift,
derselben Stelle wie bei `Media/Type/Image`. Dort muss der Hinweis stehen, denn
`.img-copyright-default` zieht sich mit einem negativen Margin über die
Unterkante des Bildes und braucht das Bild als vorangehendes Geschwister.

Die Toast-Variante (Extension-Konfiguration `imgCopyright = 2`) bleibt den
Slidern erspart: Ihr Skript hängt an `.image .img-copyright`, und ein geöffneter
Toast würde beim nächsten Slide-Wechsel mitsamt seinem Slide weggeschoben. In
Carousel und Swiper erscheint deshalb immer der eingebettete Hinweis.

Das Galerie-Partial prüfte außerdem allein `tx_t3sbootstrap_copyright`. Ein Bild,
bei dem nur das FAL-Feld `copyright` gefüllt ist, blieb dort ohne Hinweis,
während es als normales Bild einen bekam. Das neue Partial behandelt die zwei
Felder überall gleich — das FAL-Feld gewinnt.

Eine Besonderheit in `Swiper/Default`: Dort liegt das ganze `figure` in einem
`f:link.typolink`. Ein Klick auf den Copyright-Hinweis folgt also dem Bildlink.
Das zu ändern hieße, die Verschachtelung umzubauen; dafür gab es keinen Anlass.

### Behoben — Tabellen im RTE verloren ihre Bootstrap-Klasse

Eine mit „Table Striped" formatierte Tabelle sah nach Öffnen und Speichern wieder
schmucklos aus, und die Styles-Auswahl zeigte den Eintrag nicht mehr als aktiv.
Dasselbe bei „Table Hover" und „Table".

Der Grund steckt in CKEditor selbst. Dessen General-HTML-Support-Integration für
Tabellen verbraucht beim Lesen eines `<table>` zuerst die Klasse `table` und sammelt
erst danach die übrigen ein:

```js
o.consumable.consume(s, { classes: "table" });
n(s, "htmlTableAttributes");
```

Eine verbrauchte Klasse wird nicht mehr aufgenommen. CKEditor darf das, weil sein
eigenes Tabellen-Markup `<figure class="table">` als Hülle benutzt und den Namen
deshalb als intern behandelt — für Bootstrap ist genau diese Klasse aber die tragende.
Der Weg durch den Editor sah also so aus:

```
vorher    <table class="table table-striped">
Modell    htmlTableAttributes = { classes: ['table-striped'] }
nachher   <table class="table-striped">
```

Ohne `.table` greift keine der Bootstrap-Regeln, und `hasAllClasses` findet nicht mehr
beide Klassen des Styles-Eintrags.

Das neue `table-base-plugin.js` hängt einen Upcast-Konverter mit `priority: 'lowest'`
hinter den von CKEditor (`'low'`) und legt die Basisklasse zurück ins Attribut; von
dort schreibt der vorhandene Downcast sie wieder an das `<table>`. Die Styles-Einträge
in `Default.yaml` bleiben unverändert, ein Toolbar-Element kommt nicht hinzu. Erkennt
der Konverter eine `table-*`-Variante ohne Basisklasse, ergänzt er sie ebenfalls —
damit räumen bereits beschädigte Inhalte beim nächsten Speichern sich selbst auf.

### Behoben — „Linie darüber/darunter" wirkte in Wrappern nicht

Die Rahmen-Option einer Seite („Erscheinungsbild" → Rahmen) blieb an einem Wrapper
ohne Wirkung. Beim Hintergrund-Wrapper war die Linie nicht zu sehen, beim
Parallax-Wrapper und beim Swiper-Container fehlte sie ganz.

`Layouts/Content/Default` druckt bei einem Container **kein** `classAttr` an das
`div#c{uid}`; die Klassen gehen über `{class}` an das jeweilige Template und landen
dort an einem inneren Element. Für `frame-ruler-before` heißt das: Das Pseudoelement
wird innerhalb des Wrappers gezeichnet, beim Hintergrundbild also hinter dem Bild. Und
`ParallaxWrapper.fluid.html` wie `SwiperContainer.fluid.html` geben `{class}`
überhaupt nicht aus, dort fiel die Klasse vollständig weg.

Klassen, die das Element als Ganzes beschreiben, gehören an das Element mit der ID —
die Linien, weil sie Pseudoelemente im Fluss sind, der Einzug, weil er die Überschrift
mitnehmen muss. `BootstrapProcessor` sammelt beide jetzt in `outerAttr` (vormals
`indentAttr`), löst `frame-*` bei Containern aus `class` heraus und das Layout druckt
sie neben die ID. Ausgenommen ist das Accordion: Das Layout rendert es ohne dieses
`div`, dort bleibt die Klasse, wo sie war.

### Behoben — `content_margin_top` in Spalten-Containern

Der automatische Abstand galt auch für Inhaltselemente in zwei-, drei-, vier- und
sechsspaltigen Containern. Dort regelt die Rinne des Rasters den Abstand; ein
Margin darüber schiebt die Spalten gegeneinander aus der Flucht, weil jede Spalte
mit einem anderen Element beginnt. `DefaultHelper` nimmt jetzt beide
Container-Listen aus — `TX_CONTAINER_GRID` und `TX_CONTAINER`.

### Behoben — Abstand unter dem letzten Absatz in Alerts

Die Regel `div.alert > *:last-child { margin-bottom: 0 }` erreichte Absätze nicht,
die im RTE-Feld `bodytext` stecken. Sie gilt jetzt auch dort.

### Behoben — Backend-Stylesheet verbog das Karten-Raster aller Module

Gemeldet als [#527](https://github.com/t3solution/t3sbootstrap/issues/527). Seit
5.3.30 lädt die Extension bei `preview = 1` die Datei `bestyles.css` als
Backend-Stylesheet. Darin stand eine Regel ohne jede Eingrenzung:
`@container (min-width: 992px) { .card-size-small { width: calc(50% - …) !important } }`.
Sie traf jedes Backend-Modul, das die Karten des Cores benutzt — unter „Hilfe → Über"
standen statt vier nur noch zwei Karten je Reihe, und wegen `!important` schlug sie
auch `.w-100` fremder Module.

Der Ansatz war dazu von Grund auf falsch: Der Core bemisst diese Karten nicht über
`width`, sondern über `grid-template-columns` am `.card-container` — die Karten sind
Grid-Items. Eine feste Breite ändert die Spaltenaufteilung nicht, sie lässt die Karte
aus ihrer Zelle laufen. Breiter macht der Core eine Karte mit `grid-column`
(so ist `.card-size-medium` gebaut). Die Klasse `card-size-small` kommt in der
gesamten Extension sonst nirgends vor — die Regel konnte also nur fremde
Oberflächen treffen. Ersatzlos entfernt.

Im selben Zug eingegrenzt: `.settings-category-description` und
`.settings-category-headline` standen ebenfalls unbegrenzt mit `max-width: 90% !important`
in der Datei. Beide gelten jetzt nur noch im Site-Settings-Editor
(`[data-module-name="site_configuration"]`) und kommen ohne `!important` aus.

### Behoben — Offcanvas-Menü: Klasse `undefined`, Suchfeld, Ausrichtung

Im Markup des Offcanvas stand eine Klasse namens `undefined`:

```html
<div id="navbarToggler" class="offcanvas offcanvas-end bg-primary undefined">
```

Im ausgelieferten HTML steht sie nicht — sie kommt aus `t3sbOffcanvas()`. Die Funktion
setzt zwei Farbklassen (`utilColorArr[0]` und `[1]`), der `ConfigProcessor` liefert mit
`'bg-' . $navbarColor` aber nur eine. `classList.add(undefined)` schreibt daraufhin die
Zeichenkette „undefined" als Klasse ins DOM. Die Funktion arbeitet jetzt über die
gefilterte Liste und setzt so viele Klassen, wie ankommen — eine wie zwei.

Dieselbe Datei, zweiter Fund: `t3sbNavbarSearchbox()` greift unterhalb des Breakpoints
ohne Prüfung auf `document.querySelector('.navbar form')` zu, während der Zweig darüber
brav `if (form)` fragt. Ohne Suchformular in der Navbar brach die Funktion ab. Jetzt
steht die Prüfung am Anfang.

**Panel und Leiste sind dasselbe Markup.** Unterhalb des Breakpoints ist das Offcanvas
ein Panel, darüber die waagerechte Navbar — in CSS war dieser Zustand bisher nicht
unterscheidbar. `t3sbOffcanvas()` kennt ihn, weil sie Viewport und Breakpoint ohnehin
vergleicht, und markiert ihn jetzt mit `t3sb-offcanvas-panel`. Daran hängen zwei neue
Regeln, die nur im Panel gelten:

- Das Suchformular bekommt eine Trennlinie und Luft nach oben, statt direkt unter dem
  letzten Menüpunkt zu kleben
- Die Ausrichtungsoptionen der waagerechten Navbar werden neutralisiert. Sie erreichen
  das Panel über dieselbe Klasse: `nav-fill` und `nav-justified` zentrieren dort jede
  Beschriftung (Bootstrap setzt `text-align` auf ihre Kinder), `ms-auto` schiebt den
  ganzen Block an den Rand — beides kämpft im Panel mit dem linksbündigen Titel

Das Suchfeld selbst (`Resources/Private/Extensions/ke_search/…/setup.typoscript`) trug in
der Variante **ohne** Schaltfläche ein `me-2` — der Abstand zur Schaltfläche, die es dort
nicht gibt. Das Feld hörte dadurch vor dem Rand auf. Platzhalter und `aria-label` kommen
jetzt aus der Sprachdatei statt fest auf Deutsch bzw. Englisch dazustehen, und die
Schaltfläche der zweiten Variante heißt nicht mehr fest „Search" und ist nicht mehr grün.

### Entfernt — Debug-Template im Container-Ordner

`Resources/Private/Templates/Container/Default.fluid.html` enthielt nichts als ein
`<f:debug>` über alle Variablen. Kein Template referenziert es; im Release hat es nichts
zu suchen.

---

## 5.3.53 — TypoScript-Conditions, Abstände, FlexForm

Betrifft die folgenden Dateien:

```
ext_emconf.php
composer.json
Classes/Helper/DefaultHelper.php
Classes/DataProcessing/BootstrapProcessor.php
Configuration/TypoScript/Page/_main.typoscript
Configuration/TypoScript/Page/Register.typoscript
Configuration/TypoScript/Lib/ContentElement.typoscript
Configuration/FlexForms/Container/MasonryWrapper.xml
```

> **Nach dem Update: Cache leeren.** Die Bewertung der Conditions und die
> FlexForm-Struktur liegen im Cache. Eine Datenbankanalyse ist nicht nötig.

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
Deren Konstanten kommen aus `t3sbconstants.typoscript` beziehungsweise aus
`addTypoScriptConstants()` in `ext_localconf.php`, sind also immer Strings und im
Normalbetrieb unkritisch. Fehlt eine dieser Quellen — frische Installation vor dem
ersten Speichern des Konfigurations-Datensatzes, Install Tool im Failsafe-Modus —,
bleibt `{$…}` als Literal stehen und es knallt genauso.

### Behoben — `content_margin_top` im Footer und im Jumbotron

Jumbotron (`colPos 3`), Footer-Spalte (`4`) und erweiterter Inhalt (`20`/`21`)
fielen schon in 5.3.52 durch die Prüfung `colPos = 0 OR colPos > 199` heraus. Der
Footer als **eigene Seite** nicht — dessen Elemente liegen in `colPos 0` wie auf
jeder anderen Seite.

`getDefaults()` bekommt dafür jetzt `$containerConfig` übergeben und vergleicht
`footerPid` mit der `pid` des Elements; `getContainerClass()` hatte dieses Merkmal
längst, nur kam es nie an. Der Parameter hat eine Vorgabe (`array $containerConfig = []`),
damit ein eigener Aufruf aus einem Sitepackage nicht bricht.

### Behoben — veraltete TCA-Einstellung im Masonry-FlexForm

`shuffleCategories` trug noch `enableMultiSelectFilterTextfield`. Der Core wirft die
Einstellung seit v13 in `TcaMigration` per `unset()` weg und schreibt dabei eine
Deprecation-Meldung ins Log — bei jedem Aufbau der TCA. Das Suchfeld über der
Mehrfachauswahl ist seitdem ohnehin immer da, die Zeile war also schon wirkungslos,
als sie geschrieben wurde. Entfernt; am Formular ändert sich nichts.

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
