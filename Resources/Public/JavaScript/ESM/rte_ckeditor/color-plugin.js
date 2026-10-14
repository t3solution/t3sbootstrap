import * as Core from '@ckeditor/ckeditor5-core';
import * as UI from '@ckeditor/ckeditor5-ui';
import * as Utils from '@ckeditor/ckeditor5-utils';

/**
 * The six Bootstrap text colors that used to be the "Text Color *" and
 * "Block Color *" entries of the Styles dropdown.
 *
 * The markup is character for character what those entries produced, so existing
 * content needs no migration: it is recognised on open, editable through the
 * dropdown and written back unchanged.
 */
const COLORS = [
	{ value: 'primary', label: 'Primary' },
	{ value: 'secondary', label: 'Secondary' },
	{ value: 'success', label: 'Success' },
	{ value: 'info', label: 'Info' },
	{ value: 'danger', label: 'Danger' },
	{ value: 'warning', label: 'Warning' }
];

const BY_VALUE = new Map(COLORS.map(color => [color.value, color]));

const TEXT_ATTRIBUTE = 't3sbTextColor';
const BLOCK_ATTRIBUTE = 't3sbBlockColor';
const TEXT_COMMAND = 't3sbTextColor';
const BLOCK_COMMAND = 't3sbBlockColor';

const COLOR_ICON = `
	<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16">
		<g fill="currentColor">
			<path d="M8 1.5 4.6 7.2a4 4 0 1 0 6.8 0zm0 1.94 2.54 4.27a3 3 0 1 1-5.08 0z"/>
			<path d="M2 13.5h12V15H2z" opacity=".6"/>
		</g>
	</svg>
`;

/**
 * Colors the entries of the dropdown. The dropdown belongs to the editor UI and
 * is therefore not covered by contentsCss - the rules have to be added once per
 * document. Same approach as in the badge plugin.
 */
function injectDropdownStyles() {
	const id = 't3sb-color-plugin-styles';
	if (typeof document === 'undefined' || document.getElementById(id)) {
		return;
	}

	const swatches = COLORS
		.map(color => `.ck.ck-button.t3sb-color--${color.value}::before{background:var(--bs-${color.value},#adb5bd);}`)
		.join('');

	const style = document.createElement('style');
	style.id = id;
	style.textContent =
		'.ck.ck-button[class*="t3sb-color--"]{position:relative;padding-left:26px;}' +
		'.ck.ck-button[class*="t3sb-color--"]::before{content:"";position:absolute;left:8px;top:50%;' +
		'width:12px;height:12px;margin-top:-6px;border-radius:50%;border:1px solid rgba(0,0,0,.25);}' +
		'.ck.ck-button.t3sb-color--block::before{border-radius:2px;width:14px;height:10px;margin-top:-5px;}' +
		swatches;
	document.head.appendChild(style);
}

/**
 * Sets, switches or removes the color on the marked text.
 *
 * On a selection it applies to the marked ranges, on a collapsed caret to what is
 * typed next - the same behaviour as bold or italic.
 */
class TextColorCommand extends Core.Command {
	refresh() {
		const model = this.editor.model;
		const selection = model.document.selection;

		this.value = selection.getAttribute(TEXT_ATTRIBUTE) || null;
		this.isEnabled = model.schema.checkAttributeInSelection(selection, TEXT_ATTRIBUTE);
	}

	execute(options = {}) {
		const model = this.editor.model;
		const selection = model.document.selection;
		const value = options.value && BY_VALUE.has(options.value) ? options.value : null;

		model.change(writer => {
			if (selection.isCollapsed) {
				if (value === null) {
					writer.removeSelectionAttribute(TEXT_ATTRIBUTE);
				} else {
					writer.setSelectionAttribute(TEXT_ATTRIBUTE, value);
				}
				return;
			}

			const ranges = model.schema.getValidRanges(selection.getRanges(), TEXT_ATTRIBUTE);
			for (const range of ranges) {
				if (value === null) {
					writer.removeAttribute(TEXT_ATTRIBUTE, range);
				} else {
					writer.setAttribute(TEXT_ATTRIBUTE, value, range);
				}
			}
		});
	}
}

/**
 * Sets, switches or removes the color on every paragraph the selection touches.
 */
class BlockColorCommand extends Core.Command {
	refresh() {
		const blocks = this._selectedBlocks();

		this.isEnabled = blocks.length > 0;
		this.value = blocks.length > 0
			? (blocks[0].getAttribute(BLOCK_ATTRIBUTE) || null)
			: null;
	}

	execute(options = {}) {
		const model = this.editor.model;
		const value = options.value && BY_VALUE.has(options.value) ? options.value : null;
		const blocks = this._selectedBlocks();

		model.change(writer => {
			for (const block of blocks) {
				if (value === null) {
					writer.removeAttribute(BLOCK_ATTRIBUTE, block);
				} else {
					writer.setAttribute(BLOCK_ATTRIBUTE, value, block);
				}
			}
		});
	}

	/**
	 * @returns {Array<Object>} the paragraphs the attribute is allowed on
	 */
	_selectedBlocks() {
		const schema = this.editor.model.schema;
		const selection = this.editor.model.document.selection;

		return Array.from(selection.getSelectedBlocks())
			.filter(block => schema.checkAttribute(block, BLOCK_ATTRIBUTE));
	}
}

