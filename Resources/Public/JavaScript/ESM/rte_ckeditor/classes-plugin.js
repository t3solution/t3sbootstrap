import * as Core from '@ckeditor/ckeditor5-core';
import * as UI from '@ckeditor/ckeditor5-ui';
import * as Utils from '@ckeditor/ckeditor5-utils';

/**
 * Utility classes for the row and the columns of the Columns tool.
 *
 * The columns plugin already keeps every class it does not own in the model
 * attribute "extraClasses" and writes it back unchanged. This plugin is the way
 * to enter one: a toolbar dropdown over a list the project configures, so no free
 * text reaches the markup. The widths - row, col-md-* - are not offered, they
 * belong to the Columns tool and must not be removable here.
 */
const COMMAND = 't3sbClass';
const ATTRIBUTE = 'extraClasses';

/**
 * Fallback list, used when the RTE configuration carries no t3sbClassPicker key.
 */
const DEFAULT_DEFINITIONS = {
	row: {
		gutter0: { class: 'g-0', label: 'Kein Spaltenabstand', exclusive: 'gutter' },
		gutter3: { class: 'g-3', label: 'Spaltenabstand normal', exclusive: 'gutter' },
		gutter5: { class: 'g-5', label: 'Spaltenabstand groß', exclusive: 'gutter' },
		middle: { class: 'align-items-center', label: 'Spalten vertikal mittig' },
		center: { class: 'justify-content-center', label: 'Spalten mittig' },
		between: { class: 'justify-content-between', label: 'Spalten außen verteilt' },
		textCenter: { class: 'text-center', label: 'Text zentriert' }
	},
	col: {
		padding: { class: 'p-3', label: 'Innenabstand' },
		border: { class: 'border', label: 'Rahmen' },
		rounded: { class: 'rounded', label: 'Ecken gerundet' },
		textCenter: { class: 'text-center', label: 'Text zentriert' },
		flex: { class: 'd-flex', label: 'Flex-Container' },
		middle: { class: 'align-items-center', label: 'Inhalt mittig' }
	},
	div: {
		padding: { class: 'p-3', label: 'Innenabstand' },
		border: { class: 'border', label: 'Rahmen' },
		rounded: { class: 'rounded', label: 'Ecken gerundet' },
		textCenter: { class: 'text-center', label: 'Text zentriert' }
	}
};

const SCOPES = [
	{ key: 'row', model: 'bsRow', label: 'Zeile' },
	{ key: 'col', model: 'bsCol', label: 'Spalte' },
	{ key: 'div', model: 'bsDiv', label: 'Div' }
];

const CLASSES_ICON = `
	<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16">
		<g fill="currentColor">
			<path d="M6.2 2.1 4.6 13.9l1 .2L7.2 2.3zM3.4 4.6 0 8l3.4 3.4.7-.7L1.4 8l2.7-2.7zM12.6 4.6l-.7.7L14.6 8l-2.7 2.7.7.7L16 8z"/>
		</g>
	</svg>
`;

/**
 * "g-3 text-center" -> ['g-3', 'text-center']
 */
function splitClasses(value) {
	return value ? String(value).split(/\s+/).filter(Boolean) : [];
}

/**
 * Closest ancestor of the given model element name, starting at the position itself.
 */
function findAncestor(position, name) {
	let node = position ? position.parent : null;
	while (node) {
		if (node.name === name) {
			return node;
		}
		node = node.parent;
	}
	return null;
}

/**
 * A keyed map or a plain list, both end up as an array of entries.
 *
 * The map is the form the shipped configuration uses: TYPO3 merges imported YAML
 * with ArrayUtility::replaceAndAppendScalarValuesRecursive(), which APPENDS plain
 * lists and REPLACES by key. Only the map lets a sitepackage change a single entry
 * - or drop it, by setting the key to null.
 */
function toEntries(value) {
	if (Array.isArray(value)) {
		return value;
	}
	if (value && typeof value === 'object') {
		return Object.values(value);
	}
	return [];
}

/**
 * Reads the configured lists and drops entries without a class.
 */
function readDefinitions(editor) {
	const configured = editor.config.get('t3sbClassPicker');
	const source = configured && typeof configured === 'object' ? configured : DEFAULT_DEFINITIONS;
	const definitions = {};

	for (const scope of SCOPES) {
		definitions[scope.key] = toEntries(source[scope.key])
			.filter(item => item && item.class)
			.map(item => ({
				// one entry may carry several classes - "bg-brand text-white p-4"
				classes: splitClasses(item.class),
				label: item.label ? String(item.label) : String(item.class),
				exclusive: item.exclusive ? String(item.exclusive) : null
			}))
			.filter(item => item.classes.length);
	}

	return definitions;
}

/**
 * Entry headings and the lighter look of the class name behind the label.
 * The dropdown belongs to the editor UI, so contentsCss does not reach it.
 */
function injectDropdownStyles() {
	const id = 't3sb-classes-plugin-styles';
	if (typeof document === 'undefined' || document.getElementById(id)) {
		return;
	}
	const style = document.createElement('style');
	style.id = id;
	style.textContent =
		'.ck.ck-button.t3sb-class--head.ck-disabled{opacity:1;cursor:default;background:none;' +
		'font-size:.75em;text-transform:uppercase;letter-spacing:.06em;opacity:.65;padding-bottom:2px;}' +
		'.ck.ck-button.t3sb-class--head.ck-disabled .ck-button__label{font-weight:700;}';
	document.head.appendChild(style);
}

