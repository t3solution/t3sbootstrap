//#region src/tiny-emitter.ts
/**
* @fileoverview copy of `tiny-emitter` on npm, but converted to ESM
*/
var TinyEmitter = class {
	handlers;
	on(event, callback, ctx) {
		const handlers = this.handlers ?? (this.handlers = {});
		(handlers[event] || (handlers[event] = [])).push({
			fn: callback,
			ctx
		});
		return this;
	}
	once(event, callback, ctx) {
		const self = this;
		function listener() {
			self.off(event, listener);
			callback.apply(ctx, arguments);
		}
		listener.onceCb = callback;
		return this.on(event, listener, ctx);
	}
	emit(event, ...args) {
		const data = args;
		const evtArr = [...(this.handlers ?? (this.handlers = {}))[event] || []];
		let i = 0;
		const len = evtArr.length;
		for (; i < len; i += 1) evtArr[i].fn.apply(evtArr[i].ctx, data);
		return this;
	}
	off(event, callback) {
		const handlers = this.handlers ?? (this.handlers = {});
		const evts = handlers[event];
		const liveEvents = [];
		if (evts && callback) {
			for (let i = 0, len = evts.length; i < len; i += 1) if (evts[i].fn !== callback && evts[i].fn.onceCb !== callback) liveEvents.push(evts[i]);
		}
		if (liveEvents.length > 0) handlers[event] = liveEvents;
		else delete handlers[event];
		return this;
	}
};

//#endregion
//#region src/parallel.ts
/**
* Execute an array of functions in parallel.
* Calls the callback with an error if any function fails,
* otherwise calls it with null and an array of results.
*/
function parallel(fns, context, callback) {
	if (!callback) if (typeof context === "function") {
		callback = context;
		context = null;
	} else callback = noop;
	let pending = fns && fns.length;
	if (!pending) {
		callback(null, []);
		return;
	}
	let finished = false;
	const results = new Array(pending);
	for (let i = 0, len = fns.length; i < len; i++) fns[i].call(context, maybeDone(i));
	function maybeDone(i) {
		return function onDoneCheck(err, result) {
			if (finished) return;
			if (err) {
				callback(err, results);
				finished = true;
				return;
			}
			results[i] = result;
			if (!--pending) callback(null, results);
		};
	}
}
function noop() {}

//#endregion
//#region src/helpers.ts
/**
* Change a property or execute a function which will not have a transition
* @param elements DOM elements that won't be transitioned.
* @param callback A function which will be called while transition is set to 0ms.
*/
function skipTransitions(elements, callback) {
	const zero = "0ms";
	const data = elements.map((element) => {
		const { style } = element;
		const duration = style.transitionDuration;
		const delay = style.transitionDelay;
		style.transitionDuration = zero;
		style.transitionDelay = zero;
		return {
			duration,
			delay
		};
	});
	callback();
	elements[0].offsetWidth;
	for (let i = 0; i < elements.length; i += 1) {
		elements[i].style.transitionDuration = data[i].duration;
		elements[i].style.transitionDelay = data[i].delay;
	}
}
/**
* Apply styles without a transition.
* Array of transition objects.
*/
function styleImmediately(objects) {
	if (objects.length > 0) skipTransitions(objects.map((obj) => obj.item.element), () => {
		for (const obj of objects) {
			obj.item.applyCss(obj.styles);
			obj.callback();
		}
	});
}
function arrayUnique(items) {
	return [...new Set(items)];
}
const EXPECTED_WIDTH = 10;
let value = null;
function testComputedSize() {
	if (value !== null) return value;
	const element = document.body || document.documentElement;
	const div = document.createElement("div");
	div.style.cssText = "width:10px;padding:2px;box-sizing:border-box;";
	element.append(div);
	const { width } = globalThis.getComputedStyle(div, null);
	value = Math.round(getNumber(width)) === EXPECTED_WIDTH;
	div.remove();
	return value;
}
const DEFAULT_NUMBER = 0;
/**
* Always returns a numeric value, given a value. Logic from jQuery's `isNumeric`.
* @param value Possibly numeric value.
* @return `value` or zero if `value` isn't numeric.
*/
function getNumber(value) {
	return Number.parseFloat(String(value)) || DEFAULT_NUMBER;
}
/**
* Retrieve the computed style for an element, parsed as a float.
* @param element Element to get style for.
* @param style Style property.
* @param styles Optionally include clean styles to use instead of asking for
* them again.
* @return The parsed computed value or zero if that fails because IE will
* return 'auto' when the element doesn't have margins instead of the computed style.
*/
function getNumberStyle(element, style, styles = globalThis.getComputedStyle(element, null)) {
	let value = getNumber(styles[style]);
	if (!testComputedSize() && style === "width") value += getNumber(styles.paddingLeft) + getNumber(styles.paddingRight) + getNumber(styles.borderLeftWidth) + getNumber(styles.borderRightWidth);
	else if (!testComputedSize() && style === "height") value += getNumber(styles.paddingTop) + getNumber(styles.paddingBottom) + getNumber(styles.borderTopWidth) + getNumber(styles.borderBottomWidth);
	return value;
}
/**
* Returns the outer width of an element, optionally including its margins.
*
* There are a few different methods for getting the width of an element, none of
* which work perfectly for all Shuffle's use cases.
*
* 1. getBoundingClientRect() `left` and `right` properties.
*   - Accounts for transform scaled elements, making it useless for Shuffle
*   elements which have shrunk.
* 2. The `offsetWidth` property.
*   - This value stays the same regardless of the elements transform property,
*   however, it does not return subpixel values.
* 3. getComputedStyle()
*   - This works great Chrome, Firefox, Safari, but IE<=11 does not include
*   padding and border when box-sizing: border-box is set, requiring a feature
*   test and extra work to add the padding back for IE and other browsers which
*   follow the W3C spec here.
*
* @param element The element.
* @param includeMargins Whether to include margins.
* @return The width and height.
*/
function getSize(element, includeMargins = false) {
	const styles = globalThis.getComputedStyle(element, null);
	let width = getNumberStyle(element, "width", styles);
	let height = getNumberStyle(element, "height", styles);
	if (includeMargins) {
		const marginLeft = getNumberStyle(element, "marginLeft", styles);
		const marginRight = getNumberStyle(element, "marginRight", styles);
		const marginTop = getNumberStyle(element, "marginTop", styles);
		const marginBottom = getNumberStyle(element, "marginBottom", styles);
		width += marginLeft + marginRight;
		height += marginTop + marginBottom;
	}
	return {
		width,
		height
	};
}

