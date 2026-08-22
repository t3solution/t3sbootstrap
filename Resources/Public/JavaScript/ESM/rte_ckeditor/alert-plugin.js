import * as Core from '@ckeditor/ckeditor5-core';
import * as UI from '@ckeditor/ckeditor5-ui';
import * as Utils from '@ckeditor/ckeditor5-utils';
import * as Widget from '@ckeditor/ckeditor5-widget';

/**
 * All contextual alert variants of Bootstrap 5.
 * https://getbootstrap.com/docs/5.3/components/alerts/
 */
const ALERT_VARIANTS = [
	{ variant: 'primary', label: 'Alert Primary' },
	{ variant: 'secondary', label: 'Alert Secondary' },
	{ variant: 'success', label: 'Alert Success' },
	{ variant: 'danger', label: 'Alert Danger' },
	{ variant: 'warning', label: 'Alert Warning' },
	{ variant: 'info', label: 'Alert Info' },
	{ variant: 'light', label: 'Alert Light' },
	{ variant: 'dark', label: 'Alert Dark' }
];

const DEFAULT_VARIANT = 'light';
const VALID_VARIANTS = ALERT_VARIANTS.map(item => item.variant);

const ALERT_ICON = `
	<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16">
		<g fill="currentColor">
			<path d="M13 2c.6 0 1 .4 1 1v10c0 .6-.4 1-1 1H3c-.6 0-1-.4-1-1V3c0-.6.4-1 1-1h10m0-1H3c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h10c1.1 0 2-.9 2-2V3c0-1.1-.9-2-2-2z"/>
			<path d="M8 4.25c.41 0 .75.34.75.75v3.5a.75.75 0 0 1-1.5 0V5c0-.41.34-.75.75-.75z"/>
			<circle cx="8" cy="11" r="1"/>
		</g>
	</svg>
`;

/**
 * Normalizes any stored value to a variant that really exists in Bootstrap 5.
 */
function normalizeVariant(value) {
	return VALID_VARIANTS.includes(value) ? value : DEFAULT_VARIANT;
}

/**
 * Returns the closest `alert` ancestor of a model element, or null.
 */
function findAlert(element) {
	if (!element) {
		return null;
	}
	return element.getAncestors().find(ancestor => ancestor.name === 'alert') || null;
}

/**
 * Adds the colour swatches for the dropdown entries. The dropdown is part of the
 * editor chrome and therefore not covered by `contentsCss`, so the rules are
 * injected once per document.
 */
function injectDropdownStyles() {
	const id = 't3sb-alert-plugin-styles';
	if (typeof document === 'undefined' || document.getElementById(id)) {
		return;
	}
	const swatches = ALERT_VARIANTS
		.map(item => `.ck.ck-button.t3sb-alert--${item.variant}::before{background:var(--bs-${item.variant},#adb5bd);}`)
		.join('');
	const style = document.createElement('style');
	style.id = id;
	style.textContent =
		'.ck.ck-button[class*="t3sb-alert--"]{position:relative;padding-left:26px;}' +
		'.ck.ck-button[class*="t3sb-alert--"]::before{content:"";position:absolute;left:8px;top:50%;' +
		'width:10px;height:10px;margin-top:-5px;border-radius:2px;border:1px solid rgba(0,0,0,.25);}' +
		swatches;
	document.head.appendChild(style);
}

export class AlertBox extends Core.Plugin {
	static get pluginName() {
		return 'AlertBox';
	}

	init() {
		const editor = this.editor;

		editor.model.schema.register('alert', {
			allowWhere: '$block',
			allowContentOf: '$root',
			allowAttributes: ['variant']
		});

		// <div class="alert alert-*"> -> alert[variant]
		// High priority so the General HTML Support feature does not claim the div first.
		editor.conversion.for('upcast').elementToElement({
			view: {
				name: 'div',
				classes: 'alert'
			},
			model: (viewElement, { writer }) => {
				const variant = VALID_VARIANTS.find(candidate => viewElement.hasClass(`alert-${candidate}`));
				return writer.createElement('alert', { variant: variant || DEFAULT_VARIANT });
			},
			converterPriority: 'high'
		});

		// Listing `variant` in the model definition enables element reconversion,
		// so switching the variant re-renders the div instead of doing nothing.
		editor.conversion.for('dataDowncast').elementToElement({
			model: {
				name: 'alert',
				attributes: ['variant']
			},
			view: (modelElement, { writer }) => writer.createContainerElement('div', {
				class: `alert alert-${normalizeVariant(modelElement.getAttribute('variant'))}`
			})
		});

		editor.conversion.for('editingDowncast').elementToElement({
			model: {
				name: 'alert',
				attributes: ['variant']
			},
			view: (modelElement, { writer }) => {
				const div = writer.createContainerElement('div', {
					class: `alert alert-${normalizeVariant(modelElement.getAttribute('variant'))}`
				});
				return Widget.toWidgetEditable(div, writer, { label: 'alert widget' });
			}
		});

		editor.commands.add('insertAlert', new InsertAlertCommand(editor));
		editor.commands.add('removeAlert', new RemoveAlertCommand(editor));

		editor.ui.componentFactory.add('alert', locale => this._createDropdown(locale));
	}

