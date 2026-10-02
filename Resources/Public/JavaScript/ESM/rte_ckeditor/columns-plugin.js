import * as Core from '@ckeditor/ckeditor5-core';
import * as UI from '@ckeditor/ckeditor5-ui';
import * as Utils from '@ckeditor/ckeditor5-utils';
import * as Widget from '@ckeditor/ckeditor5-widget';
import { enableBlockEscape } from '@t3sbs/t3sbootstrap/rte_ckeditor/block-escape.js';

/**
 * Bootstrap grid rows for the RTE.
 *
 * A row is <div class="row"> with two to four <div class="col-md-*">. col-md-*
 * and not col-*, so the columns stack below the md breakpoint like every other
 * multi-column element of this extension.
 */
const LAYOUTS = [
	{ columns: 2, colClass: 'col-md-6', label: '2 Spalten' },
	{ columns: 3, colClass: 'col-md-4', label: '3 Spalten' },
	{ columns: 4, colClass: 'col-md-3', label: '4 Spalten' }
];

const DEFAULT_COL_CLASS = 'col';
const COL_PATTERN = /^col(-.+)?$/;

const COLUMNS_ICON = `
	<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16">
		<g fill="currentColor">
			<path d="M13 2c.6 0 1 .4 1 1v10c0 .6-.4 1-1 1H3c-.6 0-1-.4-1-1V3c0-.6.4-1 1-1h10m0-1H3c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h10c1.1 0 2-.9 2-2V3c0-1.1-.9-2-2-2z"/>
			<path d="M6.5 2h1v12h-1zM8.5 2h1v12h-1z"/>
		</g>
	</svg>
`;

/**
 * Reads the col-* class off an upcast view element.
 */
function readColClass(viewElement, fallback = DEFAULT_COL_CLASS) {
	if (!viewElement || !viewElement.is('element', 'div')) {
		return fallback;
	}
	for (const name of viewElement.getClassNames()) {
		if (COL_PATTERN.test(name)) {
			return name;
		}
	}
	return fallback;
}

/**
 * Joins a base class list with the preserved extra classes.
 */
function joinClasses(base, extra) {
	return extra ? base + ' ' + extra : base;
}

/**
 * All classes of an upcast view element except the ones the model already
 * carries - "g-3" on a row, "text-center" on a column and so on.
 */
function readExtraClasses(viewElement, ...known) {
	const extra = [];
	for (const name of viewElement.getClassNames()) {
		if (!known.includes(name)) {
			extra.push(name);
		}
	}
	return extra.join(' ');
}

/**
 * True when the view element is a <div> carrying a col-* class.
 */
function isColumnElement(viewElement) {
	if (!viewElement || !viewElement.is('element', 'div')) {
		return false;
	}
	for (const name of viewElement.getClassNames()) {
		if (COL_PATTERN.test(name)) {
			return true;
		}
	}

	return false;
}

/**
 * True when this <div class="row"> is one the plugin can represent losslessly.
 *
 * bsRow allows bsCol children only; a claimed row the plugin cannot rebuild upcasts
 * to an empty bsRow and loses its content on the next save - the bug this guard
 * exists for. Claimed only when every element child is a column and there is at
 * least one (whitespace between columns is ignored, any other text is not). A row
 * that fails stays a plain editable div with its classes kept by General HTML Support.
 */
function isConvertibleRow(viewElement) {
	if (!viewElement || !viewElement.is('element', 'div') || !viewElement.hasClass('row')) {
		return false;
	}

	let columns = 0;

	for (const child of viewElement.getChildren()) {
		if (child.is('$text') || child.is('$textProxy')) {
			if (child.data.trim() !== '') {
				return false;
			}
			continue;
		}
		if (!isColumnElement(child)) {
			return false;
		}
		columns++;
	}

	return columns > 0;
}

/**
 * Closest `bsRow` ancestor of a model element, or null.
 */
function findRow(element) {
	if (!element) {
		return null;
	}
	return element.getAncestors().find(ancestor => ancestor.name === 'bsRow') || null;
}