/**
 * Toggles one class on the row or on the column around the selection.
 *
 * value carries both class sets plus whether the respective element exists at all,
 * so every entry of the dropdown can bind its own state against it.
 */
class ClassCommand extends Core.Command {
	constructor(editor, definitions) {
		super(editor);
		this._definitions = definitions;
	}

	refresh() {
		const position = this.editor.model.document.selection.getFirstPosition();

		this._elements = {};
		const sets = {};
		const has = {};

		for (const scope of SCOPES) {
			const element = findAncestor(position, scope.model);
			this._elements[scope.key] = element;
			sets[scope.key] = new Set(splitClasses(element && element.getAttribute(ATTRIBUTE)));
			has[scope.key] = !!element;
		}

		this.isEnabled = SCOPES.some(scope => has[scope.key]);
		this.value = { sets, has };
	}

	execute(options = {}) {
		const scope = SCOPES.some(item => item.key === options.scope) ? options.scope : 'row';
		const classes = Array.isArray(options.classes) ? options.classes : [];
		const element = this._elements ? this._elements[scope] : null;

		if (!element || !classes.length) {
			return;
		}

		this.editor.model.change(writer => {
			const current = splitClasses(element.getAttribute(ATTRIBUTE));
			const isSet = classes.every(name => current.includes(name));
			let next;

			if (isSet) {
				next = current.filter(name => !classes.includes(name));
			} else {
				// one of a kind: g-0 replaces g-3 instead of standing next to it
				const siblings = options.exclusive
					? this._definitions[scope]
						.filter(item => item.exclusive === options.exclusive)
						.reduce((all, item) => all.concat(item.classes), [])
					: [];
				next = current.filter(name => !siblings.includes(name) && !classes.includes(name));
				next.push(...classes);
			}

			writer.setAttribute(ATTRIBUTE, next.join(' '), element);
		});
	}
}

export class ClassPicker extends Core.Plugin {
	static get pluginName() {
		return 'ClassPicker';
	}

	init() {
		const editor = this.editor;

		this._definitions = readDefinitions(editor);

		editor.commands.add(COMMAND, new ClassCommand(editor, this._definitions));

		injectDropdownStyles();

		editor.ui.componentFactory.add('classes', locale => this._createDropdown(locale));
	}

	_createDropdown(locale) {
		const editor = this.editor;
		const command = editor.commands.get(COMMAND);

		const dropdown = UI.createDropdown(locale);
		const items = new Utils.Collection();

		let firstGroup = true;

		for (const scope of SCOPES) {
			const definitions = this._definitions[scope.key];
			if (!definitions.length) {
				continue;
			}

			if (!firstGroup) {
				items.add({ type: 'separator' });
			}
			firstGroup = false;

			// a permanently disabled entry as the group heading - the list view knows
			// buttons, switches and separators, nothing else
			items.add({
				type: 'button',
				model: new UI.ViewModel({
					label: scope.label,
					withText: true,
					isEnabled: false,
					class: 't3sb-class--head'
				})
			});

			for (const definition of definitions) {
				const model = new UI.ViewModel({
					scope: scope.key,
					classes: definition.classes,
					exclusive: definition.exclusive,
					label: definition.label,
					withText: true
				});
				model.bind('isOn').to(command, 'value',
					value => !!(value && definition.classes.every(name => value.sets[scope.key].has(name))));
				// row entries stay greyed out while the caret sits outside a row
				model.bind('isEnabled').to(command, 'value',
					value => !!(value && value.has[scope.key]));
				items.add({ type: 'switchbutton', model });
			}
		}

		UI.addListToDropdown(dropdown, items);

		dropdown.buttonView.set({
			label: 'Klassen',
			icon: CLASSES_ICON,
			tooltip: true
		});

		// createDropdown() binds buttonView.isOn and .isEnabled itself - a second
		// bind() on the same observable throws.
		dropdown.bind('isEnabled').to(command, 'isEnabled');

		// The button sits in the toolbar for every text, but only works inside an
		// element it knows. The tooltip says so, and otherwise names what is set.
		dropdown.buttonView.bind('tooltip').to(command, 'value', value => {
			if (!value || !SCOPES.some(scope => value.has[scope.key])) {
				return 'Klassen - nur in Zeile, Spalte oder Div';
			}
			const parts = [];
			for (const scope of SCOPES) {
				if (value.has[scope.key] && value.sets[scope.key].size) {
					parts.push(scope.label + ': ' + Array.from(value.sets[scope.key]).join(' '));
				}
			}
			return parts.length ? 'Klassen - ' + parts.join(' | ') : 'Klassen setzen';
		});

		dropdown.on('execute', evt => {
			const { scope, classes, exclusive } = evt.source;
			if (!classes || !classes.length) {
				return;
			}
			editor.execute(COMMAND, { scope, classes, exclusive });
		});

		// createDropdown() closes the panel on execute. Several classes are usually
		// set in one go, so it is reopened afterwards - low priority runs last.
		dropdown.on('execute', () => {
			dropdown.isOpen = true;
		}, { priority: 'low' });

		return dropdown;
	}
}