//#endregion
//#region src/point.ts
var Point = class {
	x;
	y;
	/**
	* Represents a coordinate pair.
	* @param x X coordinate.
	* @param y Y coordinate.
	*/
	constructor(x, y) {
		this.x = getNumber(x);
		this.y = getNumber(y);
	}
	/**
	* Whether two points are equal.
	* @param pointA Point A.
	* @param pointB Point B.
	* @return Whether the points are equal.
	*/
	static equals(pointA, pointB) {
		return pointA.x === pointB.x && pointA.y === pointB.y;
	}
};

//#endregion
//#region src/rect.ts
var Rect = class {
	id;
	left;
	top;
	width;
	height;
	/**
	* Class for representing rectangular regions.
	* https://github.com/google/closure-library/blob/master/closure/goog/math/rect.js
	* @param x Left.
	* @param y Top.
	* @param width Width.
	* @param height Height.
	* @param id Identifier.
	*/
	constructor(x, y, width, height, id) {
		this.id = id;
		this.left = x;
		this.top = y;
		this.width = width;
		this.height = height;
	}
	/**
	* Returns whether two rectangles intersect.
	* @param rectA A rectangle.
	* @param rectB A rectangle.
	* @return Whether a and b intersect.
	*/
	static intersects(rectA, rectB) {
		return rectA.left < rectB.left + rectB.width && rectB.left < rectA.left + rectA.width && rectA.top < rectB.top + rectB.height && rectB.top < rectA.top + rectA.height;
	}
};

//#endregion
//#region src/constants.ts
const Classes = {
	BASE: "shuffle",
	SHUFFLE_ITEM: "shuffle-item",
	VISIBLE: "shuffle-item--visible",
	HIDDEN: "shuffle-item--hidden"
};
const ALL_ITEMS = "all";
const FILTER_ATTRIBUTE_KEY = "groups";
const FilterMode = {
	ANY: "any",
	ALL: "all"
};
const EventType = {
	LAYOUT: "shuffle:layout",
	REMOVED: "shuffle:removed"
};
const DEFAULT_OPTIONS = {
	group: ALL_ITEMS,
	speed: 250,
	easing: "cubic-bezier(0.4, 0.0, 0.2, 1)",
	itemSelector: "*",
	sizer: null,
	gutterWidth: 0,
	columnWidth: 0,
	delimiter: null,
	buffer: 0,
	columnThreshold: .01,
	initialSort: null,
	staggerAmount: 15,
	staggerAmountMax: 150,
	useTransforms: true,
	filterMode: FilterMode.ANY,
	isCentered: false,
	isRTL: false,
	roundTransforms: true
};