/**
 * A toolbar dropdown for the Bootstrap text colors, inline and per paragraph.
 *
 * A real editor feature rather than Styles entries: the classes are in the schema
 * and converted both ways, so they survive presets whose General HTML Support
 * whitelist only covers <div>, and twelve entries stay out of the Styles dropdown.
 * Disabling the toolbar item only hides the button; existing colors stay readable
 * and are written back untouched.
 */
export class ColorPicker extends Core.Plugin {
	static get pluginName() {
		return 'ColorPicker';
	}

	init() {
		const editor = this.editor;
		const schema = editor.model.schema;

		// ---------- inline, <span class="text-*"> ----------
		schema.extend('$text', { allowAttributes: TEXT_ATTRIBUTE });
		schema.setAttributeProperties(TEXT_ATTRIBUTE, { isFormatting: true, copyOnEnter: false });

		editor.conversion.for('downcast').attributeToElement({
			model: TEXT_ATTRIBUTE,
			view: (value, { writer }) => {
				if (!BY_VALUE.has(value)) {
					return;
				}
				return writer.createAttributeElement(
					'span',
					{ class: `text-${value}` },
					{ priority: 7 }
				);
			}
		});

		for (const color of COLORS) {
			editor.conversion.for('upcast').elementToAttribute({
				view: {
					name: 'span',
					classes: `text-${color.value}`
				},
				model: {
					key: TEXT_ATTRIBUTE,
					value: color.value
				}
			});
		}

		// ---------- block, <p class="text-*"> ----------
		schema.extend('paragraph', { allowAttributes: BLOCK_ATTRIBUTE });

		// A hand written converter rather than attributeToAttribute: the class has to
		// be swapped, and attributeToAttribute would leave the previous "text-*" on
		// the element when the color changes.
		editor.conversion.for('downcast').add(dispatcher => {
			dispatcher.on(`attribute:${BLOCK_ATTRIBUTE}:paragraph`, (evt, data, conversionApi) => {
				if (!conversionApi.consumable.consume(data.item, evt.name)) {
					return;
				}

				const viewElement = conversionApi.mapper.toViewElement(data.item);
				if (!viewElement) {
					return;
				}

				const writer = conversionApi.writer;
				if (data.attributeOldValue) {
					writer.removeClass(`text-${data.attributeOldValue}`, viewElement);
				}
				if (data.attributeNewValue) {
					writer.addClass(`text-${data.attributeNewValue}`, viewElement);
				}
			});
		});

		for (const color of COLORS) {
			editor.conversion.for('upcast').elementToAttribute({
				view: {
					name: 'p',
					classes: `text-${color.value}`
				},
				model: {
					key: BLOCK_ATTRIBUTE,
					value: color.value
				}
			});
		}

		editor.commands.add(TEXT_COMMAND, new TextColorCommand(editor));
		editor.commands.add(BLOCK_COMMAND, new BlockColorCommand(editor));

		injectDropdownStyles();

		editor.ui.componentFactory.add('color', locale => this._createDropdown(locale));
	}

	_createDropdown(locale) {
		const editor = this.editor;
		const textCommand = editor.commands.get(TEXT_COMMAND);
		const blockCommand = editor.commands.get(BLOCK_COMMAND);

		const dropdown = UI.createDropdown(locale);
		const items = new Utils.Collection();

		const noneModel = new UI.ViewModel({
			scope: 'none',
			commandValue: null,
			label: 'No color',
			withText: true
		});
		noneModel.bind('isOn').to(
			textCommand, 'value',
			blockCommand, 'value',
			(text, block) => !text && !block
		);
		items.add({ type: 'button', model: noneModel });

		for (const scope of ['text', 'block']) {
			const command = scope === 'text' ? textCommand : blockCommand;

			items.add({ type: 'separator' });

			for (const color of COLORS) {
				const model = new UI.ViewModel({
					scope,
					commandValue: color.value,
					label: scope === 'text' ? `Text ${color.label}` : `Block ${color.label}`,
					class: `t3sb-color--${color.value}` + (scope === 'block' ? ' t3sb-color--block' : ''),
					withText: true
				});
				model.bind('isOn').to(command, 'value', value => value === color.value);
				items.add({ type: 'button', model });
			}
		}

		UI.addListToDropdown(dropdown, items);

		dropdown.buttonView.set({
			label: 'Color',
			icon: COLOR_ICON,
			tooltip: true
		});

		// createDropdown() already binds buttonView.isOn and .isEnabled - a second
		// bind() on the same observable throws. The dropdown stays usable as long as
		// one of the two commands is, so the inline one decides.
		dropdown.bind('isEnabled').to(textCommand, 'isEnabled');

		dropdown.buttonView.bind('tooltip').to(
			textCommand, 'value',
			blockCommand, 'value',
			(text, block) => {
				if (text) {
					return `Color (text-${text})`;
				}
				if (block) {
					return `Color (paragraph, text-${block})`;
				}
				return 'Color';
			}
		);

		dropdown.on('execute', evt => {
			const source = evt.source;

			if (source.scope === 'none') {
				editor.execute(TEXT_COMMAND, { value: null });
				editor.execute(BLOCK_COMMAND, { value: null });
			} else if (source.scope === 'block') {
				editor.execute(BLOCK_COMMAND, { value: source.commandValue });
			} else {
				editor.execute(TEXT_COMMAND, { value: source.commandValue });
			}

			editor.editing.view.focus();
		});

		return dropdown;
	}
}
