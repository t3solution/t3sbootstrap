/**
 * Getting back OUT of a container - an alert or column at the end of the text was a
 * trap with no exit but the source view. Same gesture as CKEditor's block quote:
 * Enter on an empty LAST block escapes behind it, Backspace on an empty FIRST block
 * in front.
 */

/**
 * @param plugin   the CKEditor plugin instance (needs `editor` and `listenTo`)
 * @param resolve  (block) => { parent, boundary, removeWhenEmpty } | null
 *                 `parent`   the element the block has to be a direct child of
 *                 `boundary` the element the block is moved out of
 *                 `removeWhenEmpty` drop `boundary` if nothing is left in it
 */
export function enableBlockEscape(plugin, resolve) {
	const editor = plugin.editor;
	const viewDocument = editor.editing.view.document;
	const selection = editor.model.document.selection;

	const escape = (backwards) => {
		if (!selection.isCollapsed) {
			return false;
		}

		const block = selection.getLastPosition().parent;
		if (!block.is('element') || !block.isEmpty) {
			return false;
		}

		const target = resolve(block);
		if (!target || block.parent !== target.parent) {
			return false;
		}

		// Only from the outermost line - in the middle of the container the
		// normal Enter/Backspace behaviour is what an editor expects.
		if ((backwards ? block.previousSibling : block.nextSibling) !== null) {
			return false;
		}

		// A freshly inserted container holds one empty paragraph that is both empty and
		// last child - Enter would escape it at once and removeWhenEmpty would delete the
		// container with it. Enter is blocked here; Backspace must not be, or an empty
		// container is itself a trap.
		if (!backwards && block.previousSibling === null) {
			return false;
		}

		const outside = backwards
			? editor.model.createPositionBefore(target.boundary)
			: editor.model.createPositionAfter(target.boundary);

		// Do not build an invalid model - if a paragraph is not allowed next to
		// the container, leave the keystroke to CKEditor.
		if (!editor.model.schema.checkChild(outside.parent, block)) {
			return false;
		}

		editor.model.change(writer => {
			const position = backwards
				? writer.createPositionBefore(target.boundary)
				: writer.createPositionAfter(target.boundary);

			writer.move(writer.createRangeOn(block), position);
			writer.setSelection(block, 'in');

			if (target.removeWhenEmpty && target.boundary.isEmpty) {
				writer.remove(target.boundary);
			}
		});

		editor.editing.view.scrollToTheSelection();
		return true;
	};

	plugin.listenTo(viewDocument, 'enter', (evt, data) => {
		// Shift+Enter is a line break, not a request to leave.
		if (data.isSoft) {
			return;
		}
		if (escape(false)) {
			data.preventDefault();
			evt.stop();
		}
	});

	plugin.listenTo(viewDocument, 'delete', (evt, data) => {
		if (data.direction !== 'backward') {
			return;
		}
		if (escape(true)) {
			data.preventDefault();
			evt.stop();
		}
	});
}
