import * as Core from '@ckeditor/ckeditor5-core';
import * as UI from '@ckeditor/ckeditor5-ui';
import * as Utils from '@ckeditor/ckeditor5-utils';

/**
 * Bootstrap 5 spacing scale.
 * https://getbootstrap.com/docs/5.3/utilities/spacing/
 */
const STEPS = [
	{ value: '1', size: '0.25 rem' },
	{ value: '2', size: '0.5 rem' },
	{ value: '3', size: '1 rem' },
	{ value: '4', size: '1.5 rem' },
	{ value: '5', size: '3 rem' }
];

const SIDES = [
	{ key: 'top', prefix: 'mt', label: 'Top', attribute: 't3sbMarginTop', command: 't3sbMarginTop' },
	{ key: 'bottom', prefix: 'mb', label: 'Bottom', attribute: 't3sbMarginBottom', command: 't3sbMarginBottom' }
];

const MARGIN_ICON = `
	<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16">
		<g fill="currentColor">
			<path d="M2 1h12v1H2zM2 14h12v1H2z"/>
			<path d="M8 3.5 6 6h1.4v4H6l2 2.5L10 10H8.6V6H10z"/>
			<path d="M3 6h2v1H3zM11 6h2v1h-2zM3 9h2v1H3zM11 9h2v1h-2z" opacity=".5"/>
		</g>
	</svg>
`;

/**
 * Returns the blocks of the current selection that can carry the attribute.
 */
function selectableBlocks(model, attribute) {
	return Array.from(model.document.selection.getSelectedBlocks())
		.filter(block => model.schema.checkAttribute(block, attribute));
}

/**
 * Sets, changes or removes one margin attribute on every selected block.
 */
class MarginCommand extends Core.Command {
	constructor(editor, attribute) {
		super(editor);
		this.attribute = attribute;
	}

	refresh() {
		const model = this.editor.model;
		const blocks = selectableBlocks(model, this.attribute);

		this.isEnabled = blocks.length > 0;

		if (!this.isEnabled) {
			this.value = null;
			return;
		}

		// Only report a value when the whole selection agrees on it, otherwise
		// no entry in the dropdown is marked as active.
		const first = blocks[0].getAttribute(this.attribute) || null;
		const uniform = blocks.every(block => (block.getAttribute(this.attribute) || null) === first);
		this.value = uniform ? first : null;
	}

	execute(options = {}) {
		const model = this.editor.model;
		const value = options.value || null;

		model.change(writer => {
			for (const block of selectableBlocks(model, this.attribute)) {
				if (value === null) {
					writer.removeAttribute(this.attribute, block);
				} else {
					writer.setAttribute(this.attribute, value, block);
				}
			}
		});
	}
}

/**
 * A toolbar dropdown for the Bootstrap margin utilities mt-1..mt-5 and mb-1..mb-5.
 *
 * A real editor feature, not a Styles entry: the classes are in the schema and
 * converted both ways, so they survive a save even in presets whose General HTML
 * Support whitelist only covers <div>. Top and bottom are independent. Margins sit
 * on the block the cursor is in; a list item gets it on its own paragraph, the
 * surrounding <ul> cannot be addressed from inside the editor.
 */
export class MarginPicker extends Core.Plugin {
	static get pluginName() {
		return 'MarginPicker';
	}

	init() {
		const editor = this.editor;
		const schema = editor.model.schema;

		for (const side of SIDES) {
			schema.extend('$block', { allowAttributes: side.attribute });
			schema.setAttributeProperties(side.attribute, { isFormatting: true });

			// The two-way helper - the same one the Alignment feature uses for its
			// className mode. One call covers upcast plus data and editing downcast.
			const view = {};
			for (const step of STEPS) {
				view[step.value] = { key: 'class', value: `${side.prefix}-${step.value}` };
			}
			editor.conversion.attributeToAttribute({
				model: { key: side.attribute, values: STEPS.map(step => step.value) },
				view
			});

			editor.commands.add(side.command, new MarginCommand(editor, side.attribute));
		}

		editor.ui.componentFactory.add('margins', locale => this._createDropdown(locale));
	}

	_createDropdown(locale) {
		const editor = this.editor;
		const commands = SIDES.map(side => editor.commands.get(side.command));

		const dropdown = UI.createDropdown(locale);
		const items = new Utils.Collection();

		SIDES.forEach((side, index) => {
			const command = editor.commands.get(side.command);

			if (index > 0) {
				items.add({ type: 'separator' });
			}

			const noneModel = new UI.ViewModel({
				commandName: side.command,
				commandValue: null,
				label: `${side.label}: none`,
				withText: true
			});
			noneModel.bind('isOn').to(command, 'value', value => !value);
			items.add({ type: 'button', model: noneModel });

			for (const step of STEPS) {
				const model = new UI.ViewModel({
					commandName: side.command,
					commandValue: step.value,
					label: `${side.label}: .${side.prefix}-${step.value} (${step.size})`,
					withText: true
				});
				model.bind('isOn').to(command, 'value', value => value === step.value);
				items.add({ type: 'button', model });
			}
		});

		UI.addListToDropdown(dropdown, items);

		dropdown.buttonView.set({
			label: 'Margin',
			icon: MARGIN_ICON,
			tooltip: true
		});

		// Do NOT bind buttonView.isOn or buttonView.isEnabled - createDropdown()
		// already binds both, and Observable#bind throws on a second bind.
		dropdown.bind('isEnabled').to(
			commands[0], 'isEnabled',
			commands[1], 'isEnabled',
			(top, bottom) => top || bottom
		);

		dropdown.buttonView.bind('tooltip').to(
			commands[0], 'value',
			commands[1], 'value',
			(top, bottom) => {
				const active = [];
				if (top) {
					active.push(`mt-${top}`);
				}
				if (bottom) {
					active.push(`mb-${bottom}`);
				}
				return active.length ? `Margin (${active.join(' ')})` : 'Margin';
			}
		);

		dropdown.on('execute', evt => {
			const { commandName, commandValue } = evt.source;
			editor.execute(commandName, { value: commandValue });
			editor.editing.view.focus();
		});

		return dropdown;
	}
}
