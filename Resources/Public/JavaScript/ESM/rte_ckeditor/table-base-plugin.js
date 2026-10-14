import * as Core from '@ckeditor/ckeditor5-core';

/**
 * Restores the Bootstrap base class "table" on <table> after a round trip.
 *
 * CKEditor's own General HTML Support integration for tables swallows that one
 * class before it collects the remaining ones (ckeditor5-html-support,
 * TableElementSupport: `consumable.consume( viewItem, { classes: 'table' } )`
 * runs ahead of `processViewAttributes()`, and a class already consumed is never
 * picked up again). It does that because its own table markup carries
 * <figure class="table"> as a wrapper, so the name is treated as internal.
 *
 * The consequence for the Styles entries "Table", "Table Hover" and "Table
 * Striped": the editor loads `<table class="table table-striped">`, keeps only
 * `table-striped` in the model and writes `<table class="table-striped">` back
 * on save. Without the base class none of the Bootstrap table rules apply, so
 * the table loses its look on open and save - and the Styles dropdown no longer
 * marks the entry as active, because not all of its classes are present.
 *
 * This converter runs after that integration (priority "lowest" against its
 * "low") and puts the base class back into the GHS attribute. From there the
 * existing downcast prints it on <table> again, in the editor and in the saved
 * markup alike.
 */

const GHS_ATTRIBUTE = 'htmlTableAttributes';
const BASE_CLASS = 'table';

/**
 * Bootstrap's table variants. A table carrying one of them but no base class is
 * content already damaged by the round trip described above - repaired here, so
 * the style need not be assigned a second time.
 * https://getbootstrap.com/docs/5.3/content/tables/
 */
const VARIANT_CLASSES = [
	'table-striped',
	'table-striped-columns',
	'table-hover',
	'table-bordered',
	'table-borderless',
	'table-sm',
	'table-group-divider'
];

export class TableBaseClass extends Core.Plugin {
	static get pluginName() {
		return 'TableBaseClass';
	}

	init() {
		const editor = this.editor;

		// Without the table feature there is no model element "table" to attach to,
		// and GHS then handles <table> through its generic "htmlTable" path, which
		// does not consume the class in the first place.
		if (!editor.plugins.has('TableEditing')) {
			return;
		}

		editor.conversion.for('upcast').add(dispatcher => {
			dispatcher.on('element:table', (evt, data, conversionApi) => {
				if (!data.modelRange) {
					return;
				}

				const classNames = Array.from(data.viewItem.getClassNames());
				const wantsBaseClass = classNames.includes(BASE_CLASS)
					|| classNames.some(name => VARIANT_CLASSES.includes(name));

				if (!wantsBaseClass) {
					return;
				}

				for (const item of data.modelRange.getItems()) {
					if (!item.is('element', 'table')) {
						continue;
					}

					const current = item.getAttribute(GHS_ATTRIBUTE) || {};
					const classes = Array.isArray(current.classes) ? current.classes : [];

					if (!classes.includes(BASE_CLASS)) {
						conversionApi.writer.setAttribute(
							GHS_ATTRIBUTE,
							{ ...current, classes: [BASE_CLASS, ...classes] },
							item
						);
					}

					return;
				}
			}, { priority: 'lowest' });
		});
	}
}
