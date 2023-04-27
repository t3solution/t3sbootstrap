function saveFileAs() {
  let zipName = document.querySelector(".page-title h1").innerHTML.trim();
  zipName = deUmlaut(zipName.substring(0, zipName.indexOf(" (Lerngruppe")));
  if (promptFilename = prompt("Datei speichern", zipName + "_cmi5.zip")) {
    let zip = new JSZip();
    zip.file("cmi5.xml", document.querySelector(".parsedxml").value);
    zip.generateAsync({
      type: "blob"
    }).then(function(blob) {
      saveAs(blob, promptFilename);
    });
  }
}

function cmi5Xml(settings) {
  let xw = new XMLWriter('UTF-8'),
    pageTitle = document.querySelector(".page-title h1").innerHTML.trim(),
    at = document.querySelectorAll(".form-select.cmi5-activity-type option"),
    lang = document.querySelectorAll(".form-select.cmi5-language option"),
    mo = document.querySelectorAll(".form-select.cmi5-moveon option"),
    lm = document.querySelectorAll(".form-select.cmi5-launch-mode option");
  pageTitle = pageTitle.substring(0, pageTitle.indexOf(" (Lerngruppe"));
  settings.cmiDatamodelCourseTitle = pageTitle;
  if (settings.cmiDatamodelCourseDescr === "0") settings.cmiDatamodelCourseDescr = pageTitle;
  settings.cmiDatamodelAuTitle = pageTitle;
  if (settings.cmiDatamodelAuDescr === "0") settings.cmiDatamodelAuDescr = pageTitle;
  document.querySelector("#cmi5-au-title").value = pageTitle;
  document.querySelector("#cmi5-au-description").value = settings.cmiDatamodelCourseDescr;
  document.querySelector("#cmi5-au-id").value = settings.cmiDatamodelAuId.substring(0, settings.cmiDatamodelAuId.indexOf("xxx")) + deUmlaut(pageTitle);
  document.querySelector("#cmi5-mastery-score").value = settings.cmiDatamodelAuMasteryscore * 100;
  for (let i = 0; i < lang.length; i++) {
    if (lang[i].value === settings.cmiObjpropLang) lang[i].setAttribute("selected", "");
  }
  for (let i = 0; i < at.length; i++) {
    if (at[i].value === settings.cmiDatamodelAuActtype) at[i].setAttribute("selected", "");
  }
  for (let i = 0; i < mo.length; i++) {
    if (mo[i].value === settings.cmiDatamodelAuMoveon) mo[i].setAttribute("selected", "");
  }
  for (let i = 0; i < lm.length; i++) {
    if (lm[i].value === settings.cmiDatamodelAuLaunchmethod) lm[i].setAttribute("selected", "");
  }
  document.querySelector('.parsedxml').value = generateXml(pageTitle);
  document.querySelector(".form-select.cmi5-language").addEventListener("change", () => {
    settings.cmiObjpropLang = document.querySelector(".form-select.cmi5-language").value;
    document.querySelector('.parsedxml').value = generateXml(pageTitle);
  });
  document.querySelector(".form-select.cmi5-activity-type").addEventListener("change", () => {
    settings.cmiDatamodelAuActtype = document.querySelector(".form-select.cmi5-activity-type").value;
    document.querySelector('.parsedxml').value = generateXml(pageTitle);
  });
  document.querySelector(".form-select.cmi5-moveon").addEventListener("change", () => {
    settings.cmiDatamodelAuMoveon = document.querySelector(".form-select.cmi5-moveon").value;
    document.querySelector('.parsedxml').value = generateXml(pageTitle);
  });
  document.querySelector(".form-select.cmi5-launch-mode").addEventListener("change", () => {
    settings.cmiDatamodelAuLaunchmethod = document.querySelector(".form-select.cmi5-launch-mode").value;
    document.querySelector('.parsedxml').value = generateXml(pageTitle);
  });
  document.querySelector("#cmi5-au-title").addEventListener("change", () => {
    settings.cmiDatamodelAuTitle = document.querySelector("#cmi5-au-title").value;
    settings.cmiDatamodelCourseTitle = settings.cmiDatamodelAuTitle;
    document.querySelector('.parsedxml').value = generateXml(pageTitle);
  });
  document.querySelector("#cmi5-au-description").addEventListener("change", () => {
    settings.cmiDatamodelAuDescr = document.querySelector("#cmi5-au-description").value;
    settings.cmiDatamodelCourseDescr = settings.cmiDatamodelAuDescr;
    document.querySelector('.parsedxml').value = generateXml(pageTitle);
  });
  document.querySelector("#cmi5-mastery-score").addEventListener("change", () => {
    settings.cmiDatamodelAuMasteryscore = document.querySelector("#cmi5-mastery-score").value / 100;
    document.querySelector('.parsedxml').value = generateXml(pageTitle);
  });

  function generateXml(pageTitle) {
    xw.formatting = 'indented';
    xw.indentChar = ' ';
    xw.indentation = 2;
    xw.writeStartDocument();
    xw.writeStartElement('courseStructure');
    xw.writeAttributeString('xmlns', 'https://w3id.org/xapi/profiles/cmi5/v1/CourseStructure.xsd');
    xw.writeStartElement('course');
    xw.writeAttributeString('id', settings.cmiDatamodelCourseId.substring(0, settings.cmiDatamodelCourseId.indexOf("xxx")) + deUmlaut(pageTitle));
    xw.writeStartElement('title');
    xw.writeStartElement('langstring');
    xw.writeString(settings.cmiDatamodelCourseTitle);
    xw.writeAttributeString('lang', settings.cmiObjpropLang);
    xw.writeEndElement();
    xw.writeEndElement();
    xw.writeStartElement('description');
    xw.writeStartElement('langstring');
    xw.writeString(settings.cmiDatamodelCourseDescr);
    xw.writeAttributeString('lang', settings.cmiObjpropLang);
    xw.writeEndElement();
    xw.writeEndElement();
    xw.writeEndElement();
    xw.writeStartElement('au');
    xw.writeAttributeString('id', settings.cmiDatamodelAuId.substring(0, settings.cmiDatamodelAuId.indexOf("xxx")) + deUmlaut(pageTitle));
    xw.writeAttributeString('moveOn', settings.cmiDatamodelAuMoveon);
    xw.writeAttributeString('masteryScore', settings.cmiDatamodelAuMasteryscore);
    xw.writeAttributeString('launchMethod', settings.cmiDatamodelAuLaunchmethod);
    xw.writeAttributeString('activityType', settings.cmiDatamodelAuActtype);
    xw.writeStartElement('title');
    xw.writeStartElement('langstring');
    xw.writeString(settings.cmiDatamodelAuTitle);
    xw.writeAttributeString('lang', settings.cmiObjpropLang);
    xw.writeEndElement();
    xw.writeEndElement();
    xw.writeStartElement('description');
    xw.writeStartElement('langstring');
    xw.writeString(settings.cmiDatamodelAuDescr);
    xw.writeAttributeString('lang', settings.cmiObjpropLang);
    xw.writeEndElement();
    xw.writeEndElement();
    xw.writeElementString('url', window.location.href);
    xw.writeEndElement();
    xw.writeEndElement();
    xw.writeEndElement();
    xw.writeEndDocument();
    return xw.flush();
  }
}