/**
 * Styles for the dropdown entries AND the grey guide line around the columns.
 *
 * The guide line is here rather than in rte-columns.css because contentsCss is not
 * guaranteed to reach us, while a style element in the backend document always
 * covers both the toolbar and the editing root.
 */
function injectDropdownStyles() {
	const id = 't3sb-columns-plugin-styles';
	if (typeof document === 'undefined' || document.getElementById(id)) {
		return;
	}
	const style = document.createElement('style');
	style.id = id;
	style.textContent =
		// grey guide line per column - backend only, added by the editing downcast and
		// never saved to bodytext. !important because Bootstrap is loaded in the backend
		// and CKEditor styles the nested editables itself.
		'.ck-content .t3sb-rte-col{box-shadow:inset 0 0 0 1px #d4d4d4 !important;min-height:2.5em;}' +
		'.ck-content .t3sb-rte-col.ck-editor__nested-editable:focus{box-shadow:none !important;}' +
		'.ck-content .t3sb-rte-row{margin-bottom:1rem;}' +
		'.ck.ck-button[class*="t3sb-cols--"]{position:relative;padding-left:34px;}' +
		'.ck.ck-button[class*="t3sb-cols--"]::before{content:"";position:absolute;left:8px;top:50%;' +
		'width:18px;height:11px;margin-top:-6px;border:1px solid currentColor;opacity:.7;}' +
		'.ck.ck-button.t3sb-cols--2::after,.ck.ck-button.t3sb-cols--3::after,.ck.ck-button.t3sb-cols--4::after{' +
		'content:"";position:absolute;top:50%;margin-top:-6px;height:11px;border-left:1px solid currentColor;opacity:.7;}' +
		'.ck.ck-button.t3sb-cols--2::after{left:17px;}' +
		'.ck.ck-button.t3sb-cols--3::after{left:14px;box-shadow:6px 0 0 -5px currentColor;}' +
		'.ck.ck-button.t3sb-cols--4::after{left:12.5px;box-shadow:4px 0 0 -5px currentColor, 9px 0 0 -5px currentColor;}';
	document.head.appendChild(style);
}

export class ColumnsGrid extends Core.Plugin {
	static get pluginName() {
		return 'ColumnsGrid';
	}

