function saveFileAs() {
  if (promptFilename = prompt("Datei speichern", "cmi5.xml")) {
    var textBlob = new Blob([document.querySelector(".parsedxml").value], {
      type: 'text/plain'
    });
    var downloadLink = document.createElement("a");
    downloadLink.download = promptFilename;
    downloadLink.innerHTML = "Download File";
    downloadLink.href = window.URL.createObjectURL(textBlob);
    downloadLink.click();
    delete downloadLink;
    delete textBlob;
  }
}

function cmi5Xml(settings) {
  let xw = new XMLWriter('UTF-8');
  xw.formatting = 'indented';
  xw.indentChar = ' ';
  xw.indentation = 2;
  xw.writeStartDocument();
  xw.writeStartElement('courseStructure');
  xw.writeAttributeString('xmlns', 'https://w3id.org/xapi/profiles/cmi5/v1/CourseStructure.xsd');
  xw.writeStartElement('course');
  xw.writeAttributeString('id', settings.cmiDatamodelCourseId);
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
  xw.writeAttributeString('id', settings.cmiDatamodelAuId);
  xw.writeAttributeString('moveOn', settings.cmiDatamodelAuMoveon);
  xw.writeAttributeString('masteryScore', settings.cmiDatamodelAuMasteryscore);
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