//#endregion
//#region src/shuffle-item.ts
let id$1 = 0;
var ShuffleItem = class ShuffleItem {
	id;
	element;
	isRTL;
	isVisible;
	isHidden;
	scale = 1;
	point = new Point();
	constructor(element, isRTL) {
		id$1 += 1;
		this.id = id$1;
		this.element = element;
		/**
		* Set correct direction of item
		*/
		this.isRTL = Boolean(isRTL);
		/**
		* Used to separate items for layout and shrink.
		*/
		this.isVisible = true;
		/**
		* Used to determine if a transition will happen. By the time the _layout
		* and _shrink methods get the ShuffleItem instances, the `isVisible` value
		* has already been changed by the separation methods, so this property is
		* needed to know if the item was visible/hidden before the shrink/layout.
		*/
		this.isHidden = false;
	}
	show() {
		this.isVisible = true;
		this.element.classList.remove(Classes.HIDDEN);
		this.element.classList.add(Classes.VISIBLE);
		this.element.removeAttribute("aria-hidden");
	}
	hide() {
		this.isVisible = false;
		this.element.classList.remove(Classes.VISIBLE);
		this.element.classList.add(Classes.HIDDEN);
		this.element.setAttribute("aria-hidden", "true");
	}
	init() {
		this.addClasses([Classes.SHUFFLE_ITEM, Classes.VISIBLE]);
		this.applyCss(ShuffleItem.Css.INITIAL);
		this.applyCss(this.isRTL ? ShuffleItem.Css.DIRECTION.rtl : ShuffleItem.Css.DIRECTION.ltr);
		this.scale = ShuffleItem.Scale.VISIBLE;
		this.point = new Point();
	}
	addClasses(classes) {
		for (const className of classes) this.element.classList.add(className);
	}
	removeClasses(classes) {
		for (const className of classes) this.element.classList.remove(className);
	}
	applyCss(obj) {
		for (const [key, value] of Object.entries(obj)) this.element.style[key] = String(value);
	}
	dispose() {
		this.removeClasses([
			Classes.HIDDEN,
			Classes.VISIBLE,
			Classes.SHUFFLE_ITEM
		]);
		this.element.removeAttribute("style");
		this.element = null;
	}
	static Css = {
		INITIAL: {
			position: "absolute",
			top: 0,
			visibility: "visible",
			willChange: "transform"
		},
		DIRECTION: {
			ltr: { left: 0 },
			rtl: { right: 0 }
		},
		VISIBLE: {
			before: {
				opacity: 1,
				visibility: "visible"
			},
			after: { transitionDelay: "" }
		},
		HIDDEN: {
			before: { opacity: 0 },
			after: {
				visibility: "hidden",
				transitionDelay: ""
			}
		}
	};
	static Scale = {
		VISIBLE: 1,
		HIDDEN: .001
	};
};
/**
* Toggles the visible and hidden class names.
* @param Object with visible and hidden arrays.
*/
function toggleFilterClasses({ visible, hidden }) {
	for (const item of visible) item.show();
	for (const item of hidden) item.hide();
}
/**
* Set the initial css for each item
* @param items Set to initialize.
*/
function initItems(items) {
	for (const item of items) item.init();
}
/**
* Remove element reference and styles.
* @param items Set to dispose.
*/
function disposeItems(items) {
	for (const item of items) item.dispose();
}
function applyHiddenState(item) {
	item.scale = ShuffleItem.Scale.HIDDEN;
	item.isHidden = true;
	item.applyCss(ShuffleItem.Css.HIDDEN.before);
	item.applyCss(ShuffleItem.Css.HIDDEN.after);
}

//#endregion
//#region src/sorter.ts
/**
* Fisher-Yates shuffle.
* http://stackoverflow.com/a/962890/373422
* https://bost.ocks.org/mike/shuffle/
* @param array Array to shuffle.
* @return Randomly sorted array.
*/
function randomize(array) {
	let count = array.length;
	while (count) {
		count -= 1;
		const index = Math.floor(Math.random() * (count + 1));
		const temp = array[index];
		array[index] = array[count];
		array[count] = temp;
	}
	return array;
}
const defaults = {
	reverse: false,
	by: null,
	compare: null,
	randomize: false,
	key: "element"
};
/**
* You can return `undefined` from the `by` function to revert to DOM order.
* @param arr Array to sort.
* @param options Sorting options.
* @return The sorted array.
*/
function sorter(arr, options) {
	if (!Array.isArray(arr)) return [];
	const opts = {
		...defaults,
		...options
	};
	const original = [...arr];
	let revert = false;
	if (arr.length === 0) return [];
	if (opts.randomize) return randomize(arr);
	if (typeof opts.by === "function") {
		const sortBy = opts.by;
		arr.sort((itemA, itemB) => {
			if (revert) return 0;
			const itemAValue = itemA[opts.key];
			const itemBValue = itemB[opts.key];
			const valA = sortBy(itemAValue);
			const valB = sortBy(itemBValue);
			if (valA === void 0 && valB === void 0) {
				revert = true;
				return 0;
			}
			if (valA < valB || valA === "sortFirst" || valB === "sortLast") return -1;
			if (valA > valB || valA === "sortLast" || valB === "sortFirst") return 1;
			return 0;
		});
	} else if (typeof opts.compare === "function") arr.sort(opts.compare);
	if (revert) return original;
	if (opts.reverse) arr.reverse();
	return arr;
}

//#endregion
//#region src/transition-manager.ts
var TransitionManager = class {
	#transitions = /* @__PURE__ */ new Map();
	#count = 0;
	#uniqueId() {
		this.#count += 1;
		return `transitionend${this.#count}`;
	}
	waitForTransition(element, callback) {
		const id = this.#uniqueId();
		const listener = (event) => {
			if (event.currentTarget === event.target) {
				this.cancelTransition(id);
				callback(event);
			}
		};
		element.addEventListener("transitionend", listener);
		this.#transitions.set(id, {
			element,
			listener
		});
		return id;
	}
	cancelTransition(id) {
		const entry = this.#transitions.get(id);
		if (entry) {
			entry.element.removeEventListener("transitionend", entry.listener);
			this.#transitions.delete(id);
			return true;
		}
		return false;
	}
	cancelAll() {
		for (const id of this.#transitions.keys()) this.cancelTransition(id);
	}
};
function createTransitionManager() {
	return new TransitionManager();
}

