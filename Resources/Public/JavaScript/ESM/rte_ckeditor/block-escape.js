/**
 * Getting back OUT of a container.
 *
 * An alert or a column that sits at the very end of the text is a trap: the
 * caret is inside it, there is no paragraph behind it, and nothing an editor
 * can click creates one. The only way out used to be the source view.
 *
 * This is the gesture CKEditor's own block quote uses, applied to our
 * containers:
 *
 *   Enter     on an empty LAST  block  -> the block moves behind the container
 *   Backspace on an empty FIRST block  -> the block moves in front of it
 *
 * So: Enter once to open a new line, Enter again to step out. The same works
 * upwards with Backspace, which matters when the container is the first thing
 * in the field.
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

		// A freshly inserted container holds exactly one empty paragraph, which is
		// both empty and the last child - Enter would have escaped it right away,
		// and with removeWhenEmpty the alert would have been gone with it. Forward
		// only: Backspace has to keep working here, otherwise an empty container
		// would be a trap. Once the editor has typed something and pressed Enter,
		// there are two blocks and stepping out works as documented.
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
