var mediumHighlighter;
document.addEventListener(
  "DOMContentLoaded", () => {
    mediumHighlighter = document.createElement("medium-highlighter");
    document.body.appendChild(mediumHighlighter);
  }
);

function setMarkerPosition(markerPosition) {
  console.log("markerPosition");
  console.log(markerPosition);
  return mediumHighlighter.setAttribute(
    "markerposition",
    JSON.stringify(markerPosition));
}

function getSelectedText() {
  return window.getSelection().toString();
}
document.addEventListener("click", () => {
  if (getSelectedText().length > 0) {
    console.log("getSelectedText()");
    console.log(getSelectedText());
    setMarkerPosition(getMarkerPosition());
  }
});

document.addEventListener("selectionchange", () => {
  if (getSelectedText().length === 0) {
    setMarkerPosition({
      display: "none"
    });
  }
});

function getMarkerPosition() {
  const rangeBounds = window
    .getSelection()
    .getRangeAt(0)
    .getBoundingClientRect();
  return {
    // Substract width of marker button -> 40px / 2 = 20
    left: rangeBounds.left + rangeBounds.width / 2 - 20,
    top: rangeBounds.top - 30,
    display: "flex",
  };
}