//#endregion
//#region src/layout.ts
/**
* Determine the number of columns an items spans.
* @param itemWidth Width of the item.
* @param columnWidth Width of the column (includes gutter).
* @param columns Total number of columns.
* @param threshold A buffer value for the size of the column to fit.
* @return The column span.
*/
function getColumnSpan(itemWidth, columnWidth, columns, threshold) {
	let columnSpan = itemWidth / columnWidth;
	if (Math.abs(Math.round(columnSpan) - columnSpan) < threshold) columnSpan = Math.round(columnSpan);
	return Math.min(Math.ceil(columnSpan), columns);
}
/**
* Retrieves the column set to use for placement.
* @param positions The position array.
* @param columnSpan The number of columns this current item spans.
* @param columns The total columns in the grid.
* @return An array of numbers representing the column set.
*/
function getAvailablePositions(positions, columnSpan, columns) {
	if (columnSpan === 1) return positions;
	const available = [];
	for (let i = 0; i <= columns - columnSpan; i += 1) available.push(Math.max(...positions.slice(i, i + columnSpan)));
	return available;
}
/**
* Find index of short column, the first from the left where this item will go.
*
* @param positions The array to search for the smallest number.
* @param buffer Optional buffer which is very useful when the height is a percentage of the width.
* @return Index of the short column.
*/
function getShortColumn(positions, buffer) {
	const minPosition = Math.min(...positions);
	for (let i = 0, len = positions.length; i < len; i += 1) if (positions[i] >= minPosition - buffer && positions[i] <= minPosition + buffer) return i;
	return 0;
}
/**
* Determine the location of the next item, based on its size.
* @param params Object with itemSize, positions, gridSize, total, threshold, and buffer.
* @return The point position for the item.
*/
function getItemPosition({ itemSize, positions, gridSize, total, threshold, buffer }) {
	const span = getColumnSpan(itemSize.width, gridSize, total, threshold);
	const setY = getAvailablePositions(positions, span, total);
	const shortColumnIndex = getShortColumn(setY, buffer);
	const point = new Point(gridSize * shortColumnIndex, setY[shortColumnIndex]);
	const setHeight = setY[shortColumnIndex] + itemSize.height;
	for (let i = 0; i < span; i += 1) positions[shortColumnIndex + i] = setHeight;
	return point;
}
/**
* This method attempts to center items. This method could potentially be slow
* with a large number of items because it must place items, then check every
* previous item to ensure there is no overlap.
* @param itemRects Item data objects.
* @param containerWidth Width of the containing element.
* @return An array of centered points.
*/
function getCenteredPositions(itemRects, containerWidth) {
	const rowMap = {};
	for (const itemRect of itemRects) if (rowMap[itemRect.top] === void 0) rowMap[itemRect.top] = [itemRect];
	else rowMap[itemRect.top].push(itemRect);
	const rects = [];
	const rows = [];
	const centeredRows = [];
	for (const itemRects of Object.values(rowMap)) {
		rows.push(itemRects);
		const lastItem = itemRects.at(-1);
		const end = lastItem.left + lastItem.width;
		const offset = Math.round((containerWidth - end) / 2);
		let finalRects = itemRects;
		let canMove = false;
		if (offset > 0) {
			const newRects = [];
			canMove = itemRects.every((comparisonRect) => {
				const newRect = new Rect(comparisonRect.left + offset, comparisonRect.top, comparisonRect.width, comparisonRect.height, comparisonRect.id);
				const noOverlap = !rects.some((rectangle) => Rect.intersects(newRect, rectangle));
				newRects.push(newRect);
				return noOverlap;
			});
			if (canMove) finalRects = newRects;
		}
		if (!canMove) {
			let intersectingRect;
			if (itemRects.some((itemRect) => rects.some((comparisonRect) => {
				const intersects = Rect.intersects(itemRect, comparisonRect);
				if (intersects) intersectingRect = comparisonRect;
				return intersects;
			}))) {
				const rowIndex = centeredRows.findIndex((items) => items.includes(intersectingRect));
				centeredRows.splice(rowIndex, 1, rows[rowIndex]);
			}
		}
		rects.push(...finalRects);
		centeredRows.push(finalRects);
	}
	return centeredRows.flat().toSorted((rowA, rowB) => rowA.id - rowB.id).map((itemRect) => new Point(itemRect.left, itemRect.top));
}

