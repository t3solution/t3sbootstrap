import * as Core from '@ckeditor/ckeditor5-core';
import * as UI from '@ckeditor/ckeditor5-ui';
import * as Utils from '@ckeditor/ckeditor5-utils';

/**
 * The eight Bootstrap 5 context colours, once as badge and once as pill.
 * https://getbootstrap.com/docs/5.3/components/badge/
 *
 * The markup is character for character what the former "Badge *" / "Pill Badge *"
 * Styles entries produced, so existing content needs no migration: it is recognised
 * on open, editable through the dropdown and written back unchanged.
 */
const VARIANTS = [
	{ variant: 'primary', label: 'Primary' },
	{ variant: 'secondary', label: 'Secondary' },
	{ variant: 'success', label: 'Success' },
	{ variant: 'danger', label: 'Danger' },
	{ variant: 'warning', label: 'Warning' },
	{ variant: 'info', label: 'Info' },
	{ variant: 'light', label: 'Light' },
	{ variant: 'dark', label: 'Dark' }
];

const ATTRIBUTE = 't3sbBadge';
const COMMAND = 't3sbBadge';

/**
 * Baut die 16 Modellwerte: "primary" .. "dark" und "pill-primary" .. "pill-dark".
 *
 * @returns {Array<{value: string, variant: string, pill: boolean, classes: string[], label: string}>}
 */
function buildDefinitions() {
	const definitions = [];

	for (const pill of [false, true]) {
		for (const item of VARIANTS) {
			definitions.push({
				value: pill ? `pill-${item.variant}` : item.variant,
				variant: item.variant,
				pill,
				classes: pill
					? ['badge', 'rounded-pill', `text-bg-${item.variant}`]
					: ['badge', `text-bg-${item.variant}`],
				label: pill ? `Pill ${item.label}` : item.label
			});
		}
	}

	return definitions;
}

const DEFINITIONS = buildDefinitions();
const BY_VALUE = new Map(DEFINITIONS.map(definition => [definition.value, definition]));

const BADGE_ICON = `
	<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16">
		<g fill="currentColor">
			<path d="M4.5 4h7A2.5 2.5 0 0 1 14 6.5v3a2.5 2.5 0 0 1-2.5 2.5h-7A2.5 2.5 0 0 1 2 9.5v-3A2.5 2.5 0 0 1 4.5 4zm0 1A1.5 1.5 0 0 0 3 6.5v3A1.5 1.5 0 0 0 4.5 11h7A1.5 1.5 0 0 0 13 9.5v-3A1.5 1.5 0 0 0 11.5 5z"/>
			<path d="M5 7.25h6v1.5H5z" opacity=".6"/>
		</g>
	</svg>
`;

/**
 * Faerbt die Eintraege im Dropdown. Das Dropdown gehoert zur Editor-Oberflaeche
 * und wird deshalb nicht von contentsCss erfasst - die Regeln muessen einmal je
 * Dokument eingehaengt werden. Gleiche Vorgehensweise wie im Alert-Plugin.
 */
function injectDropdownStyles() {
	const id = 't3sb-badge-plugin-styles';
	if (typeof document === 'undefined' || document.getElementById(id)) {
		return;
	}

	const swatches = VARIANTS
		.map(item => `.ck.ck-button.t3sb-badge--${item.variant}::before{background:var(--bs-${item.variant},#adb5bd);}`)
		.join('');

	const style = document.createElement('style');
	style.id = id;
	style.textContent =
		'.ck.ck-button[class*="t3sb-badge--"]{position:relative;padding-left:26px;}' +
		'.ck.ck-button[class*="t3sb-badge--"]::before{content:"";position:absolute;left:8px;top:50%;' +
		'width:12px;height:8px;margin-top:-4px;border-radius:2px;border:1px solid rgba(0,0,0,.25);}' +
		'.ck.ck-button.t3sb-badge--pill::before{border-radius:4px;}' +
		swatches;
	document.head.appendChild(style);
}

/**
 * Sets, switches or removes the badge attribute.
 *
 * On a selection it applies to the marked ranges, on a collapsed caret to what is
 * typed next - the same behaviour as bold or italic.
 */
class BadgeCommand extends Core.Command {
	refresh() {
		const model = this.editor.model;
		const selection = model.document.selection;

		this.value = selection.getAttribute(ATTRIBUTE) || null;
		this.isEnabled = model.schema.checkAttributeInSelection(selection, ATTRIBUTE);
	}