	init() {
		const editor = this.editor;
		const schema = editor.model.schema;

		schema.register('bsRow', {
			allowWhere: '$block',
			allowChildren: ['bsCol'],
			allowAttributes: ['extraClasses']
		});

		schema.register('bsCol', {
			allowIn: 'bsRow',
			allowContentOf: '$root',
			// keeps the selection from escaping into a neighbouring column
			isLimit: true,
			allowAttributes: ['colClass', 'extraClasses']
		});

		// <div class="row"> -> bsRow. High priority so General HTML Support does
		// not claim the div first.
		editor.conversion.for('upcast').elementToElement({
			// Only rows that really contain columns. Everything else stays with the
			// normal conversion - claiming it would empty it out, because bsRow
			// accepts no other children.
			view: viewElement => isConvertibleRow(viewElement)
				? { name: true, classes: ['row'] }
				: null,
			model: (viewElement, { writer }) => writer.createElement('bsRow', {
				extraClasses: readExtraClasses(viewElement, 'row')
			}),
			converterPriority: 'high'
		});

		// <div class="col-md-6"> -> bsCol[colClass], the class is kept verbatim so
		// hand-written widths such as col-lg-8 survive a round trip.
		editor.conversion.for('upcast').elementToElement({
			// A col-* div only means something inside a row that became a bsRow.
			// Anywhere else it would upcast to a bsCol the schema allows nowhere,
			// and its content would be lost - so the same guard as above decides.
			view: viewElement => {
				const colClass = readColClass(viewElement, null);
				if (colClass === null) {
					return null;
				}
				if (!isConvertibleRow(viewElement.parent)) {
					return null;
				}

				return { name: true, classes: [colClass] };
			},
			model: (viewElement, { writer }) => {
				const colClass = readColClass(viewElement);

				return writer.createElement('bsCol', {
					colClass,
					extraClasses: readExtraClasses(viewElement, colClass)
				});
			},
			converterPriority: 'high'
		});

		editor.conversion.for('dataDowncast').elementToElement({
			model: { name: 'bsRow', attributes: ['extraClasses'] },
			view: (modelElement, { writer }) => writer.createContainerElement('div', {
				class: joinClasses('row', modelElement.getAttribute('extraClasses'))
			})
		});

		// listing colClass enables element reconversion, so a changed width
		// re-renders the div instead of doing nothing
		editor.conversion.for('dataDowncast').elementToElement({
			model: { name: 'bsCol', attributes: ['colClass', 'extraClasses'] },
			view: (modelElement, { writer }) => writer.createContainerElement('div', {
				class: joinClasses(
					modelElement.getAttribute('colClass') || DEFAULT_COL_CLASS,
					modelElement.getAttribute('extraClasses')
				)
			})
		});

		editor.conversion.for('editingDowncast').elementToElement({
			model: { name: 'bsRow', attributes: ['extraClasses'] },
			view: (modelElement, { writer }) => writer.createContainerElement('div', {
				class: joinClasses('row t3sb-rte-row', modelElement.getAttribute('extraClasses'))
			})
		});

		editor.conversion.for('editingDowncast').elementToElement({
			model: { name: 'bsCol', attributes: ['colClass', 'extraClasses'] },
			view: (modelElement, { writer }) => {
				const div = writer.createContainerElement('div', {
					class: joinClasses(
						(modelElement.getAttribute('colClass') || DEFAULT_COL_CLASS) + ' t3sb-rte-col',
						modelElement.getAttribute('extraClasses')
					)
				});
				return Widget.toWidgetEditable(div, writer, { label: 'column' });
			}
		});

		editor.commands.add('insertBsRow', new InsertRowCommand(editor));
		editor.commands.add('removeBsRow', new RemoveRowCommand(editor));

		editor.ui.componentFactory.add('columns', locale => this._createDropdown(locale));

		// Same dead end as with the alert, only that the way out leads past the
		// whole row - a paragraph between two columns would break the grid.
		// The column itself is never removed, that would change the layout.
		enableBlockEscape(this, block => {
			const column = block.parent;
			if (!column || column.name !== 'bsCol') {
				return null;
			}
			const row = findRow(block);
			return row ? { parent: column, boundary: row, removeWhenEmpty: false } : null;
		});
	}

	_createDropdown(locale) {
		const editor = this.editor;
		const insertCommand = editor.commands.get('insertBsRow');
		const removeCommand = editor.commands.get('removeBsRow');

		injectDropdownStyles();

		const dropdown = UI.createDropdown(locale);
		const items = new Utils.Collection();

		for (const layout of LAYOUTS) {
			const model = new UI.ViewModel({
				commandName: 'insertBsRow',
				commandValue: layout.columns,
				label: layout.label,
				class: `t3sb-cols--${layout.columns}`,
				withText: true
			});
			// marks the layout of the row the caret is currently in
			model.bind('isOn').to(insertCommand, 'value', value => value === layout.columns);
			items.add({ type: 'button', model });
		}

		items.add({ type: 'separator' });

		const removeModel = new UI.ViewModel({
			commandName: 'removeBsRow',
			label: 'Spalten aufheben',
			withText: true
		});
		removeModel.bind('isEnabled').to(removeCommand, 'isEnabled');
		items.add({ type: 'button', model: removeModel });

		UI.addListToDropdown(dropdown, items);

		dropdown.buttonView.set({
			label: 'Spalten',
			icon: COLUMNS_ICON
		});

		// Do NOT bind buttonView.isOn or buttonView.isEnabled here: createDropdown()
		// already binds both and Observable#bind throws on a second binding. Both
		// commands feed the state - binding to insertBsRow alone would disable the
		// dropdown inside a row, where "unwrap columns" is exactly what is needed.
		dropdown.bind('isEnabled').to(
			insertCommand, 'isEnabled',
			removeCommand, 'isEnabled',
			(canInsert, canRemove) => canInsert || canRemove
		);

		dropdown.on('execute', evt => {
			const { commandName, commandValue } = evt.source;
			if (commandName === 'removeBsRow') {
				editor.execute('removeBsRow');
			} else {
				editor.execute('insertBsRow', { columns: commandValue });
			}
			editor.editing.view.focus();
		});

		return dropdown;
	}
}

