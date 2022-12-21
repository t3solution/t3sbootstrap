function cmi5Settings(s1, s2, s3, s4, s5, s6, s7, s8, s9, s10, s11) {
  let xmlTextarea = document.createElement('TEXTAREA'),
    pagination = document.querySelectorAll('.pagination'),
    settings = {};
  xmlTextarea.classList.add("parsedxml");
  if (pagination.length > 0) {
    for (let i = 0; i < pagination.length; i++) {
      pagination[i].classList.add("d-none");
    }
  }
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
  settings.cmiDatamodelAuLaunchmethod = s11;
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
  document.querySelector('#page-content main').insertAdjacentHTML('beforeend', '<div class="input-group mb-3"> <span class="input-group-text cmi5-au-title">Titel</span> <input type="text" id="cmi5-au-title" class="form-control" value="Titel" placeholder="Titel" aria-label="Titel" aria-describedby="cmi5-au-title"> </div> <div class="input-group mb-3"> <span class="input-group-text cmi5-au-description">Beschreibung</span> <input type="text" id="cmi5-au-description" class="form-control" value="Beschreibung" placeholder="Beschreibung" aria-label="Beschreibung" aria-describedby="cmi5-au-description"> </div> <div class="row"> <div class="col"> <div class="input-group mb-3"> <span class="input-group-text cmi5-activity-type" id="cmi5-activity-type">Lernaktivität</span> <select class="form-select cmi5-activity-type" aria-label=".form-select-sm example"> <option value="http://adlnet.gov/expapi/activities/module">Lernmodul</option> <option value="http://adlnet.gov/expapi/activities/course">Kurs</option> <option value="http://activitystrea.ms/schema/1.0/task">Aufgabe</option> <option value="http://adlnet.gov/expapi/activities/assessment">Test</option> </select> </div> </div> <div class="col"> <div class="mb-3 input-group"> <span class="input-group-text cmi5-language" id="cmi5-language">Sprache</span> <select class="form-select cmi5-language" aria-label=".form-select-sm example"> <option value="de-DE">Deutsch</option> <option value="en-EN">Englisch</option> <option value="fr-FR">Französich</option> <option value="es-ES">Spanisch</option> </select> </div> </div> </div> <div class="row"> <div class="col"> <div class="mb-3 input-group"> <span class="input-group-text cmi5-moveon" id="cmi5-moveon">MoveOn Kriterium</span> <select class="form-select cmi5-moveon" aria-label=".form-select-sm example"> <option value="Completed">Completed</option> <option value="Passed">Passed</option> <option value="CompletedAndPassed">Completed and Passed</option> <option value="CompletedOrPassed">Completed or Passed</option> <option value="NotApplicable">Not applicable</option> </select> </div> </div> <div class="col"> <div class="mb-3 input-group"> <span class="input-group-text cmi5-mastery-score">Mastery Score</span> <input type="text" id="cmi5-mastery-score" class="text-end form-control" value="75" placeholder="75" aria-label="75" aria-describedby="cmi5-mastery-score"> <span class="input-group-text">%</span> </div> </div> </div> <div class="mb-3 input-group"> <span class="input-group-text cmi5-launch-mode" id="cmi5-launch-mode">Startmodus</span> <select class="form-select cmi5-launch-mode" aria-label=".form-select-sm example"> <option value="OwnWindow" selected>Eigenes Fenster</option> <option value="AnyWindow">Neues Fenster</option> </select> </div> <div class="mb-3 input-group"> <span class="input-group-text cmi5-au-id">ID</span> <input type="text" id="cmi5-au-id" class="form-control" value="http://www.example.com/identifiers/course/AU/xxx-xxx-xxx" placeholder="http://www.example.com/identifiers/course/AU/xxx-xxx-xxx" aria-label="http://www.example.com/identifiers/course/AU/xxx-xxx-xxx" aria-describedby="cmi5-au-id"> </div> <div class="pb-3"></div>');
  document.querySelector('.parsedxml').classList.add("d-none");
  document.querySelector('.parsedxml').innerHTML = cmi5Xml(settings);
  document.querySelector('#page-content main').insertAdjacentHTML('beforeend', '<button class="btn btn-warning btn-lg" id="save-button">Datei speichern</button>');
  document.getElementById("save-button").addEventListener("click", saveFileAs);
}