	execute(options = {}) {
		const model = this.editor.model;
		const selection = model.document.selection;
		const value = options.value && BY_VALUE.has(options.value) ? options.value : null;

		model.change(writer => {
			if (selection.isCollapsed) {
				if (value === null) {
					writer.removeSelectionAttribute(ATTRIBUTE);
				} else {
					writer.setSelectionAttribute(ATTRIBUTE, value);
				}
				return;
			}

			const ranges = model.schema.getValidRanges(selection.getRanges(), ATTRIBUTE);
			for (const range of ranges) {
				if (value === null) {
					writer.removeAttribute(ATTRIBUTE, range);
				} else {
					writer.setAttribute(ATTRIBUTE, value, range);
				}
			}
		});
	}
}

/**
 * A toolbar dropdown for Bootstrap badges, plain and pill.
 *
 * A real editor feature rather than Styles entries: the classes are in the schema
 * and converted both ways, so they survive presets whose General HTML Support
 * whitelist only covers <div>, and 16 entries stay out of the 57-entry Styles
 * dropdown. Disabling the toolbar item only hides the button; existing badges stay
 * readable and are written back untouched.
 */
export class BadgePicker extends Core.Plugin {
	static get pluginName() {
		return 'BadgePicker';
	}

	init() {
		const editor = this.editor;
		const schema = editor.model.schema;

		schema.extend('$text', { allowAttributes: ATTRIBUTE });
		schema.setAttributeProperties(ATTRIBUTE, { isFormatting: true, copyOnEnter: false });

		editor.conversion.for('downcast').attributeToElement({
			model: ATTRIBUTE,
			view: (value, { writer }) => {
				const definition = BY_VALUE.get(value);
				if (!definition) {
					return;
				}
				return writer.createAttributeElement(
					'span',
					{ class: definition.classes.join(' ') },
					{ priority: 7 }
				);
			}
		});

		// Pill variants first and with higher priority: a pill also carries the two
		// classes the plain matcher accepts, and whoever consumes first wins - otherwise
		// every pill becomes a plain badge and "rounded-pill" is left to General HTML Support.
		for (const definition of DEFINITIONS) {
			editor.conversion.for('upcast').elementToAttribute({
				view: {
					name: 'span',
					classes: definition.classes
				},
				model: {
					key: ATTRIBUTE,
					value: definition.value
				},
				converterPriority: definition.pill ? 'highest' : 'high'
			});
		}

		editor.commands.add(COMMAND, new BadgeCommand(editor));

		injectDropdownStyles();

		editor.ui.componentFactory.add('badge', locale => this._createDropdown(locale));
	}

	_createDropdown(locale) {
		const editor = this.editor;
		const command = editor.commands.get(COMMAND);

		const dropdown = UI.createDropdown(locale);
		const items = new Utils.Collection();

		const noneModel = new UI.ViewModel({
			commandValue: null,
			label: 'No badge',
			withText: true
		});
		noneModel.bind('isOn').to(command, 'value', value => !value);
		items.add({ type: 'button', model: noneModel });

		let previousGroup = null;
		for (const definition of DEFINITIONS) {
			const group = definition.pill ? 'pill' : 'badge';
			if (group !== previousGroup) {
				items.add({ type: 'separator' });
				previousGroup = group;
			}

			const model = new UI.ViewModel({
				commandValue: definition.value,
				label: definition.label,
				class: `t3sb-badge--${definition.variant}` + (definition.pill ? ' t3sb-badge--pill' : ''),
				withText: true
			});
			model.bind('isOn').to(command, 'value', value => value === definition.value);
			items.add({ type: 'button', model });
		}

		UI.addListToDropdown(dropdown, items);

		dropdown.buttonView.set({
			label: 'Badge',
			icon: BADGE_ICON,
			tooltip: true
		});

		// createDropdown() bindet buttonView.isOn und .isEnabled bereits selbst -
		// ein zweites bind() auf denselben Observable wirft.
		dropdown.bind('isEnabled').to(command, 'isEnabled');

		dropdown.buttonView.bind('tooltip').to(command, 'value', value => {
			const definition = value ? BY_VALUE.get(value) : null;
			return definition ? `Badge (${definition.classes.join(' ')})` : 'Badge';
		});

		dropdown.on('execute', evt => {
			editor.execute(COMMAND, { value: evt.source.commandValue });
			editor.editing.view.focus();
		});

		return dropdown;
	}
}
