import * as Core from '@ckeditor/ckeditor5-core';
import * as UI from '@ckeditor/ckeditor5-ui';
import * as Utils from '@ckeditor/ckeditor5-utils';
import * as Widget from '@ckeditor/ckeditor5-widget';
import { enableBlockEscape } from '@t3sbs/t3sbootstrap/rte_ckeditor/block-escape.js';

/**
 * A plain <div> as an editable block widget.
 *
 * General HTML Support keeps a <div> in the markup but never makes a widget of it:
 * no selection frame, nothing a toolbar item could act on, so classes could only be
 * typed in the source view. This plugin claims every div the other plugins do not
 * own, which gives it a frame, a label and - through the class picker - a way to set
 * classes without touching the source.
 */
const ELEMENT = 'bsDiv';
const INSERT = 'insertBsDiv';
const REMOVE = 'removeBsDiv';

// Two levels are still readable in the editor; deeper is a maze.
const MAX_DEPTH = 2;

const COL_PATTERN = /^col(-.+)?$/;

// Divs that belong to another plugin. The guard runs in the upcast callback rather
// than relying on converter priority: with equal priority the registration order
// decides, and that is not something to depend on.
const CLAIMED_CLASSES = ['alert', 'row'];

const DIV_ICON = `
	<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16">
		<g fill="currentColor">
			<path d="M13 2c.6 0 1 .4 1 1v10c0 .6-.4 1-1 1H3c-.6 0-1-.4-1-1V3c0-.6.4-1 1-1h10m0-1H3c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h10c1.1 0 2-.9 2-2V3c0-1.1-.9-2-2-2z" opacity=".55"/>
			<path d="M5 5h6v1.5H5zM5 8h6v1.5H5zM5 11h3.5v1.5H5z"/>
		</g>
	</svg>
`;

/**
 * True when another plugin owns this div - alert, row or a column.
 */
function isClaimedByOther(viewElement) {
	for (const name of viewElement.getClassNames()) {
		if (CLAIMED_CLASSES.includes(name) || COL_PATTERN.test(name)) {
			return true;
		}
	}
	return false;
}

/**
 * Every class of the view element, as one string.
 */
function readClasses(viewElement) {
	return Array.from(viewElement.getClassNames()).join(' ');
}

/**
 * Every attribute except class.
 *
 * The element is consumed here, so General HTML Support no longer stores anything
 * for it - id, style and data-* would be dropped on the next save if they were not
 * carried in the model. An inline "column-count" is exactly such a case.
 */
function readAttributes(viewElement) {
	const attributes = {};
	for (const [key, value] of viewElement.getAttributes()) {
		if (key !== 'class') {
			attributes[key] = value;
		}
	}
	return attributes;
}

/**
 * Closest bsDiv ancestor of a model element, or null.
 */
function findDiv(element) {
	let node = element;
	while (node) {
		if (node.name === ELEMENT) {
			return node;
		}
		node = node.parent;
	}
	return null;
}

/**
 * How many bsDiv elements the position sits in.
 */
function depthOf(element) {
	let depth = 0;
	let node = element;
	while (node) {
		if (node.name === ELEMENT) {
			depth++;
		}
		node = node.parent;
	}
	return depth;
}

/**
 * Builds the view attributes for both downcasts.
 */
function viewAttributes(modelElement, extraClass) {
	const classes = modelElement.getAttribute('extraClasses') || '';
	const attributes = Object.assign({}, modelElement.getAttribute('htmlAttributes') || {});
	const classList = extraClass ? (classes ? classes + ' ' + extraClass : extraClass) : classes;

	if (classList) {
		attributes.class = classList;
	}

	return attributes;
}

/**
 * The grey outline and the class name in the corner - backend only, added by the
 * editing downcast and never saved. Without them an empty div is invisible, which
 * is the whole reason a plain div was unusable before.
 */
function injectStyles() {
	const id = 't3sb-div-plugin-styles';
	if (typeof document === 'undefined' || document.getElementById(id)) {
		return;
	}
	const style = document.createElement('style');
	style.id = id;
	style.textContent =
		'.ck-content .t3sb-rte-div{position:relative;box-shadow:inset 0 0 0 1px #d4d4d4 !important;' +
		'min-height:2.5em;padding:.75em .75em .25em !important;margin-bottom:1rem;}' +
		'.ck-content .t3sb-rte-div::before{content:attr(data-t3sb-label);position:absolute;top:0;right:0;' +
		'font-size:10px;line-height:1.4;padding:0 4px;background:#d4d4d4;color:#333;' +
		'font-family:monospace;pointer-events:none;}' +
		'.ck-content .t3sb-rte-div.ck-widget_selected{box-shadow:inset 0 0 0 1px #1a9aef !important;}';
	document.head.appendChild(style);
}

export class DivBlock extends Core.Plugin {
	static get pluginName() {
		return 'DivBlock';
	}