//#endregion
//#region src/shuffle.ts
let id = 0;
var Shuffle = class Shuffle extends TinyEmitter {
	element;
	sizer;
	options;
	lastSort;
	group;
	lastFilter;
	isEnabled;
	isDestroyed;
	isInitialized;
	isTransitioning;
	id;
	items;
	sortedItems;
	visibleItems;
	cols;
	colWidth;
	containerWidth;
	positions;
	#transitionManager;
	#queue;
	#rafId;
	#resizeObserver = null;
	/**
	* Categorize, sort, and filter a responsive grid of items.
	*
	* @param element An element which is the parent container for the grid items.
	* @param options Options object.
	* @constructor
	*/
	constructor(element, options = {}) {
		super();
		this.options = {
			...Shuffle.options,
			...options
		};
		this.lastSort = {};
		this.group = Shuffle.ALL_ITEMS;
		this.lastFilter = Shuffle.ALL_ITEMS;
		this.isEnabled = true;
		this.isDestroyed = false;
		this.isInitialized = false;
		this.#transitionManager = createTransitionManager();
		this.isTransitioning = false;
		this.#queue = [];
		const el = this.#getElementOption(element);
		if (!el) throw new TypeError("Shuffle needs to be initialized with an element.");
		this.element = el;
		this.id = `shuffle_${id}`;
		id += 1;
		this.items = this.#getItems();
		this.sortedItems = this.items;
		this.sizer = this.#getElementOption(this.options.sizer);
		this.element.classList.add(Shuffle.Classes.BASE);
		initItems(this.items);
		if (document.readyState !== "complete") {
			const layout = this.layout.bind(this);
			window.addEventListener("load", function onLoad() {
				window.removeEventListener("load", onLoad);
				layout();
			});
		}
		const containerCss = globalThis.getComputedStyle(this.element, null);
		const containerWidth = Shuffle.getSize(this.element).width;
		this.#validateStyles(containerCss);
		this.#setColumns(containerWidth);
		this.filter(this.options.group, this.options.initialSort);
		this.#rafId = null;
		this.#resizeObserver = new ResizeObserver(this.#handleResizeCallback.bind(this));
		this.#resizeObserver.observe(this.element);
		this.element.offsetWidth;
		this.setItemTransitions(this.items);
		this.element.style.transition = `height ${this.options.speed}ms ${this.options.easing}`;
		this.isInitialized = true;
	}
	on(event, callback, context) {
		return super.on(event, callback, context);
	}
	once(event, callback, context) {
		return super.once(event, callback, context);
	}
	emit(event, data) {
		if (this.isDestroyed) return this;
		return super.emit(event, data);
	}
	off(event, callback) {
		return super.off(event, callback);
	}
	/**
	* Retrieve an element from an option.
	* @param option The option to check.
	* @return The plain element or null.
	*/
	#getElementOption(option) {
		if (typeof option === "string") return this.element ? this.element.querySelector(option) : document.querySelector(option);
		if (option && "nodeType" in option && option.nodeType && option.nodeType === 1) return option;
		if (option && "jquery" in option) return option[0];
		return null;
	}
	/**
	* Ensures the shuffle container has the css styles it needs applied to it.
	* @param styles Key value pairs for position and overflow.
	*/
	#validateStyles(styles) {
		if (styles.position === "static") this.element.style.position = "relative";
		if (styles.overflow !== "hidden") this.element.style.overflow = "hidden";
	}
	/**
	* Filter the elements by a category.
	* @param category Category to filter by. If it's given, the last category will be used to filter the items.
	* @param collection Optionally filter a collection. Defaults to all the items.
	* @return Object with visible and hidden arrays.
	*/
	#filter(category = this.lastFilter, collection = this.items) {
		const set = this.#getFilteredSets(category, collection);
		toggleFilterClasses(set);
		this.lastFilter = category;
		if (typeof category === "string") this.group = category;
		return set;
	}
	/**
	* Returns an object containing the visible and hidden elements.
	* @param category Category or function to filter by.
	* @param items A collection of items to filter.
	*/
	#getFilteredSets(category, items) {
		let visible = [];
		const hidden = [];
		if (category === Shuffle.ALL_ITEMS) visible = items;
		else for (const item of items) if (this.#doesPassFilter(category, item.element)) visible.push(item);
		else hidden.push(item);
		return {
			visible,
			hidden
		};
	}
	/**
	* Test an item to see if it passes a category.
	* @param category Category or function to filter by.
	* @param element An element to test.
	* @return Whether it passes the category/filter.
	*/
	#doesPassFilter(category, element) {
		if (typeof category === "function") return category.call(element, element, this);
		const attr = element.dataset[Shuffle.FILTER_ATTRIBUTE_KEY] ?? "";
		const keys = this.options.delimiter ? attr.split(this.options.delimiter) : JSON.parse(attr);
		function testCategory(category) {
			return keys.includes(category);
		}
		if (Array.isArray(category)) {
			if (this.options.filterMode === FilterMode.ANY) return category.some(testCategory);
			return category.every(testCategory);
		}
		return keys.includes(category);
	}
	/**
	* Updates the visible item count.
	*/
	#updateItemCount() {
		this.visibleItems = this.#getFilteredItems().length;
	}
	/**
	* Sets css transform transition on a group of elements. This is not executed
	* at the same time as `item.init` so that transitions don't occur upon
	* initialization of a new Shuffle instance.
	* @param items Shuffle items to set transitions on.
	* @protected
	*/
	setItemTransitions(items) {
		const { speed, easing } = this.options;
		const positionProps = this.options.useTransforms ? ["transform"] : ["top", "left"];
		const cssProps = Object.keys(ShuffleItem.Css.HIDDEN.before);
		const properties = [...positionProps, ...cssProps].join(",");
		for (const item of items) {
			item.element.style.transitionDuration = `${speed}ms`;
			item.element.style.transitionTimingFunction = easing;
			item.element.style.transitionProperty = properties;
		}
	}
	#getItems() {
		return Array.from(this.element.children).filter((el) => el.matches(this.options.itemSelector)).map((el) => new ShuffleItem(el, this.options.isRTL));
	}
	/**
	* Combine the current items array with a new one and sort it by DOM order.
	* @param items Items to track.
	*/
	#mergeNewItems(items) {
		const children = Array.from(this.element.children);
		return sorter([...this.items, ...items], { by(element) {
			return children.indexOf(element);
		} });
	}
	#getFilteredItems() {
		return this.items.filter((item) => item.isVisible);
	}
	#getConcealedItems() {
		return this.items.filter((item) => !item.isVisible);
	}
	/**
	* Returns the column size, based on column width and sizer options.
	* @param containerWidth Size of the parent container.
	* @param gutterSize Size of the gutters.
	*/
	#getColumnSize(containerWidth, gutterSize) {
		let size;
		if (typeof this.options.columnWidth === "function") size = this.options.columnWidth(containerWidth);
		else if (this.sizer) size = Shuffle.getSize(this.sizer).width;
		else if (this.options.columnWidth) size = this.options.columnWidth;
		else if (this.items.length > 0) size = Shuffle.getSize(this.items[0].element, true).width;
		else size = containerWidth;
		if (size === 0) size = containerWidth;
		return size + gutterSize;
	}
	/**
	* Returns the gutter size, based on gutter width and sizer options.
	* @param containerWidth Size of the parent container.
	*/
	#getGutterSize(containerWidth) {
		if (typeof this.options.gutterWidth === "function") return this.options.gutterWidth(containerWidth);
		if (this.sizer) return getNumberStyle(this.sizer, "marginLeft");
		if (this.options.gutterWidth) return this.options.gutterWidth;
		return 0;
	}
	/**
	* Calculate the number of columns to be used. Gets css if using sizer element.
	* @param containerWidth Optionally specify a container width if it's already available.
	*/
	#setColumns(containerWidth = Shuffle.getSize(this.element).width) {
		const gutter = this.#getGutterSize(containerWidth);
		const columnWidth = this.#getColumnSize(containerWidth, gutter);
		let calculatedColumns = (containerWidth + gutter) / columnWidth;
		const threshold = this.options.columnThreshold;
		if (Math.abs(Math.round(calculatedColumns) - calculatedColumns) < threshold) calculatedColumns = Math.round(calculatedColumns);
		this.cols = Math.max(Math.floor(calculatedColumns || 0), 1);
		this.containerWidth = containerWidth;
		this.colWidth = columnWidth;
	}
	/**
	* Adjust the height of the grid
	*/
	#setContainerSize() {
		this.element.style.height = `${this.#getContainerSize()}px`;
	}
	/**
	* Based on the column heights, it returns the biggest one.
	*/
	#getContainerSize() {
		return Math.max(...this.positions);
	}
	/**
	* Get the clamped stagger amount.
	* @param index Index of the item to be staggered.
	*/
	#getStaggerAmount(index) {
		return Math.min(index * this.options.staggerAmount, this.options.staggerAmountMax);
	}
	/**
	* Zeros out the y columns array, which is used to determine item placement.
	*/
	#resetCols() {
		let i = this.cols;
		this.positions = [];
		while (i) {
			i -= 1;
			this.positions.push(0);
		}
	}
	/**
	* Loops through each item that should be shown and calculates the x, y position.
	* @param items Array of items that will be shown/layed out in order in their array.
	*/
	#layout(items) {
		const itemPositions = this.#getNextPositions(items);
		let count = 0;
		for (let i = 0; i < items.length; i += 1) {
			const item = items[i];
			const callback = () => {
				item.applyCss(ShuffleItem.Css.VISIBLE.after);
			};
			if (Point.equals(item.point, itemPositions[i]) && !item.isHidden) {
				item.applyCss(ShuffleItem.Css.VISIBLE.before);
				callback();
				continue;
			}
			item.point = itemPositions[i];
			item.scale = ShuffleItem.Scale.VISIBLE;
			item.isHidden = false;
			const styles = this.getStylesForTransition(item, ShuffleItem.Css.VISIBLE.before);
			styles.transitionDelay = `${this.#getStaggerAmount(count)}ms`;
			this.#queue.push({
				item,
				styles,
				callback
			});
			count += 1;
		}
	}
	/**
	* Return an array of Point instances representing the future positions of
	* each item.
	* @param items Array of sorted shuffle items.
	*/
	#getNextPositions(items) {
		if (this.options.isCentered) {
			const itemsData = items.map((item, i) => {
				const itemSize = Shuffle.getSize(item.element, true);
				const point = this.#getItemPosition(itemSize);
				return new Rect(point.x, point.y, itemSize.width, itemSize.height, i);
			});
			return this.getTransformedPositions(itemsData, this.containerWidth);
		}
		return items.map((item) => this.#getItemPosition(Shuffle.getSize(item.element, true)));
	}
	/**
	* Determine the location of the next item, based on its size.
	* @param itemSize Object with width and height.
	*/
	#getItemPosition(itemSize) {
		return getItemPosition({
			itemSize,
			positions: this.positions,
			gridSize: this.colWidth,
			total: this.cols,
			threshold: this.options.columnThreshold,
			buffer: this.options.buffer
		});
	}
	/**
	* Mutate positions before they're applied.
	* @param itemRects Item data objects.
	* @param containerWidth Width of the containing element.
	* @protected
	*/
	getTransformedPositions(itemRects, containerWidth) {
		return getCenteredPositions(itemRects, containerWidth);
	}
	/**
	* Hides the elements that don't match our filter.
	* @param collection Collection to shrink.
	*/
	#shrink(collection = this.#getConcealedItems()) {
		let count = 0;
		for (const item of collection) {
			const callback = () => {
				item.applyCss(ShuffleItem.Css.HIDDEN.after);
			};
			if (item.isHidden) {
				item.applyCss(ShuffleItem.Css.HIDDEN.before);
				callback();
				continue;
			}
			item.scale = ShuffleItem.Scale.HIDDEN;
			item.isHidden = true;
			const styles = this.getStylesForTransition(item, ShuffleItem.Css.HIDDEN.before);
			styles.transitionDelay = `${this.#getStaggerAmount(count)}ms`;
			this.#queue.push({
				item,
				styles,
				callback
			});
			count += 1;
		}
	}
	/**
	* Resize handler.
	* @param entries
	*/
	#handleResizeCallback(entries) {
		if (!this.isEnabled || this.isDestroyed) return;
		for (const entry of entries) if (Math.round(entry.contentRect.width) !== Math.round(this.containerWidth)) {
			cancelAnimationFrame(this.#rafId);
			this.#rafId = requestAnimationFrame(() => {
				this.update();
			});
		}
	}
	/**
	* Returns styles which will be applied to the an item for a transition.
	* @param item Item to get styles for. Should have updated scale and point properties.
	* @param styleObject Extra styles that will be used in the transition.
	* @return Transforms for transitions, left/top for animate.
	*/
	getStylesForTransition(item, styleObject) {
		const styles = { ...styleObject };
		if (this.options.useTransforms) styles.transform = `translate(${this.options.isRTL ? "-" : ""}${this.options.roundTransforms ? Math.round(item.point.x) : item.point.x}px, ${this.options.roundTransforms ? Math.round(item.point.y) : item.point.y}px) scale(${item.scale})`;
		else {
			if (this.options.isRTL) styles.right = `${item.point.x}px`;
			else styles.left = `${item.point.x}px`;
			styles.top = `${item.point.y}px`;
		}
		return styles;
	}
	/**
	* Listen for the transition end on an element and execute the itemCallback
	* when it finishes.
	* @param element Element to listen on.
	* @param itemCallback Callback for the item.
	* @param done Callback to notify `parallel` that this one is done.
	*/
	#whenTransitionDone(element, itemCallback, done) {
		this.#transitionManager.waitForTransition(element, (evt) => {
			itemCallback();
			done(null, evt);
		});
	}
	/**
	* Return a function which will set CSS styles and call the `done` function
	* when (if) the transition finishes.
	* @param opts Transition object.
	*/
	#getTransitionFunction(opts) {
		return (done) => {
			opts.item.applyCss(opts.styles);
			this.#whenTransitionDone(opts.item.element, opts.callback, done);
		};
	}
	/**
	* Execute the styles gathered in the style queue. This applies styles to elements,
	* triggering transitions.
	*/
	#processQueue() {
		if (this.isTransitioning) this.#cancelMovement();
		const hasSpeed = typeof this.options.speed === "number" && this.options.speed > 0;
		const hasQueue = this.#queue.length > 0;
		if (hasQueue && hasSpeed && this.isInitialized) this.#startTransitions(this.#queue);
		else if (hasQueue) {
			styleImmediately(this.#queue);
			this.emit(Shuffle.EventType.LAYOUT, {
				type: Shuffle.EventType.LAYOUT,
				shuffle: this
			});
		} else this.emit(Shuffle.EventType.LAYOUT, {
			type: Shuffle.EventType.LAYOUT,
			shuffle: this
		});
		this.#queue.length = 0;
	}
	/**
	* Wait for each transition to finish, the emit the layout event.
	* Array of transition objects.
	*/
	#startTransitions(transitions) {
		this.isTransitioning = true;
		parallel(transitions.map((obj) => this.#getTransitionFunction(obj)), this.#movementFinished.bind(this));
	}
	#cancelMovement() {
		this.#transitionManager.cancelAll();
		this.isTransitioning = false;
	}
	#movementFinished() {
		this.isTransitioning = false;
		this.emit(Shuffle.EventType.LAYOUT, {
			type: Shuffle.EventType.LAYOUT,
			shuffle: this
		});
	}
	/**
	* The magic. This is what makes the plugin 'shuffle'
	* @param category Category to filter by. Can be a function, string, or array of strings.
	* @param sortOptions A sort object which can sort the visible set
	*/
	filter(category, sortOptions) {
		if (!this.isEnabled) return;
		if (!category || category.length === 0) category = Shuffle.ALL_ITEMS;
		this.#filter(category);
		this.#shrink();
		this.#updateItemCount();
		this.sort(sortOptions);
	}
	/**
	* Gets the visible elements, sorts them, and passes them to layout.
	* @param sortOptions The options object to pass to `sorter`.
	*/
	sort(sortOptions = this.lastSort) {
		if (!this.isEnabled) return;
		this.#resetCols();
		const items = sorter(this.#getFilteredItems(), sortOptions);
		this.sortedItems = items;
		this.#layout(items);
		this.#processQueue();
		this.#setContainerSize();
		this.lastSort = sortOptions;
	}
	/**
	* Reposition everything.
	* @param options options object
	* @param options.recalculateSizes Whether to calculate column, gutter, and container widths again.
	* @param options.force By default, `update` does nothing if the instance is disabled. Setting this
	*    to true forces the update to happen regardless.
	*/
	update({ recalculateSizes = true, force = false } = {}) {
		if (this.isEnabled || force) {
			if (recalculateSizes) this.#setColumns();
			this.sort();
		}
	}
	/**
	* Use this instead of `update()` if you don't need the columns and gutters updated
	* Maybe an image inside `shuffle` loaded (and now has a height), which means calculations
	* could be off.
	*/
	layout() {
		this.update({ recalculateSizes: true });
	}
	/**
	* New items have been appended to shuffle. Mix them in with the current
	* filter or sort status.
	* @param newItems Collection of new items.
	*/
	add(newItems) {
		const items = arrayUnique(newItems).map((el) => new ShuffleItem(el, this.options.isRTL));
		initItems(items);
		this.#resetCols();
		const sortedItems = sorter(this.#mergeNewItems(items), this.lastSort);
		const allSortedItemsSet = this.#filter(this.lastFilter, sortedItems);
		const itemPositions = this.#getNextPositions(allSortedItemsSet.visible);
		for (let i = 0; i < allSortedItemsSet.visible.length; i += 1) {
			const item = allSortedItemsSet.visible[i];
			if (items.includes(item)) {
				item.point = itemPositions[i];
				applyHiddenState(item);
				item.applyCss(this.getStylesForTransition(item, {}));
			}
		}
		for (const item of allSortedItemsSet.hidden) if (items.includes(item)) applyHiddenState(item);
		this.element.offsetWidth;
		this.setItemTransitions(items);
		this.items = this.#mergeNewItems(items);
		this.filter(this.lastFilter);
	}
	/**
	* Disables shuffle from updating dimensions and layout on resize
	*/
	disable() {
		this.isEnabled = false;
	}
	/**
	* Enables shuffle again
	* @param isUpdateLayout if undefined, shuffle will update columns and gutters
	*/
	enable(isUpdateLayout = true) {
		this.isEnabled = true;
		if (isUpdateLayout) this.update();
	}
	/**
	* Remove 1 or more shuffle items.
	* @param elements An array containing one or more elements in shuffle
	*/
	remove(elements) {
		if (elements.length === 0) return;
		const collection = arrayUnique(elements);
		const oldItems = collection.map((element) => this.getItemByElement(element)).filter(Boolean);
		const handleLayout = () => {
			disposeItems(oldItems);
			for (const element of collection) element.remove();
			this.emit(Shuffle.EventType.REMOVED, {
				type: Shuffle.EventType.REMOVED,
				shuffle: this,
				collection
			});
		};
		toggleFilterClasses({
			visible: [],
			hidden: oldItems
		});
		this.#shrink(oldItems);
		this.sort();
		this.items = this.items.filter((item) => !oldItems.includes(item));
		this.#updateItemCount();
		this.once(Shuffle.EventType.LAYOUT, handleLayout);
	}
	/**
	* Retrieve a shuffle item by its element.
	* @param element Element to look for.
	* @return A shuffle item or undefined if it's not found.
	*/
	getItemByElement(element) {
		return this.items.find((item) => item.element === element);
	}
	/**
	* Dump the elements currently stored and reinitialize all child elements which
	* match the `itemSelector`.
	*/
	resetItems() {
		disposeItems(this.items);
		this.isInitialized = false;
		this.items = this.#getItems();
		initItems(this.items);
		this.once(Shuffle.EventType.LAYOUT, () => {
			this.setItemTransitions(this.items);
			this.isInitialized = true;
		});
		this.filter(this.lastFilter);
	}
	/**
	* Destroys shuffle, removes events, styles, and classes
	*/
	destroy() {
		this.#cancelMovement();
		if (this.#resizeObserver) {
			this.#resizeObserver.unobserve(this.element);
			this.#resizeObserver = null;
		}
		this.element.classList.remove("shuffle");
		this.element.removeAttribute("style");
		disposeItems(this.items);
		this.items.length = 0;
		this.sortedItems.length = 0;
		this.sizer = null;
		this.element = null;
		this.isDestroyed = true;
		this.isEnabled = false;
	}
	static getSize = getSize;
	static ShuffleItem = ShuffleItem;
	static ALL_ITEMS = ALL_ITEMS;
	static FILTER_ATTRIBUTE_KEY = FILTER_ATTRIBUTE_KEY;
	static EventType = EventType;
	static Classes = Classes;
	static FilterMode = FilterMode;
	static options = DEFAULT_OPTIONS;
	static Point = Point;
	static Rect = Rect;
};

//#endregion
export { Shuffle as default };
//# sourceMappingURL=shuffle.mjs.map