	_createDropdown(locale) {
		const editor = this.editor;
		const insertCommand = editor.commands.get('insertAlert');
		const removeCommand = editor.commands.get('removeAlert');

		injectDropdownStyles();

		const dropdown = UI.createDropdown(locale);
		const items = new Utils.Collection();

		for (const definition of ALERT_VARIANTS) {
			const model = new UI.ViewModel({
				commandName: 'insertAlert',
				commandValue: definition.variant,
				label: definition.label,
				class: `t3sb-alert--${definition.variant}`,
				withText: true
			});
			model.bind('isOn').to(insertCommand, 'value', value => value === definition.variant);
			items.add({ type: 'button', model });
		}

		items.add({ type: 'separator' });

		const removeModel = new UI.ViewModel({
			commandName: 'removeAlert',
			label: 'Remove Alert',
			withText: true
		});
		removeModel.bind('isEnabled').to(removeCommand, 'isEnabled');
		items.add({ type: 'button', model: removeModel });

		UI.addListToDropdown(dropdown, items);

		dropdown.buttonView.set({
			label: 'Insert Alert',
			icon: ALERT_ICON
		});

		// Do NOT bind buttonView.isOn or buttonView.isEnabled here:
		// createDropdown() already binds both to the dropdown itself, and
		// Observable#bind throws on a property that is bound a second time.
		// Binding the dropdown's own isEnabled is the documented way.
		dropdown.bind('isEnabled').to(insertCommand, 'isEnabled');
		dropdown.buttonView.bind('tooltip').to(
			insertCommand,
			'value',
			value => value ? `Insert Alert (alert-${value})` : 'Insert Alert'
		);

		dropdown.on('execute', evt => {
			const { commandName, commandValue } = evt.source;
			if (commandName === 'removeAlert') {
				editor.execute('removeAlert');
			} else {
				editor.execute('insertAlert', { variant: commandValue });
			}
			editor.editing.view.focus();
		});

		return dropdown;
	}
}

/**
 * Wraps the selected blocks in an alert, or changes the variant of an existing alert.
 */
class InsertAlertCommand extends Core.Command {
	execute(options = {}) {
		const editor = this.editor;
		const model = editor.model;
		const variant = normalizeVariant(options.variant);
		const blocks = Array.from(model.document.selection.getSelectedBlocks());

		if (blocks.length === 0) {
			return;
		}

		model.change(writer => {
			const existingAlert = findAlert(blocks[0]);

			// Already inside an alert: only switch the variant.
			if (existingAlert) {
				writer.setAttribute('variant', variant, existingAlert);
				return;
			}

			const alertElement = writer.createElement('alert', { variant });
			writer.insert(alertElement, writer.createPositionBefore(blocks[0]));

			for (const block of blocks) {
				if (!findAlert(block)) {
					writer.move(
						writer.createRangeOn(block),
						writer.createPositionAt(alertElement, 'end')
					);
				}
			}

			if (alertElement.childCount === 0) {
				writer.append(writer.createElement('paragraph'), alertElement);
			}
		});
	}

	refresh() {
		const blocks = Array.from(this.editor.model.document.selection.getSelectedBlocks());
		const alertElement = blocks.length > 0 ? findAlert(blocks[0]) : null;

		this.isEnabled = blocks.length > 0;
		this.value = alertElement ? normalizeVariant(alertElement.getAttribute('variant')) : null;
	}
}

/**
 * Unwraps the alert around the current selection and keeps its content.
 */
class RemoveAlertCommand extends Core.Command {
	execute() {
		const model = this.editor.model;
		const blocks = Array.from(model.document.selection.getSelectedBlocks());

		if (blocks.length === 0) {
			return;
		}

		model.change(writer => {
			const alertElement = findAlert(blocks[0]);
			if (!alertElement) {
				return;
			}

			for (const child of Array.from(alertElement.getChildren())) {
				writer.move(
					writer.createRangeOn(child),
					writer.createPositionBefore(alertElement)
				);
			}

			writer.remove(alertElement);
		});
	}

	refresh() {
		const blocks = Array.from(this.editor.model.document.selection.getSelectedBlocks());
		this.isEnabled = blocks.length > 0 && !!findAlert(blocks[0]);
	}
}