/**
 * Inserts a row with the requested number of columns at the current position.
 */
class InsertRowCommand extends Core.Command {
	execute(options = {}) {
		const model = this.editor.model;
		const layout = LAYOUTS.find(item => item.columns === Number(options.columns)) || LAYOUTS[0];

		model.change(writer => {
			const position = model.document.selection.getFirstPosition();
			const existingRow = position ? findRow(position.parent) : null;

			// caret already inside a row: convert it instead of nesting a new one
			if (existingRow) {
				this._convert(writer, existingRow, layout);
				return;
			}

			const row = writer.createElement('bsRow');

			for (let i = 0; i < layout.columns; i++) {
				const col = writer.createElement('bsCol', { colClass: layout.colClass });
				writer.append(writer.createElement('paragraph'), col);
				writer.append(col, row);
			}

			model.insertContent(row);

			const firstParagraph = row.getChild(0) && row.getChild(0).getChild(0);
			if (firstParagraph) {
				writer.setSelection(firstParagraph, 'in');
			}
		});
	}

	/**
	 * Changes an existing row to the requested number of columns. Surplus columns
	 * are not dropped - their content is appended to the last column that stays,
	 * so nothing an editor wrote can silently disappear.
	 */
	_convert(writer, row, layout) {
		const cols = Array.from(row.getChildren());
		const current = cols.length;

		if (layout.columns > current) {
			for (let i = current; i < layout.columns; i++) {
				const col = writer.createElement('bsCol', { colClass: layout.colClass });
				writer.append(writer.createElement('paragraph'), col);
				writer.append(col, row);
			}
		} else if (layout.columns < current) {
			const lastKept = cols[layout.columns - 1];
			for (let i = layout.columns; i < current; i++) {
				for (const child of Array.from(cols[i].getChildren())) {
					writer.move(writer.createRangeOn(child), writer.createPositionAt(lastKept, 'end'));
				}
				writer.remove(cols[i]);
			}
		}

		for (const col of Array.from(row.getChildren())) {
			writer.setAttribute('colClass', layout.colClass, col);
		}
	}

	refresh() {
		const model = this.editor.model;
		const position = model.document.selection.getFirstPosition();

		if (!position) {
			this.isEnabled = false;
			this.value = null;
			return;
		}

		const row = findRow(position.parent);

		if (row) {
			// inside a row the command converts, so it stays enabled and reports
			// the current column count for the isOn state of the dropdown entries
			this.isEnabled = true;
			this.value = Array.from(row.getChildren()).length;
			return;
		}

		// checkChild() against position.parent would ask whether a paragraph may
		// contain a row - it may not, and the dropdown would never enable.
		// findAllowedParent() walks up until it finds an element that accepts it.
		this.isEnabled = model.schema.findAllowedParent(position, 'bsRow') !== null;
		this.value = null;
	}
}

/**
 * Dissolves the row around the selection and keeps the content of every column.
 */
class RemoveRowCommand extends Core.Command {
	execute() {
		const model = this.editor.model;

		model.change(writer => {
			const row = findRow(model.document.selection.getFirstPosition().parent);
			if (!row) {
				return;
			}

			for (const col of Array.from(row.getChildren())) {
				for (const child of Array.from(col.getChildren())) {
					writer.move(writer.createRangeOn(child), writer.createPositionBefore(row));
				}
			}

			writer.remove(row);
		});
	}

	refresh() {
		const position = this.editor.model.document.selection.getFirstPosition();
		this.isEnabled = !!position && !!findRow(position.parent);
	}
}