	init() {
		const editor = this.editor;

		editor.model.schema.register(ELEMENT, {
			// allowWhere $block puts it wherever a paragraph may go - including
			// inside a column, an alert and inside itself, which is the nesting.
			allowWhere: '$block',
			allowContentOf: '$root',
			allowAttributes: ['extraClasses', 'htmlAttributes']
		});

		editor.conversion.for('upcast').elementToElement({
			view: viewElement => {
				if (!viewElement.is('element', 'div') || isClaimedByOther(viewElement)) {
					return null;
				}
				return { name: true };
			},
			model: (viewElement, { writer }) => writer.createElement(ELEMENT, {
				extraClasses: readClasses(viewElement),
				htmlAttributes: readAttributes(viewElement)
			}),
			converterPriority: 'high'
		});

		// Both attributes are listed so a changed class re-renders the element
		// instead of doing nothing.
		editor.conversion.for('dataDowncast').elementToElement({
			model: { name: ELEMENT, attributes: ['extraClasses', 'htmlAttributes'] },
			view: (modelElement, { writer }) =>
				writer.createContainerElement('div', viewAttributes(modelElement))
		});

		editor.conversion.for('editingDowncast').elementToElement({
			model: { name: ELEMENT, attributes: ['extraClasses', 'htmlAttributes'] },
			view: (modelElement, { writer }) => {
				const attributes = viewAttributes(modelElement, 't3sb-rte-div');
				attributes['data-t3sb-label'] = modelElement.getAttribute('extraClasses') || 'div';
				const div = writer.createContainerElement('div', attributes);

				return Widget.toWidgetEditable(div, writer, { label: 'div' });
			}
		});

		editor.commands.add(INSERT, new InsertDivCommand(editor));
		editor.commands.add(REMOVE, new RemoveDivCommand(editor));

		injectStyles();

		editor.ui.componentFactory.add('div', locale => this._createDropdown(locale));

		// A div at the very end of the field would be a dead end otherwise: Enter on
		// an empty last line steps out of it, Backspace on an empty first line steps
		// out upwards.
		enableBlockEscape(this, block => {
			const div = block.parent && block.parent.name === ELEMENT ? block.parent : null;
			return div ? { parent: div, boundary: div, removeWhenEmpty: true } : null;
		});
	}

	_createDropdown(locale) {
		const editor = this.editor;
		const insertCommand = editor.commands.get(INSERT);
		const removeCommand = editor.commands.get(REMOVE);

		const dropdown = UI.createDropdown(locale);
		const items = new Utils.Collection();

		const insertModel = new UI.ViewModel({
			commandName: INSERT,
			label: 'Div einfügen',
			withText: true
		});
		insertModel.bind('isEnabled').to(insertCommand, 'isEnabled');
		items.add({ type: 'button', model: insertModel });

		items.add({ type: 'separator' });

		const removeModel = new UI.ViewModel({
			commandName: REMOVE,
			label: 'Div auflösen',
			withText: true
		});
		removeModel.bind('isEnabled').to(removeCommand, 'isEnabled');
		items.add({ type: 'button', model: removeModel });

		UI.addListToDropdown(dropdown, items);

		dropdown.buttonView.set({
			label: 'Div',
			icon: DIV_ICON,
			tooltip: true
		});

		// createDropdown() binds buttonView.isOn and .isEnabled itself - a second
		// bind() on the same observable throws.
		dropdown.bind('isEnabled').to(
			insertCommand, 'isEnabled',
			removeCommand, 'isEnabled',
			(canInsert, canRemove) => canInsert || canRemove
		);

		dropdown.on('execute', evt => {
			editor.execute(evt.source.commandName);
			editor.editing.view.focus();
		});

		return dropdown;
	}
}

/**
 * Wraps the selected blocks in a div, or inserts an empty one.
 */
class InsertDivCommand extends Core.Command {
	execute() {
		const model = this.editor.model;
		const selection = model.document.selection;

		model.change(writer => {
			const blocks = Array.from(selection.getSelectedBlocks())
				.filter(block => block.parent && model.schema.checkChild(block.parent, ELEMENT));

			// Only blocks that share a parent can be wrapped in one element -
			// anything else would tear the document apart.
			const sameParent = blocks.length > 0
				&& blocks.every(block => block.parent === blocks[0].parent);

			const div = writer.createElement(ELEMENT);

			if (sameParent) {
				writer.insert(div, blocks[0].parent, blocks[0].index);
				for (const block of blocks) {
					writer.move(writer.createRangeOn(block), writer.createPositionAt(div, 'end'));
				}
				writer.setSelection(div.getChild(0), 'in');
				return;
			}

			const paragraph = writer.createElement('paragraph');
			writer.append(paragraph, div);
			model.insertContent(div);
			writer.setSelection(paragraph, 'in');
		});
	}

	refresh() {
		const model = this.editor.model;
		const position = model.document.selection.getFirstPosition();

		if (!position) {
			this.isEnabled = false;
			return;
		}

		if (depthOf(position.parent) >= MAX_DEPTH) {
			this.isEnabled = false;
			return;
		}

		// checkChild() against position.parent would ask whether a paragraph may
		// contain a div - it may not. findAllowedParent() walks up instead.
		this.isEnabled = model.schema.findAllowedParent(position, ELEMENT) !== null;
	}
}

/**
 * Dissolves the div around the selection and keeps its content.
 */
class RemoveDivCommand extends Core.Command {
	execute() {
		const model = this.editor.model;

		model.change(writer => {
			const div = findDiv(model.document.selection.getFirstPosition().parent);
			if (!div) {
				return;
			}

			for (const child of Array.from(div.getChildren())) {
				writer.move(writer.createRangeOn(child), writer.createPositionBefore(div));
			}

			writer.remove(div);
		});
	}

	refresh() {
		const position = this.editor.model.document.selection.getFirstPosition();
		this.isEnabled = !!position && !!findDiv(position.parent);
	}
}
