function cmi5Settings(s1, s2, s3, s4, s5, s6, s7, s8, s9, s10) {
  let xmlTextarea = document.createElement('TEXTAREA'),
    settings = {};
  xmlTextarea.classList.add("parsedxml");
  document.querySelector('#page-content main').insertAdjacentHTML('beforeend', '<textarea class="parsedxml" style="width:100%" rows="10"></textarea>');
  settings.cmiObjpropLang = s1;
  settings.cmiDatamodelAuActtype = s2;
  settings.cmiDatamodelAuTitle = s3;
  settings.cmiDatamodelAuDescr = s4;
  settings.cmiDatamodelAuId = s5;
  settings.cmiDatamodelAuMoveon = s6;
  settings.cmiDatamodelAuMasteryscore = s7;
  settings.cmiDatamodelCourseTitle = s8;
  settings.cmiDatamodelCourseDescr = s9;
  settings.cmiDatamodelCourseId = s10;
  sessionStorage.setItem("cmi5ObjectProperties", JSON.stringify([settings.cmiObjpropLang, settings.cmiDatamodelAuActtype, settings.cmiDatamodelAuTitle, settings.cmiDatamodelAuDescr]));
  if (settings.cmiDatamodelAuActtype == 0) alert("Please select in valid Activity Type in cmi5 Settings!");
  let cop = JSON.parse(sessionStorage.getItem("cmi5ObjectProperties"));
  for (let i = 0; i < Object.keys(settings).length; i++) {
    if (Object.values(settings)[i] == 0 && Object.keys(settings)[i] == "cmiDatamodelAuTitle") settings[Object.keys(settings)[i]] = "### valid AU Title required here ###";
    if (Object.values(settings)[i] == 0 && Object.keys(settings)[i] == "cmiDatamodelAuId") settings[Object.keys(settings)[i]] = "### valid AU ID required here ###";
    if (Object.values(settings)[i] == 0 && Object.keys(settings)[i] == "cmiDatamodelAuMoveon") settings[Object.keys(settings)[i]] = "### valid moveOn Criteria required here ###";
    if (Object.values(settings)[i] == 0 && Object.keys(settings)[i] == "cmiDatamodelCourseTitle") settings[Object.keys(settings)[i]] = "### valid Course Title required here ###";
    if (Object.values(settings)[i] == 0 && Object.keys(settings)[i] == "cmiDatamodelCourseId") settings[Object.keys(settings)[i]] = "### valid Course ID required here ###";
    if (Object.values(settings)[i] == 0 && Object.keys(settings)[i] == "cmiObjpropLang") settings[Object.keys(settings)[i]] = "### valid Language Tag required here ###";
  }
  document.querySelector('.parsedxml').innerHTML = cmi5Xml(settings);
  document.querySelector('#page-content main').insertAdjacentHTML('beforeend', '<button class="btn btn-warning btn-lg" id="save-button">Datei speichern</button>');
  document.getElementById("save-button").onclick = saveFileAs;
}