function deUmlaut(value) {
  value = value.toLowerCase();
  value = value.replace(/ä/g, 'ae');
  value = value.replace(/ö/g, 'oe');
  value = value.replace(/ü/g, 'ue');
  value = value.replace(/ß/g, 'ss');
  value = value.replace(/ /g, '-');
  value = value.replace(/\./g, '');
  value = value.replace(/,/g, '');
  value = value.replace(/\(/g, '');
  value = value.replace(/\)/g, '');
  return value;
}

function saveAs(blob, filename) {
  if (typeof navigator.msSaveOrOpenBlob !== 'undefined') {
    return navigator.msSaveOrOpenBlob(blob, fileName);
  } else if (typeof navigator.msSaveBlob !== 'undefined') {
    return navigator.msSaveBlob(blob, fileName);
  } else {
    let elem = window.document.createElement('a');
    elem.href = window.URL.createObjectURL(blob);
    elem.download = filename;
    elem.style = 'display:none;opacity:0;color:transparent;';
    (document.body || document.documentElement).appendChild(elem);
    if (typeof elem.click === 'function') {
      elem.click();
    } else {
      elem.target = '_blank';
      elem.dispatchEvent(new MouseEvent('click', {
        view: window,
        bubbles: true,
        cancelable: true
      }));
    }
    URL.revokeObjectURL(elem.href);
  }
}