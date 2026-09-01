<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\UserFunction;

/**
 * Renders read-only info boxes inside the Extension Configuration form.
 *
 * Used via "type=user[...]" in ext_conf_template.txt - the returned HTML is
 * printed where the input field would normally be.
 */
final class ExtensionConfigurationInfo
{
	/**
	 * Info box for the "RTE" tab.
	 *
	 * @param array<string, mixed> $params
	 */
	public function renderRteInfo(array &$params, ?object $parentObject = null): string
	{
		// .callout is a flex container in the TYPO3 backend - the text has to sit
		// inside .callout-content/.callout-body, otherwise every paragraph becomes
		// a flex item and the box reads as newspaper columns.
		return <<<HTML
<div class="callout callout-info">
	<div class="callout-content">
		<div class="callout-body">
			<p><strong>Switching something off here hides the button - it does not touch existing content.</strong></p>
			<p>
				An editor without the Alert button can still open and edit an alert that is already in
				the text, they just cannot create a new one. That is on purpose: the CKEditor plugin
				stays loaded either way. If a switch dropped the plugin instead, CKEditor would no
				longer know the element and General HTML Support would decide its fate on the next
				save - usually by quietly throwing it away.
			</p>
			<p>
				The style groups work the same way: their classes are handed over to the HTML
				whitelist before the entry disappears from the dropdown, so nothing is lost on save.
			</p>
			<p>
				Need this per page tree instead of site-wide? Page TSconfig wins:
				<code>RTE.t3sbootstrap.features.columns = 0</code>
			</p>
			<p>
				<strong>Renamed:</strong> badges left the Styles dropdown and became a toolbar item of
				their own, so <code>rteStyleBadges</code> is now <code>rteBadge</code> (page TSconfig:
				<code>styleBadges</code> &rarr; <code>badge</code>). A value stored under the old name
				keeps deciding until the upgrade wizard <em>&quot;Rename the RTE setting
				rteStyleBadges to rteBadge&quot;</em> has run - or until this form is saved once, which
				drops the obsolete key.
			</p>
		</div>
	</div>
</div>
HTML;
	}
}
