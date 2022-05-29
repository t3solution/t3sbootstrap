var bookmarkingTracking = function() {
  this.pagesVisited = [];
  this.attemptDuration = 0;
  this.pageDuration = 0;
  this.attemptComplete = false;
  this.completed = false;
  this.pagesTotal = 0;
  this.failed = false;
  this.passed = false;
  this.passedOrFailed = false;
  this.pageTitle = "";
  this.progress = 0;
  this.hls = [];
};

bookmarkingTracking.prototype = {
  initFromBookmark: function(bookmark) {
    this.pagesVisited = bookmark.pagesVisited;
    this.attemptDuration = bookmark.attemptDuration;
    this.attemptComplete = bookmark.attemptComplete;
    this.pagesTotal = bookmark.pagesTotal;
    this.completed = bookmark.completed;
    this.failed = bookmark.failed;
    this.passed = bookmark.passed;
    this.passedOrFailed = bookmark.passedOrFailed;
    this.hls = bookmark.hls;
  },
  getAttemptDuration: function() {
    if (typeof this.attemptDuration === "string") this.attemptDuration = parseInt(this.attemptDuration);
    if (sessionStorage.getItem("pageDuration")) this.attemptDuration += parseInt(sessionStorage.getItem("pageDuration"));
    sessionStorage.setItem("attemptDuration", this.attemptDuration);
    return this.attemptDuration;
  },
  getPageDuration: function() {
    let pd, pds = [];
    if (sessionStorage.getItem("pageDurations")) pds = JSON.parse(sessionStorage.getItem("pageDurations"));
    pds.push(Math.abs((new Date())));
    sessionStorage.setItem("pageDurations", JSON.stringify(pds));
    if (pds.length > 1) pd = pds[pds.length - 1] - pds[pds.length - 2];
    else pd = pds[0] - parseInt(sessionStorage.getItem("startTimeStamp"));
    sessionStorage.setItem("pageDuration", pd);
    return pd;
  },
  // function: go to page bookmarked in LRS when resume course
  goToBookmarkedPage: function() {
    // get bookmarking data on init session ...
    let bookmark = [];
    if (!sessionStorage.getItem("bookMarkInit")) {
      sessionStorage.setItem("startTimeStamp", Math.abs((new Date())));
      // check moveOn..
      let launchedSessions, satisfiedSession, initializedSessions;
      launchedSessions = this.getStatements("launched");
      initializedSessions = this.getStatements("initialized");
      satisfiedSession = this.getStatements("satisfied");
      if (satisfiedSession > 0) sessionStorage.setItem("satisfied", true);
      if (launchedSessions < 2 || initializedSessions < 1) cmi5Controller.sendAllowedState("bookmarkingData", {});
      else bookmark = cmi5Controller.getAllowedState("bookmarkingData");
    } else {
      // ... or get bookmarking data from sessionStorage during session
      if (sessionStorage.getItem("pagesTotal")) bookmark.pagesTotal = parseInt(sessionStorage.getItem("pagesTotal"));
      if (sessionStorage.getItem("completed")) bookmark.completed = sessionStorage.getItem("completed");
      if (sessionStorage.getItem("passed")) bookmark.passed = sessionStorage.getItem("passed");
      if (sessionStorage.getItem("passedOrFailed")) bookmark.passedOrFailed = sessionStorage.getItem("passedOrFailed");
      if (sessionStorage.getItem("failed")) bookmark.failed = sessionStorage.getItem("failed");
      if (sessionStorage.getItem("pagesVisited")) bookmark.pagesVisited = JSON.parse(sessionStorage.getItem("pagesVisited"));
      if (sessionStorage.getItem("attemptDuration")) bookmark.attemptDuration = parseInt(sessionStorage.getItem("attemptDuration"));
      if (sessionStorage.getItem("attemptComplete")) bookmark.attemptComplete = sessionStorage.getItem("attemptComplete");
    }
    // check if completed and set launchmode to "Review"
    console.log("launchMode set to: " + cmi5Controller.launchMode);

    // load highlighted text at relevant pages to sessionStorage
    if (bookmark.hls) textHightlighting("", "", "", bookmark.hls);

    // check if resumed AU
    if (typeof bookmark.pagesVisited !== "undefined") {
      // populate object of bookmarking data
      this.initFromBookmark(bookmark);
      if (!sessionStorage.getItem("bookMarkInit")) resumeDialog();
    } else document.querySelector("body").style.display = "block";
  },
  getStatements: function(verb) {
    let sessions_, sessions = ADL.XAPIWrapper.searchParams(),
      actorAccount = cmi5Controller.agent.account;
    sessions["verb"] = "http://adlnet.gov/expapi/verbs/" + verb;
    sessions["agent"] = '{ "account": { "homePage": "' + actorAccount.homePage + '", "name": "' + actorAccount.name + '" }}';
    sessions["registration"] = cmi5Controller.registration;
    ADL.XAPIWrapper.changeConfig({
      "endpoint": cmi5Controller.endPoint,
      "auth": "Basic " + cmi5Controller.authToken
    });
    sessions_ = ADL.XAPIWrapper.getStatements(sessions);
    if (sessions_.statements.length < 1) {
      sessions["verb"] = "https://w3id.org/xapi/adl/verbs/" + verb;
      sessions_ = ADL.XAPIWrapper.getStatements(sessions);
    }
    return sessions_.statements.length;
  },
  getLaunched: function(verb) {
    let sessions_, sessions = ADL.XAPIWrapper.searchParams(),
      actorAccount = cmi5Controller.agent.account;
    sessions["verb"] = "http://adlnet.gov/expapi/verbs/" + verb;
    sessions["agent"] = '{ "account": { "homePage": "' + actorAccount.homePage + '", "name": "' + actorAccount.name + '" }}';
    sessions["registration"] = cmi5Controller.registration;
    ADL.XAPIWrapper.changeConfig({
      "endpoint": cmi5Controller.endPoint,
      "auth": "Basic " + cmi5Controller.authToken
    });
    sessions_ = ADL.XAPIWrapper.getStatements(sessions);
    if (sessions_.statements.length < 1) {
      sessions["verb"] = "https://w3id.org/xapi/adl/verbs/" + verb;
      sessions_ = ADL.XAPIWrapper.getStatements(sessions);
    }
    return sessions_.statements;
  },
  getStatements_: function(verb) {
    //sessions["id"] = "xyz";
    //sessions["agent"] = '{ "account": { "homePage": "' + actorAccount.homePage + '", "name": "' + actorAccount.name + '" }}';
    //sessions["registration"] = cmi5Controller.registration;
    //const collectSinceDate = new Date("January 1, 2020 00:00:00");
    //const collectBeforeDate = new Date("February 1, 2020 00:00:00");
    //sessions["since"] = collectSinceDate.toISOString();
    //sessions["until"] = collectBeforeDate.toISOString();
    var sessions = ADL.XAPIWrapper.searchParams();
    let sessions_,
      actorAccount = cmi5Controller.agent.account,
      match, progress, duration, selection = [],
      selectionNameVisits = {},
      selectionNameProgress = {},
      selectionNameDuration = {};
    sessions["verb"] = "http://adlnet.gov/expapi/verbs/" + verb;
    sessions["related_activities"] = true;
    sessions["related_agents"] = true;
    sessions["activity"] = cmi5Controller.activityId;
    ADL.XAPIWrapper.changeConfig({
      "endpoint": cmi5Controller.endPoint,
      "auth": "Basic " + cmi5Controller.authToken
    });
    sessions = ADL.XAPIWrapper.getStatements(sessions);

    function getSessions(s) {
      s.more = "/Modules/CmiXapi/xapiproxy.php" + s.more.slice(10);
      let moreStatements = ADL.XAPIWrapper.getStatements("", s.more);
      return moreStatements;
    }

    function getMoreSessions(s) {
      s = getSessions(s);
      for (let i = 0; i < s.statements.length; i++) {
        sessions.statements.push(s.statements[i]);
      }
      return s;
    }
    if (sessions.more && sessions.more !== "") {
      sessions_ = getSessions(sessions);
      for (let i = 0; i < sessions_.statements.length; i++) {
        sessions.statements.push(sessions_.statements[i]);
      }
      do sessions_ = getMoreSessions(sessions_);
      while (sessions_.more && sessions_.more !== "");
    }

    sessions = sessions.statements;
    //console.log(sessions);
    for (let i = 0; i < sessions.length; i++) {
      match = sessions[i].context.extensions["https://w3id.org/xapi/acrossx/activities/page"];
      if (match && match === location.pathname) selection.push(sessions[i]);
    }
    for (let i = 0; i < selection.length; i++) {
      match = selection[i].actor.account.name;
      progress = selection[i].result.extensions["https://w3id.org/xapi/cmi5/result/extensions/progress"];
      duration = parseISO8601Duration(selection[i].result.duration);
      if (selectionNameVisits.hasOwnProperty(match)) {
        selectionNameVisits[match]++;
        selectionNameProgress[match] += progress;
        selectionNameDuration[match] += duration;
      } else {
        selectionNameVisits[match] = 1;
        selectionNameProgress[match] = progress;
        selectionNameDuration[match] = duration;
      }
    }
    let p = Object.keys(selectionNameProgress),
      ret = [],
      user;
    cmi5Controller.st = [];
    cmi5Controller.st["vi"] = [];
    cmi5Controller.st["pr"] = [];
    cmi5Controller.st["du"] = [];
    for (let i = 0; i < p.length; i++) {
      if (selectionNameVisits.hasOwnProperty(p[i])) {
        selectionNameProgress[p[i]] = parseInt(selectionNameProgress[p[i]] / selectionNameVisits[p[i]]);
        selectionNameDuration[p[i]] = selectionNameDuration[p[i]] / 60;
        selectionNameDuration[p[i]] = selectionNameDuration[p[i]].toFixed(1);
        if (p[i] === actorAccount.name) user = "Me and myself";
        else user = "User " + (i + 1);
        cmi5Controller.st.vi.push({
          "name": user,
          "value": selectionNameVisits[p[i]]
        });
        cmi5Controller.st.pr.push({
          "name": user,
          "value": selectionNameProgress[p[i]]
        });
        cmi5Controller.st.du.push({
          "name": user,
          "value": selectionNameDuration[p[i]]
        });
      }
    }
  },
  // function: follow up on resume dialog...
  resume: function() {
    if (this.pagesVisited.length > 0) location.href = this.pagesVisited[1];
  },
  // function: get pathname of current page as bookmark and save to LRS
  saveBookmarkingData: function(notfinish) {
    let bookmark, lp = location.pathname,
      index;
    if (notfinish) {
      index = this.getCurrentPage(this.pagesVisited, lp);
      if (index < 0 && !sessionStorage.getItem("bookMarkInit")) this.pagesVisited.push(lp);
      else {
        // remove pathname of current page if visited before ...
        if (index > -1) this.pagesVisited.splice(index, 1);
        // ... and add pathname of current page to the top of the array of pathnames
        this.pagesVisited.unshift(lp);
      }
      this.getAttemptDuration();
      sessionStorage.setItem("pagesVisited", JSON.stringify(this.pagesVisited));
      if (sessionStorage.getItem("completed")) this.completed = true;
      if (sessionStorage.getItem("failed")) this.failed = true;
      if (sessionStorage.getItem("passed")) this.passed = true;
      if (sessionStorage.getItem("passedOrFailed")) this.passedOrFailed = true;
    }
    // save bookmarking data to LRS
    bookmark = {
      pagesVisited: JSON.parse(sessionStorage.getItem("pagesVisited")),
      attemptDuration: sessionStorage.getItem("attemptDuration"),
      attemptComplete: sessionStorage.getItem("attemptComplete"),
      pagesTotal: sessionStorage.getItem("pagesTotal"),
      completed: sessionStorage.getItem("completed"),
      failed: sessionStorage.getItem("failed"),
      passed: sessionStorage.getItem("passed"),
      passedOrFailed: sessionStorage.getItem("passedOrFailed"),
      hls: textHightlighting("", "", true)
    };
    cmi5Controller.sendAllowedState("bookmarkingData", bookmark);
  },
  getCurrentPage: function(pagesVisited, currentPage, attr) {
    if (attr)
      for (let i = 0; i < pagesVisited.length; i++) {
        if (pagesVisited[i][attr] === currentPage) return i;
      }
    else
      for (let i = 0; i < pagesVisited.length; i++) {
        if (pagesVisited[i] == currentPage) return i;
      }
    return -1;
  },
  checkMoveOn: function(moveOn, finish) {
    let masteryScore = 100;
    if (cmi5Controller.masteryScore) masteryScore = cmi5Controller.masteryScore * 100;
    if (sessionStorage.getItem("pagesTotal")) this.pagesTotal = parseInt(sessionStorage.getItem("pagesTotal"));

    function moveOnPassed() {
      if (sessionStorage.getItem("passed")) {
        sendDefinedStatementWrapper("Passed", parseFloat(sessionStorage.getItem("score")), bm.attemptDuration);
      } else if (sessionStorage.getItem("failed")) {
        sendDefinedStatementWrapper("Failed", parseFloat(sessionStorage.getItem("score")), bm.attemptDuration);
      }
    }

    function moveOnCompleted() {
      if (bm.progress >= masteryScore) {
        // send statement "completed", but only once!
        sendDefinedStatementWrapper("Completed", "", bm.attemptDuration);
        sessionStorage.setItem("satisfied", true);
      }
    }
    if (this.pagesTotal > 0) {
      if (!sessionStorage.getItem("satisfied")) {
        switch (moveOn.toUpperCase()) {
          case "PASSED":
            moveOnPassed();
            break;
          case "COMPLETED":
            moveOnCompleted();
            break;
          case "COMPLETEDANDPASSED":
            moveOnPassed();
            moveOnCompleted();
            break;
          case "COMPLETEDORPASSED":
            moveOnPassed();
            moveOnCompleted();
            break;
        }
      }
      if (this.progress && !finish) sendAllowedStatementWrapper("Progressed", "", this.getPageDuration(), this.progress);
    }
  },
  // function: indicate relevant menu items in t3 menu as visited, set current progress in progressbar
  // Typo3: apply to typo3 menu object?
  markMenuItems: function() {
    let mItemsTotal = document.querySelectorAll(".main-navbarnav a[target=_self]"),
      dItemsTotal = document.querySelectorAll(".main-navbarnav .nav-item > a"),
      progressbar = document.querySelectorAll(".progress-bar"),
      pageId = document.querySelector("body").id,
      mItemsP, dItemsP, mItems = [];
    // get menu items from t3 menu and skip submenu items if applicable
    // indicate relevant menu items in t3 menu as visited and add checkmarks
    if (sessionStorage.getItem("bookMarkInit") && sessionStorage.getItem("startPageId") != pageId) {
      for (let i = 0; i < mItemsTotal.length; i++) {
        mItemsP = mItemsTotal[i];
        // get total of pages
        if (!sessionStorage.getItem("pagesTotal")) mItems.push(mItemsP);
        // highlight menu item of current page and add checkmark
        if (mItemsP.classList.contains("active")) {
          //mItemsTotal[i].classList.add("active"); //, "visited");
          mItemsP.classList.add("visited"); //, "visited");
          this.pageTitle = mItemsP.innerHTML.trim();
          // hide pagination on last page
          if (i < mItemsTotal.length - 1) document.querySelector(".page-pagination").style.display = "block";
        }
        // add checkmarks to menu items of pages visited
        if (!mItemsP.classList.contains("check-mark") && !mItemsP.parentNode.classList.contains("nav-item")) {
          let iNode = document.createElement("i"),
            spanNode = document.createElement("span");
          iNode.classList.add("fas", "fa-check");
          spanNode.classList.add("check-mark");
          mItemsP.insertBefore(spanNode, mItemsP.childNodes[0]);
          mItemsP.firstChild.appendChild(iNode);
          for (let j = 0; j < this.pagesVisited.length; j++) {
            if (mItemsTotal[i].getAttribute("href").indexOf(this.pagesVisited[j]) != -1 && !mItemsP.classList.contains("visited")) {
              mItemsTotal[i].classList.add("visited");
              mItemsP.classList.add("visited");
            }
          }
        }
      }
    }
    let pc1 = '<progress-circle value="" offset="top" pull="-150" part="chart"><slice part="background" size="100%" stroke-width="150" radius="75" stroke="#fff" fill="transparent"><!--No label--></slice><slice part="circle" x="438" y="64" size="',
      pc2 = '%" stroke-width="',
      pc3 = '" radius="75" stroke="#fff"><!--No label--></slice></progress-circle>';
    for (let i = 0; i < dItemsTotal.length; i++) {
      if (!dItemsTotal[i].classList.contains("progress-circle")) {
        if (!dItemsTotal[i].classList.contains("dropdown-toggle")) dItemsTotal[i].insertAdjacentHTML("afterbegin", pc1 + 100 + pc2 + 150 + pc3);
        else {
          let l = 0,
            lt = dItemsTotal[i].nextSibling.childNodes.length;
          for (let j = 0; j < lt; j++) {
            if (dItemsTotal[i].nextSibling.childNodes[j].classList.contains("visited")) l++;
          }
          if (l > 0) dItemsTotal[i].insertAdjacentHTML("afterbegin", pc1 + (l / lt * 100) + pc2 + 150 + pc3);
          else dItemsTotal[i].insertAdjacentHTML("afterbegin", pc1 + (l / lt * 100) + pc2 + 0 + pc3);
        }
        dItemsTotal[i].classList.add("progress-circle");
      }
    }
    if (sessionStorage.getItem("bookMarkInit") && sessionStorage.getItem("startPageId") != pageId && !sessionStorage.getItem("pagesTotal")) sessionStorage.setItem("pagesTotal", mItems.length);
    // set current progress in progressbar
    this.progress = parseInt(this.pagesVisited.length / parseInt(sessionStorage.getItem("pagesTotal")) * 100);
    if (progressbar.length > 0) {
      progressbar[0].style.width = this.progress + "%";
      progressbar[0].innerHTML = this.progress + "%";
    }
  }
};

var bm = new bookmarkingTracking();

document.addEventListener(
  "DOMContentLoaded", () => {

    customizeTemplate();

    if (document.querySelectorAll(".course-login").length > 0) {
      sessionStorage.setItem("courseLoggedIn", 0);
      document.getElementById("main-navbar").classList.add("d-none");
    }
    // get cmi5 parms of location.href
    if (!sessionStorage.getItem("cmi5Parms")) getCmi5Parms();
    // add cmi5 parms to URL if applicable
    else if (location.href.indexOf("endpoint") == -1 && parseInt(sessionStorage.getItem("courseLoggedIn")) > 0) {
      window.history.replaceState(null, null, "?" + sessionStorage.getItem("cmi5Parms"));
    }
    // Parse parameters passed on the command line and set properties of the cmi5 controller.
    if ((sessionStorage.getItem("cmi5No") == "false" && location.href.indexOf("endpoint") != -1)) {
      cmi5Controller.setEndPoint(parse("endpoint"));
      cmi5Controller.setFetchUrl(parse("fetch"));
      cmi5Controller.setRegistration(parse("registration"));
      cmi5Controller.setActivityId(parse("activityid"));
      cmi5Controller.setActor(parse("actor"));
      // Call the cmi5Controller.startUp() method.  Two call back functions are passed:
      // cmi5Ready......This function is called once the controller has fetched the authorization token, read the State document and the agent Profile.
      // startUpError...This function is called if the startUp() method detects an error.
      cmi5Controller.startUp(cmi5Ready, startUpError);
    }
  }
);

function cmi5Ready() {
  // This method was passed to the cmi5Controller.startup() call.

  // Set additional properties for the xAPI object.
  // The cmi5Controller already knows the object ID to use in cmi5-defined statements since it is passed on the launch command.
  // It does not know:
  // 1) The langstring used by the AU.
  // 2) The actitityType
  // 3) The name of the AU
  // 4) The description of the AU
  // pass the 4 values of cmi5ObjectProperties called in template of AU, e.g. "au1.js"
  // Typo3: make constants editable in template of AU?.
  let cop = JSON.parse(sessionStorage.getItem("cmi5ObjectProperties"));
  cmi5Controller.setObjectProperties(cop[0], cop[1], cop[2], cop[3]);
  cmi5Controller.dLang = cop[0];
  cmi5Controller.dTitle = '"' + cop[2] + '"';
  sessionStorage.setItem("endPoint", cmi5Controller.endPoint);
  sessionStorage.setItem("auth", "Basic " + cmi5Controller.authToken);
  // Perform any other setup actions required by the AU here.

  // check if logged in
  if (parseInt(sessionStorage.getItem("courseLoggedIn")) > 0) {
    // Send the initialized statement, but only on cmi5Init (i.e. at the beginning of the session)!
    if (!sessionStorage.getItem("cmi5Init")) {
      // get objectProperties from LMS
      let objectProperties = bm.getLaunched("launched");
      objectProperties = objectProperties[0].context.contextActivities.grouping[0].definition;
      cop[0] = Object.keys(objectProperties.name)[0];
      cop[2] = objectProperties.name[cop[0]];
      cop[3] = objectProperties.description[cop[0]];
      sessionStorage.setItem("courseTitle", cop[2]);
      cmi5Controller.dLang = cop[0];
      cmi5Controller.dTitle = '"' + cop[2] + '"';
      // set objectProperties from LMS
      sessionStorage.setItem("cmi5ObjectProperties", JSON.stringify(cop));
      // send Initialized
      sendDefinedStatementWrapper("Initialized");
      sessionStorage.setItem("cmi5Init", "true");
      sessionStorage.setItem("cmi5No", "false");
    }
    // on init/move to a new page perform bookmarking and highlight visited pages in menu (progress)
    bookmarkingAndProgress();

    if (!sessionStorage.getItem("bookMarkInit")) document.querySelector("body").style.display = "block";

    // track video events if applicable
    if (document.querySelectorAll('video').length > 0) {
      let video = document.querySelectorAll('video'),
        context = cmi5Controller.getContextExtensions();
      for (let i = 0; i < video.length; i++) {
        trackVideoEvents(video[i], context);
      }
    }
  }
  // on launch of AU, log in as frontend user
  else feLogIn();
}

// function: log in as frontend user
function feLogIn() {
  // hide anything during log in
  sessionStorage.setItem("courseLoggedIn", 1);
  let formData = document.querySelectorAll(".course-login form")[0],
    inp = formData.querySelectorAll("input"),
    butn = formData.querySelectorAll("fieldset button"),
    coursePw = "devuser3j8d03mx7"; // + document.querySelectorAll(".auth")[0].innerHTML.trim(), //location.pathname + document.querySelectorAll(".auth")[0].innerHTML.trim(),
  courseId = "devuser"; //location.pathname;
  formData.setAttribute("autocomplete", "off");
  if (coursePw.length > 100) coursePw = coursePw.substring(0, 100);
  if (courseId.length > 100) courseId = courseId.substring(0, 100);
  for (let i = 0; i < inp.length; i++) {
    inp[i].type = "hidden"
    if (inp[i].name == "user") inp[i].value = courseId;
    if (inp[i].name == "pass") inp[i].value = coursePw;
    if (inp[i].name == "submit") inp[i].click();
  }
  if (butn.length > 0) butn[0].click();
}

function scroll(ev) {
  console.log(ev);
  let st = Math.max(document.documentElement.scrollTop, document.body.scrollTop);
  if (!st) {
    if (scrollUp < 1) scrollUp++;
    else document.querySelector(".prev-page .page-link").click();
  } else if ((st + document.documentElement.clientHeight) >= document.documentElement.scrollHeight) {
    if (scrollDown < 1) scrollDown++;
    else document.querySelector(".next-page .page-link").click();
  }
}

// function: add "exit course" button to header, style jumbotron image, style "next" button etc
// Typo3: layout, design, location, function of exit button?
var navbar = false,
  scrollDown = scrollUp = 0;

function customizeTemplate() {
  document.querySelector("html").setAttribute("lang", "de");
  let b1 = '<div class="btn styled rules-au-button"><i class="fas fa-exclamation"></i></div>',
    b2 = '<div class="btn styled notes-au-button"><i class="fas fa-pen"></i></div>',
    b3 = '<div class="btn-close btn btn-close-white styled exit-au-button"></div>',
    navbarContainer = document.querySelectorAll('#main-navbar .container'),
    offcanvasHeader = document.querySelectorAll('.offcanvas-header'),
    offcanvasBody = document.querySelectorAll('.offcanvas-body'),
    pageId = document.querySelector("body").id,
    pageItems = document.querySelectorAll('.pagination .page-item');
  if (navbarContainer.length > 0) {
    navbar = true;
    navbarContainer[0].insertAdjacentHTML("beforeend", b1);
    navbarContainer[0].insertAdjacentHTML("beforeend", b2);
    navbarContainer[0].insertAdjacentHTML("beforeend", b3);
  }
  //  if (sessionStorage.getItem("cmi5No")) document.querySelector(".page-pagination").style.display = "block";

  if (pageItems.length > 0) {
    if (pageItems.length > 1) {
      pageItems[0].style.display = "none"; //remove();
      pageItems[0].classList.add("prev-page");
      pageItems[1].classList.add("next-page");
    } else pageItems[0].classList.add("next-page");
    let pageItemsA = document.querySelectorAll('.pagination .next-page.page-item a'),
      pageItemsArrow = document.querySelectorAll('.pagination .next-page.page-item a span');
    pageItemsA[0].innerHTML = "Weiter";
    pageItemsA[0].appendChild(pageItemsArrow[0]);
    let varArrowN = document.querySelectorAll('.pagination .next-page.page-item a span i');
    varArrowN[0].className = "";
    varArrowN[0].classList.add("fas", "fa-chevron-down");
    pageItemsA[0].classList.add("text-center", "text-grid");
  }
  if (document.querySelector("footer.start-page")) {
    if (!sessionStorage.getItem("startPageId")) sessionStorage.setItem("startPageId", pageId);
    if (pageItems.length > 0) pageItems[0].style.display = "none";
    if (document.querySelector("#main-navbar")) {
      document.querySelector("#main-navbar").style.display = "none";
      document.querySelector("#page-footer").classList.remove("py-4");
    }
    if (document.querySelectorAll(".start-button")) {
      document.querySelector(".start-button").addEventListener("click", function() {
        document.querySelector(".next-page .page-link").click();
      });
    }

    let jumbotronImage = document.querySelectorAll('.jumbotron.background-image');
    if (jumbotronImage.length > 0) {
      jumbotronImage = jumbotronImage[0];
      let style = jumbotronImage.currentStyle || window.getComputedStyle(jumbotronImage, false),
        bi = style.backgroundImage;
      bi = "linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), " + bi;
      jumbotronImage.insertAdjacentHTML("beforebegin", '<style> #' + jumbotronImage.id + '.jumbotron {background-image: ' + bi + '!important}</style>');
      sessionStorage.setItem("jumbotron", bi);
    }
  } else if (offcanvasBody.length > 0) {
    offcanvasBody[0].classList.add("fs-4", "fw-light");
    offcanvasBody[0].insertAdjacentHTML("afterbegin", '<div class="progress"><div class="progress-bar" role="progressbar" style="width: 25%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">25%</div></div>');
    offcanvasHeader[0].insertAdjacentHTML("afterbegin", '<div class="module-title fs-4 fw-light" style="background-image:"></div>');
  }

  setTimeout(function() {
    let menuImage = document.querySelectorAll('.offcanvas-header .module-title'),
      exitAuButton = document.querySelectorAll('.modal .exit-au-button'),
      resumeButton = document.querySelectorAll('.modal .resume-button'),
      rulesAuButton = document.querySelectorAll('.navbar .rules-au-button'),
      notesAuButton = document.querySelector('.navbar .notes-au-button'),
      navbarExitAuButton = document.querySelectorAll('.navbar .exit-au-button'),
      summaryExitAuButton = document.querySelectorAll('#page-content .exit-au-button'),
      modalNotes = document.querySelectorAll(".container button.modal-notes"),
      modalRules = document.querySelectorAll(".container button.modal-rules"),
      closeButton = document.querySelectorAll('.modal .close-button'),
      modalCloseButton = document.querySelectorAll("button.btn-close"),
      pageContent = document.getElementById("page-content"),
      jumbotron = document.querySelector(".jumbotron"),
      summary = document.querySelector(".summary-highlights");
    //iframe = document.querySelector('iframe.h5p-iframe');
    //iframe.contentDocument.querySelector("video").muted = true;
    //iframe.contentDocument.querySelector("video").play();
    //iframe.contentDocument.querySelector("video").unmuted = true;
    //if (parseInt(sessionStorage.getItem("courseLoggedIn")) > 0 && document.querySelectorAll('.video iframe').length > 0) {
    let vimeoOrYt = document.querySelectorAll('.video iframe'),
      localVideo = document.querySelectorAll('.video video'),
      context = cmi5Controller.getContextExtensions(),
      player = [];

    if (vimeoOrYt.length > 0) {
      for (let i = 0; i < vimeoOrYt.length; i++) {
        trackVideoEvents(vimeoOrYt[i], context, "vimeoOrYtObserver" + i);
      }
    }
    if (localVideo.length > 0) {
      for (let i = 0; i < localVideo.length; i++) {
        player[i] = new Plyr(localVideo[i]);
        trackVideoEvents(localVideo[i], context);
      }
    }
    if (navbarContainer.length > 0 && !jumbotron) textHightlighting(pageContent, notesAuButton);

    if (summary) summaryHighlights();

    if (menuImage.length > 0) {
      menuImage[0].style.backgroundImage = sessionStorage.getItem("jumbotron");
      menuImage[0].innerHTML = sessionStorage.getItem("courseTitle");
    }
    //      if (modalNotes.length == 0 && notesAuButton.length > 0) notesAuButton[0].style.color = "rgba(255,255,255,0.5)";
    if (modalNotes.length == 0 && notesAuButton) notesAuButton.style.color = "rgba(255,255,255,0.5)";
    if (modalRules.length == 0 && rulesAuButton.length > 0) rulesAuButton[0].style.color = "rgba(255,255,255,0.5)";
    if (closeButton.length > 0) {
      for (let i = 0; i < closeButton.length; i++) {
        for (let j = 0; j < modalCloseButton.length; j++) {
          modalCloseButton[j].classList.add("btn-close-white");
        }
        closeButton[i].addEventListener("click", function() {
          for (let j = 0; j < modalCloseButton.length; j++) {
            modalCloseButton[j].click();
          }
        });
      }
    }
    if (exitAuButton.length > 0) exitAuButton[0].addEventListener("click", function() {
      exitAU();
    });
    if (resumeButton.length > 0) resumeButton[0].addEventListener("click", function() {
      bm.resume();
    });
    if (rulesAuButton.length > 0) rulesAuButton[0].addEventListener("click", function() {
      modalRulesDialog();
    });
    /*if (notesAuButton.length > 0) notesAuButton[0].addEventListener("click", function() {
      modalNotesDialog();
    });*/
    if (navbarExitAuButton.length > 0) navbarExitAuButton[0].addEventListener("click", function() {
      exitAUDialog();
    });
    if (summaryExitAuButton.length > 0) summaryExitAuButton[0].addEventListener("click", function() {
      exitAUDialog();
    });
  }, 100);
}

function textHightlighting(pageContent, notesAuButton, createObject, readObject) {
  var lp = location.pathname;
  if (readObject) {
    // read object from LRS via State API and re-store sessionStorage
    for (let i = 0; i < readObject.length; i++) {
      sessionStorage.setItem(Object.keys(readObject[i])[0], Object.values(readObject[i])[0]);
    }
  } else if (createObject) {
    // set highlighted text at relevant pages and prepare object suitable for storage in LRS via State API
    var hls = [];
    for (let i = 0; i < sessionStorage.length; i++) {
      if (sessionStorage.key(i) === "hl___" + lp) cmi5Controller.hltr.deserializeHighlights(sessionStorage.getItem(sessionStorage.key(i)));
      if (sessionStorage.key(i).indexOf("hl___") != -1) {
        hls.push({
          [sessionStorage.key(i)]: sessionStorage.getItem(sessionStorage.key(i))
        });
      }
    }
    return hls;
  } else {
    // set EventListener to highlight text and to delete highlights
    setTimeout(function() {
      let iframe = document.querySelector('iframe.h5p-iframe');
      if (iframe) cmi5Controller.hltrIframe = new TextHighlighter(iframe.contentDocument.body);
    }, 500);
    cmi5Controller.hltr = new TextHighlighter(pageContent);
    pageContent.addEventListener('click', function() {
      if (!lp.indexOf("lerngruppe") != -1) sessionStorage.setItem("hl___" + lp, cmi5Controller.hltr.serializeHighlights());
    });
    /*iframe.contentDocument.body.addEventListener('click', function() {
      if (!lp.indexOf("lerngruppe") != -1) sessionStorage.setItem("hli___" + lp, cmi5Controller.hltrIframe.serializeHighlights());
    });*/
    notesAuButton.addEventListener('click', function() {
      cmi5Controller.hltr.removeHighlights();
      cmi5Controller.hltrIframe.removeHighlights();
      sessionStorage.removeItem("hl___" + lp);
      sessionStorage.removeItem("hli___" + lp);
    });
  }
}

function summaryHighlights() {
  function rem(s) {
    return s.replace(/<\/?SPAN[^>]*>/gi, "").replace(/<\/?i[^>]*>/gi, "").replace(/<\/?svg[^>]*>/gi, "").replace(/<\/?style[^>]*>/gi, "").replace(/<\/?path[^>]*>/gi, "");
  }
  setTimeout(function() {
    var lpx, lp = location.pathname,
      hldiv = "",
      mtitle = document.querySelectorAll('.navbar-nav.main-navbarnav .dropdown-item');
    for (let i = 0; i < sessionStorage.length; i++) {
      if (sessionStorage.key(i).indexOf("hl___") != -1 && sessionStorage.key(i).indexOf("lerngruppe") == -1 && sessionStorage.key(i).indexOf("zusammenfassung") == -1) {
        lpx = sessionStorage.key(i).substring(5);
        for (let j = 0; j < mtitle.length; j++) {
          if (mtitle[j].href.indexOf(lpx) != -1) {
            hldiv += "<div><a href='" + lpx + "'>" + rem(mtitle[j].innerHTML).trim() + "</a></div>";
          }
        }
      }
    }
    document.querySelector(".summary-highlights p").insertAdjacentHTML("afterend", hldiv);
  }, 500);
}

function exitAUDialog() {
  document.querySelector(".btn.exit-dialog").click();
}

function resumeDialog() {
  document.querySelector("body").style.display = "block";
  sendAllowedStatementWrapper("Resumed");
  document.querySelector(".btn.resume-dialog").click();
}

function modalNotesDialog() {
  let modalNotes = document.querySelectorAll(".container button.modal-notes");
  if (modalNotes.length > 0) modalNotes[0].click();
  else alert("Keine Notizen hier ...");
}

function modalRulesDialog() {
  let modalRules = document.querySelectorAll(".container button.modal-rules");
  if (modalRules.length > 0) modalRules[0].click();
  else alert("Keine Merksätze hier ...");
}

// function: get cmi5 parms of location.href
function getCmi5Parms() {
  if (location.href.indexOf("endpoint") != -1) {
    let cmi5Parms = [];
    cmi5Parms = location.href.split("?");
    if (location.href.indexOf("&cHash") != -1) cmi5Parms = cmi5Parms[1].split("&cHash");
    sessionStorage.setItem("cmi5Parms", cmi5Parms[1]);
    sessionStorage.setItem("cmi5No", "false");
  } else sessionStorage.setItem("cmi5No", "true");
}

// function: on init/move to a new page perform bookmarking and highlight visited pages in menu (progress)
function bookmarkingAndProgress() {
  // get title of AU and set at start page
  if (document.querySelector(".jumbotron-content .text-light")) document.querySelector(".jumbotron-content .text-light").innerHTML = sessionStorage.getItem("courseTitle");

  // go to page bookmarked in LRS when resume course
  bm.goToBookmarkedPage();

  // indicate relevant menu items in t3 menu as visited
  // Typo3: apply to typo3 menu object?
  bm.markMenuItems();

  // get pathname of current page as bookmark, send statement "completed" if applicable
  setTimeout(function() {
    bm.saveBookmarkingData(true);
  }, 200);

  // send statement "completed" if number of visited pages is greater than cmi5 mastery score (for example "0.8")
  // Typo3: there may be other conditions for completion, like score achieved in assessment etc - how to make editable in template of AU?
  // else send Progressed statement on current page visited
  bm.checkMoveOn(cmi5Controller.moveOn);
  // set bookMarkInit session flag
  if (!sessionStorage.getItem("bookMarkInit")) sessionStorage.setItem("bookMarkInit", "true");
}

function startUpError() {
  // This is called if there is an error in the cmi5 controller startUp method.
  alert("An error was detected in the cmi5Controller.startUp() method.  Please check the console log for any errors.");
}

function sendAllowedStatementWrapper(verbName, score, duration, progress, highlighted) {
  let verbUpper = verbName.toUpperCase(),
    cExtentions = cmi5Controller.getContextExtensions(),
    verb;
  switch (verbUpper) {
    case "EXPERIENCED":
      verb = ADL.verbs.experienced;
      break;
    case "PROGRESSED":
      verb = ADL.verbs.progressed;
      break;
    case "RESUMED":
      verb = ADL.verbs.resumed;
      break;
    case "SUSPENDED":
      verb = ADL.verbs.suspended;
      break;
    case "HIGHLIGHTED":
      verb = ADL.verbs.highlighted;
      break;
  }
  if (cmi5Controller.launchMode.toUpperCase() !== "NORMAL") {
    // Only initialized and terminated are allowed per section 10.0 of the spec.
    console.log("When launchMode is " + cmi5Controller.launchMode + "only Initialized and Terminated verbs are allowed");
    return false;
  }

  if (verb) {
    // Context extensions were read from the State document's context template
    let stmt, dur = convertMillisecondsToMinSec(duration),
      stmtObject = JSON.parse(sessionStorage.getItem("stmtObject"));
    // Get basic cmi5 allowed statement object
    stmtObject.id += "_" + location.hostname + location.pathname;
    stmt = cmi5Controller.getcmi5AllowedStatement(verb, stmtObject, cmi5Controller.getContextActivities(), cExtentions);
    stmt.object.definition.name = {
      [cmi5Controller.dLang]: cmi5Controller.dTitle + ' at page ' + '"' + bm.pageTitle + '", duration: ' + dur
    };

    if (verbUpper === "RESUMED") stmt.object.definition.name = {
      [cmi5Controller.dLang]: cmi5Controller.dTitle
    };
    if (verbUpper === "SUSPENDED") stmt.object.definition.name = {
      [cmi5Controller.dLang]: cmi5Controller.dTitle + ' at page ' + '"' + bm.pageTitle + '"'
    };
    if (verbUpper === "PROGRESSED") stmt.object.definition.name = {
      [cmi5Controller.dLang]: cmi5Controller.dTitle + ' at page ' + '"' + bm.pageTitle + '"' + ', progress: ' + progress + '%'
    };
    if (verbUpper === "HIGHLIGHTED") stmt.object.definition.name = {
      [cmi5Controller.dLang]: cmi5Controller.dTitle + ' at page ' + '"' + bm.pageTitle + '"' + ', Text highlighted ...'
    };
    /*stmt.context.contextActivities["other"] = [];
    stmt.context.contextActivities.other.push({
      "objectType": "Activity",
      "id": "https://www.example.com/activities/other"
    });*/
    // Add UTC timestamp, required by cmi5 spec.
    stmt.timestamp = (new Date()).toISOString();
    if (duration || progress) {
      stmt.result = {};
      if (typeof(progress) === "number") stmt.result.extensions = {
        "https://w3id.org/xapi/cmi5/result/extensions/progress": progress
      };
      if (duration) stmt.result.duration = convertMillisecondsToISO8601Duration(duration);
    }
    let cx = {
      "https://w3id.org/xapi/acrossx/activities/page": location.pathname
    };
    if (highlighted) {
      cx = {
        "https://w3id.org/xapi/acrossx/activities/page": location.pathname,
        "http://risc-inc.com/annotator/activities/highlight": highlighted
      };
    }
    /*if (sessionStorage.getItem("serialized")) {
      cx = {
        "https://w3id.org/xapi/acrossx/activities/page": location.pathname,
        "https://w3id.org/xapi/acrossx/activities/markings": sessionStorage.getItem("serialized")
      };
      //sessionStorage.removeItem("serialized");
    }*/
    Object.assign(cExtentions, cx);
    stmt.context.extensions = cExtentions;
    cmi5Controller.sendStatement(stmt);
  } else console.log("Invalid verb passed: " + verbName);
  return false;
}

function sendDefinedStatementWrapper(verbName, score, duration, progress) {
  // What verb is to be sent?
  let verbUpper = verbName.toUpperCase(),
    verb;
  switch (verbUpper) {
    case "INITIALIZED":
      verb = ADL.verbs.initialized;
      break;
    case "COMPLETED":
      verb = ADL.verbs.completed;
      break;
    case "PASSED":
      verb = ADL.verbs.passed;
      break;
    case "FAILED":
      verb = ADL.verbs.failed;
      break;
    case "TERMINATED":
      verb = ADL.verbs.terminated;
      break;
  }

  if (cmi5Controller.launchMode.toUpperCase() !== "NORMAL") {
    // Only initialized and terminated are allowed per section 10.0 of the spec.
    if (verbUpper !== "TERMINATED" && verbUpper !== "INITIALIZED") {
      console.log("When launchMode is " + cmi5Controller.launchMode + "only Initialized and Terminated verbs are allowed");
      return false;
    }
  }

  if (verb) {
    // Context extensions were read from the State document's context template
    let cExtentions = cmi5Controller.getContextExtensions(),
      success, complete = null,
      stmt, dur = convertMillisecondsToMinSec(duration);
    if (verbUpper === "PASSED" || verbUpper === "FAILED") {
      // Passed and Failed statements require the masteryScore as an context extension
      if (cmi5Controller.masteryScore && !cExtentions["https://w3id.org/xapi/cmi5/context/extensions/masteryscore"]) cExtentions["https://w3id.org/xapi/cmi5/context/extensions/masteryscore"] = cmi5Controller.masteryScore;

      // Per section 9.5.2 of the cmi5 spec
      success = verbUpper === "PASSED";
    }

    // Automatically set complete based on cmi5 rules (9.5.3)
    if (verbUpper === "COMPLETED") complete = true;

    // Get basic cmi5 defined statement object

    stmt = cmi5Controller.getcmi5DefinedStatement(verb, cExtentions);
    if (!sessionStorage.getItem("stmtObject")) sessionStorage.setItem("stmtObject", JSON.stringify(stmt.object));
    if (verbUpper === "INITIALIZED") stmt.object.definition.name = {
      [cmi5Controller.dLang]: cmi5Controller.dTitle
    };
    else stmt.object.definition.name = {
      [cmi5Controller.dLang]: cmi5Controller.dTitle + ' at page ' + '"' + bm.pageTitle + '"'
    };

    if (verbUpper === "TERMINATED") stmt.object.definition.name = {
      [cmi5Controller.dLang]: cmi5Controller.dTitle + ' at page ' + '"' + bm.pageTitle + '", session duration: ' + dur
    };
    if (verbUpper === "COMPLETED") stmt.object.definition.name = {
      [cmi5Controller.dLang]: cmi5Controller.dTitle + ' at page ' + '"' + bm.pageTitle + '", attempt duration: ' + dur
    };

    // Add UTC timestamp, required by cmi5 spec.
    stmt.timestamp = (new Date()).toISOString();

    // Do we need a result object?
    if (success || complete || score || duration || progress) {
      stmt.result = {};
      if (typeof(complete) === "boolean") stmt.result.completion = complete;
      if (typeof(success) === "boolean") stmt.result.success = success;
      if (typeof(score) === "number") stmt.result.score = {
        "scaled": score
      };
      if (duration) stmt.result.duration = convertMillisecondsToISO8601Duration(duration);
      // Statements that include success or complete must include a moveon activity in the context
      if (success || complete || verbName === "Failed") stmt.context.contextActivities.category.push({
        "id": "https://w3id.org/xapi/cmi5/context/categories/moveon"
      });
    }
    /*let cx = {
      "https://w3id.org/xapi/video/extensions/length": location.pathname
    };
    Object.assign(cExtentions, cx);
    stmt.context.extensions = cExtentions;*/
    cmi5Controller.sendStatement(stmt);
  } else console.log("Invalid verb passed: " + verbName);
  return false;
}

function exitAU() {
  finishAU();
  //close();
}

function finishAU() {
  let sd = Math.abs((new Date())) - parseInt(sessionStorage.getItem("startTimeStamp"));
  if (cmi5Controller.launchMode.toUpperCase() === "NORMAL") sendAllowedStatementWrapper("Suspended", "", sd);
  bm.checkMoveOn(cmi5Controller.moveOn, true);
  bm.saveBookmarkingData(false);
  sendDefinedStatementWrapper("Terminated", "", sd);
  sessionStorage.clear();
  cmi5Controller.goLMS();
}

function parse(val) {
  // Utility function to parse command line parameters
  let result = result_ = "Not found",
    tmp = [];
  val = val.toUpperCase();
  location.search.substr(1).split("&").forEach(function(item) {
    tmp = item.split("=");
    if (tmp[0].toUpperCase() === val) result = decodeURIComponent(tmp[1]);
  });
  return result;
}

function convertMillisecondsToISO8601Duration(inputMilliseconds) {
  let hours, minutes, seconds, i_inputCentiseconds,
    i_inputMilliseconds = parseInt(inputMilliseconds, 10),
    inputIsNegative = "",
    rtnStr = "";
  //round to nearest 0.01 seconds
  i_inputCentiseconds = Math.round(i_inputMilliseconds / 10);
  if (i_inputCentiseconds < 0) {
    inputIsNegative = "-";
    i_inputCentiseconds = i_inputCentiseconds * -1;
  }
  hours = parseInt(((i_inputCentiseconds) / 360000), 10);
  minutes = parseInt((((i_inputCentiseconds) % 360000) / 6000), 10);
  seconds = (((i_inputCentiseconds) % 360000) % 6000) / 100;
  rtnStr = inputIsNegative + "PT";
  if (hours > 0) rtnStr += hours + "H";
  if (minutes > 0) rtnStr += minutes + "M";
  return rtnStr += seconds + "S";
}

function convertMillisecondsToMinSec(millisec) {
  let seconds = (millisec / 1000).toFixed(0),
    minutes = Math.floor(seconds / 60),
    hours = "";
  if (minutes > 59) {
    hours = Math.floor(minutes / 60);
    hours = (hours >= 10) ? hours : "0" + hours;
    minutes = minutes - (hours * 60);
    minutes = (minutes >= 10) ? minutes : "0" + minutes;
  }
  seconds = Math.floor(seconds % 60);
  seconds = (seconds >= 10) ? seconds : "0" + seconds;
  if (hours != "") return hours + ":" + minutes + ":" + seconds;
  else return minutes + ":" + seconds;
}

function parseISO8601Duration(iso8601Duration) {
  let iso8601DurationRegex = /(-)?P(?:([.,\d]+)Y)?(?:([.,\d]+)M)?(?:([.,\d]+)W)?(?:([.,\d]+)D)?T(?:([.,\d]+)H)?(?:([.,\d]+)M)?(?:([.,\d]+)S)?/;
  let matches = iso8601Duration.match(iso8601DurationRegex);
  if (matches[1] === undefined) matches[1] = '+';
  else matches[1] = '-';
  if (matches[2] === undefined) matches[2] = 0;
  if (matches[3] === undefined) matches[3] = 0;
  if (matches[4] === undefined) matches[4] = 0;
  if (matches[5] === undefined) matches[5] = 0;
  if (matches[6] === undefined) matches[6] = 0;
  if (matches[7] === undefined) matches[7] = 0;
  if (matches[8] === undefined) matches[8] = 0;
  let duration = parseInt(matches[4]) * 604800 + parseInt(matches[5]) * 86400 + parseInt(matches[6]) * 2400 + parseInt(matches[7]) * 60 + parseInt(matches[8]);
  return duration;
}

function trackVideoEvents(videoObj, cExtentions, observer) {
  var result, observer;
  if (observer) {
    observer = new window.IntersectionObserver(([entry]) => {
      if (entry.isIntersecting) {
        window.addEventListener('message', onMessageReceived, false);
        console.log("added :" + videoObj.src);
        return
      }
      window.removeEventListener("message", onMessageReceived, false);
      console.log("removed :" + videoObj.src);
    }, {
      root: null,
      threshold: 1.0, // set offset 0.1 means trigger if atleast 10% of element in viewport
    });
  }

  function vSections(visitedSec, prevTimeSec, curTimeSec, duration) {
    visitedSec.push({
      "t1": prevTimeSec,
      "t2": curTimeSec
    });
    visitedSec.sort(function(a, b) {
      return a.t1 - b.t1;
    });
    var rest = 0.0,
      done = 0.0,
      notVisited = [],
      visited_ = [];
    for (let i = 0; i < visitedSec.length; i++) {
      if (i < visitedSec.length - 1) {
        if (parseFloat(visitedSec[i].t2) < parseFloat(visitedSec[i + 1].t1)) {
          rest += parseFloat(visitedSec[i + 1].t1) - parseFloat(visitedSec[i].t2);
          notVisited.push({
            "t1": visitedSec[i].t2,
            "t2": visitedSec[i + 1].t1
          });
        }
      } else {
        rest += duration - parseFloat(visitedSec[i].t2);
        notVisited.push({
          "t1": visitedSec[i].t2,
          "t2": duration
        });
      }
    }
    done = (duration - rest).toFixed(2);
    rest = rest.toFixed(2);
    for (let i = 0; i < notVisited.length; i++) {
      if (i > 0) {
        visited_.push({
          "t1": notVisited[i - 1].t2,
          "t2": notVisited[i].t1
        });
      } else {
        visited_.push({
          "t1": "0.0",
          "t2": notVisited[i].t1
        });
      }
    }
    /*console.log(visitedSec);
    console.log(notVisited);
    console.log(visited_);
    console.log(rest);
    console.log(done);*/
    return visited_;
  }

  function sendPaused(curTime, total, duration, progress) {
    console.log("paused");
    let result = {
      "extensions": {
        "https://w3id.org/xapi/video/extensions/time": curTime,
        "https://w3id.org/xapi/video/extensions/progress": ((total / duration).toFixed(2)),
        "https://w3id.org/xapi/video/extensions/played-segments": progress.substr(0, progress.length - 2)
      }
    };
    console.log(result);
    sendVideoStatement("paused", this, result, cExtentions);
  }

  function sendPlayed(curTime) {
    console.log("played");
    let result = {
      "extensions": {
        "https://w3id.org/xapi/video/extensions/time": curTime
      }
    };
    console.log(result);
    sendVideoStatement("played", this, result, cExtentions);
  }

  function sendFinished(startVideo) {
    console.log("finished");
    let result = {
      "score": {
        "scaled": 1,
        "min": 0,
        "max": 100,
        "raw": 100
      },
      "success": true,
      "completion": true,
      "response": "",
      "duration": ISO8601_time(startVideo)
    };
    console.log(result);
    sendVideoStatement("ended", this, result, cExtentions);
  }

  function sendSeeked(prevTime, curTime) {
    console.log("seeked");
    let result = {
      "extensions": {
        "https://w3id.org/xapi/video/extensions/time-from": prevTime,
        "https://w3id.org/xapi/video/extensions/time-to": curTime
      }
    };
    console.log(result);
    sendVideoStatement("seeked", this, result, cExtentions);
    return [prevTime, curTime];
  }

  if (videoObj.src && videoObj.src.indexOf("youtube") != -1) {
    if (videoObj.src.indexOf("-nocookie") != -1) {
      //videoObj.src = videoObj.src.replace("-nocookie", "");
      //videoObj.src = videoObj.src.replace("&origin=https%3A%2F%2Fcms2.cmifive.io", "");
    }
    var ytProgress = "",
      ytTotal = 0.0,
      ytStartVideo = "",
      ytPrevTime = 0.0,
      ytCurTime = 0.0,
      ytCurTime_ = 0.0,
      ytDuration = 0.0,
      ytPS = [],
      ytPreSeek = [0, 0, 0, 0, 0],
      ytSeekTemp = {},
      ytPrevTimePrev = [],
      ytVisited = [],
      message = function() {
        return JSON.stringify({
          event: 'listening',
          func: 'info',
          args: []
        });
      };
    videoObj.contentWindow.postMessage(message(), '*');
    window.addEventListener('message', onMessageReceived, false);
    setTimeout(function() {
      observer.observe(videoObj);
    }, 900);

    function onMessageReceived(event) {
      //console.log(event);
      let data = JSON.parse(event.data);
      if ("info" in data) {
        //console.log(data.info);
        if (data.info) {
          if ("currentTime" in data.info) onSeek(data.info);
          if ("playerState" in data.info) {
            switch (data.info.playerState) {
              case 2:
                onPause(data.info);
                break;
              case 0:
                onFinish(data.info);
                break;
              case 1:
                onPlay(data.info);
                break;
            }
          }
        }
      }
    }

    function onPause(data) {
      if (ytDuration == 0.0) ytDuration = parseFloat(data.duration).toFixed(2);
      ytCurTime_ = ytCurTime;
      if (ytPS.length > 1 && ytPS[0] == "played") ytCurTime_ = ytSeekTemp[0];
      if (ytPrevTime == ytCurTime_) ytPrevTime = ytPrevTimePrev[0];
      ytProgress += ytPrevTime + ' - ' + ytCurTime_ + ', ';
      ytTotal += ytCurTime_ - ytPrevTime;
      sendPaused(ytCurTime_, ytTotal, ytDuration, ytProgress);
      ytVisited = vSections(ytVisited, ytPrevTime, ytCurTime_, ytDuration);
      ytPS = [];
    }

    function onPlay(data) {
      ytPS.push("played");
      ytPrevTime = ytPreSeek[3];
      ytPrevTimePrev.push(ytPrevTime);
      if (ytPrevTimePrev.length > 2) ytPrevTimePrev.shift();
      if (ytStartVideo !== null) {
        let DateTime = new Date();
        ytStartVideo = DateTime.getTime();
      }
      sendPlayed(ytCurTime);
    }

    function onSeek(data) {
      ytPreSeek.push(parseFloat(data.currentTime).toFixed(2));
      if (ytPreSeek.length > 5) ytPreSeek.shift();
      //console.log(ytPreSeek);
      ytCurTime = ytPreSeek[4];
      if (Math.abs(ytPreSeek[4] - ytPreSeek[3]) > 1) {
        ytPrevTime = ytPreSeek[3];
        ytSeekTemp = sendSeeked(ytPrevTime, ytCurTime);
        ytPS.push("seeked");
      }
    }

    function onFinish(data) {
      sendFinished(ytStartVideo);
    }
    return;
  }
  if (videoObj.src && videoObj.src.indexOf("vimeo") != -1) {
    var viProgress = "",
      viTotal = 0.0,
      viDuration = 0.0,
      viSeeked = false,
      viVisited = [],
      viStartVideo = "",
      viPrevTime = 0.0,
      viPreSeek = [0, 0, 0, 0, 0],
      viPreSeekPrev = 0.0,
      viCurTime = 0.0,
      viCurTime_ = 0.0;
    // Listen for messages from the player
    window.addEventListener('message', onMessageReceived, false);

    setTimeout(function() {
      observer.observe(videoObj);
    }, 900);
    // Handle messages received from the player
    function onMessageReceived(event) {
      //console.log(event);
      var data = JSON.parse(event.data);
      switch (data.event) {
        case 'ready':
          onReady();
          break;
        case 'pause':
          onPause(data.data);
          break;
        case 'finish':
          onFinish(data.data);
          break;
        case 'seek':
          onSeek(data.data);
          break;
        case 'play':
          onPlay(data.data);
          break;
        case 'playProgress':
          onPlayProgress(data.data);
          break;
      }
    }
    // Helper function for sending a message to the player
    function post(action, value) {
      let data = {
        method: action
      };

      if (value) data.value = value;

      videoObj.contentWindow.postMessage(JSON.stringify(data), "*");
    }

    function onReady() {
      post('addEventListener', 'pause');
      post('addEventListener', 'finish');
      post('addEventListener', 'seek');
      post('addEventListener', 'play');
      post('addEventListener', 'playProgress');
    }

    function onPlayProgress(data) {
      viPreSeek.push(parseFloat(data.seconds).toFixed(2));
      if (viPreSeek.length > 5) viPreSeek.shift();
      viCurTime = viPreSeek[4];
    }

    function onPause(data) {
      if (data) viDuration = parseFloat(data.duration).toFixed(2);
      if (viSeeked) {
        viCurTime_ = viPrevTime;
        viPrevTime = viPreSeekPrev;
      } else if (viCurTime > viPrevTime) {
        viCurTime_ = viCurTime;
        viProgress += viPrevTime + ' - ' + viCurTime_ + ', ';
        viTotal += viCurTime_ - viPrevTime;
        sendPaused(viCurTime_, viTotal, viDuration, viProgress);
      }
      viVisited = vSections(viVisited, viPrevTime, viCurTime_, viDuration);
      viSeeked = false;
    }

    function onPlay(data) {
      viPrevTime = viCurTime;
      if (viStartVideo !== null) {
        let DateTime = new Date();
        viStartVideo = DateTime.getTime();
      }
      sendPlayed(viCurTime);
    }

    function onSeek(data) {
      viSeeked = true;
      viPreSeekPrev = viPrevTime;
      viPrevTime = viPreSeek[2];
      sendSeeked(viPrevTime, viCurTime);
      post('pause');
      onPause(data);
    }

    function onFinish(data) {
      sendFinished(viStartVideo);
    }
    return;
  }
  if (videoObj.poster) {
    var progress = "",
      startVideo = "",
      prevTime = 0.0,
      preSeek = [0, 0, 0, 0, 0],
      preSeekPrev = 0.0,
      curTime = 0.0,
      curTime_ = 0.0,
      total = 0.0,
      duration = 0.0,
      visited = [],
      seeked = false;

    videoObj.ontimeupdate = function() {
      preSeek.push(parseFloat(this.currentTime).toFixed(2));
      if (preSeek.length > 5) preSeek.shift();
      curTime = preSeek[4];
    }
    videoObj.onseeked = function() {
      seeked = true;
      preSeekPrev = prevTime;
      prevTime = preSeek[2];
      sendSeeked(prevTime, curTime);
      videoObj.pause();
      videoObj.onpause();
    }
    videoObj.onpause = function() {
      duration = parseFloat(this.duration).toFixed(2);
      if (seeked) {
        curTime_ = prevTime;
        prevTime = preSeekPrev;
      } else {
        curTime_ = curTime;
        progress += prevTime + ' - ' + curTime_ + ', ';
        total += curTime_ - prevTime;
        sendPaused(curTime_, total, duration, progress);
      }
      visited = vSections(visited, prevTime, curTime_, duration);
      seeked = false;
    };
    videoObj.onplay = function() {
      prevTime = curTime;
      if (startVideo !== null) {
        let DateTime = new Date();
        startVideo = DateTime.getTime();
      }
      sendPlayed(curTime);
    };
    videoObj.onended = function() {
      sendFinished(startVideo);
    };
  }

  function ISO8601_time(start) {
    let currentTime = new Date();
    return "PT" + Math.round((currentTime.getTime() / 1000) - (start / 1000)) + "S";
  }
}

function sendVideoStatement(verbName, videoObj, result, cExtentions) {
  // What verb is to be sent?
  let verbUpper = verbName.toUpperCase(),
    verb, videoSrc = document.querySelectorAll('video source')[0].getAttribute("src"),
    videoSrcPath = videoSrc;
  videoSrc = videoSrc.substring(videoSrc.lastIndexOf('/') + 1);
  switch (verbUpper) {
    case "SEEKED":
      verb = ADL.verbs.seeked;
      break;
    case "PAUSED":
      verb = ADL.verbs.paused;
      break;
    case "PLAYED":
      verb = ADL.verbs.played;
      break;
    case "INTERACTED":
      verb = ADL.verbs.initeracted;
      break;
    case "TERMINATED":
      verb = ADL.verbs.terminated;
      break;
    case "EXPERIENCED":
      verb = ADL.verbs.experienced;
      break;
    case "INITIALIZED":
      verb = ADL.verbs.initialized;
      break;
    case "ENDED":
      verb = ADL.verbs.ended;
      break;
  }
  if (cmi5Controller.launchMode.toUpperCase() !== "NORMAL") {
    // Only initialized and terminated are allowed per section 10.0 of the spec.
    console.log("When launchMode is " + cmi5Controller.launchMode + "only Initialized and Terminated verbs are allowed");
    return false;
  }
  if (verb) {
    // Context extensions were read from the State document's context template
    let stmt, cx, vObj = [],
      stmtObject = JSON.parse(sessionStorage.getItem("stmtObject"));
    // Get basic cmi5 defined statement object
    stmtObject.id += "_" + location.hostname + location.pathname;
    vObj = {
      "id": stmtObject.id + "_" + videoSrcPath,
      "objectType": "Activity",
      "definition": {
        "type": "https://w3id.org/xapi/video/activity-type/video",
        "name": {
          "en-US": videoSrc
        }
      }
    };
    cx = {
      //"https://w3id.org/xapi/video/extensions/completion-threshold": "1.0",
      "https://w3id.org/xapi/video/extensions/length": videoObj.duration,
      "https://w3id.org/xapi/video/extensions/full-screen": videoObj.fullscreenchange,
      "https://w3id.org/xapi/video/extensions/screen-size": window.innerWidth + " x " + window.innerHeight,
      "https://w3id.org/xapi/video/extensions/video-playback-size": videoObj.videoWidth + " x " + videoObj.videoHeight,
      //"https://w3id.org/xapi/video/extensions/cc-enabled": videoObj.texttrack,
      //"https://w3id.org/xapi/video/extensions/speed": "1x",
      //"https://w3id.org/xapi/video/extensions/frame-rate": "23.98",
      "https://w3id.org/xapi/video/extensions/quality": videoObj.videoWidth + " x " + videoObj.videoHeight,
      "https://w3id.org/xapi/video/extensions/user-agent": navigator.userAgent,
      "https://w3id.org/xapi/video/extensions/volume": videoObj.volume,
      "https://w3id.org/xapi/acrossx/activities/page": location.pathname
    };
    stmt = cmi5Controller.getcmi5AllowedStatement(verb, vObj, cmi5Controller.getContextActivities(), cx);
    Object.assign(cExtentions, cx);
    stmt.context.extensions = cExtentions;
    stmt.object.definition.name = {
      [cmi5Controller.dLang]: cmi5Controller.dTitle + ': "' + videoSrc + '"' + ' at page ' + '"' + bm.pageTitle + '"'
    };
    stmt.result = {} = result;
    // Add UTC timestamp, required by cmi5 spec.
    stmt.timestamp = (new Date()).toISOString();
    cmi5Controller.sendStatement(stmt);
  } else console.log("Invalid verb passed: " + verbName);
  return false;
}

// function to handle H5P generated statements and generate cmi5 allowed statements
var H5Pinit = true,
  H5Ptitle, handleH5P = function(event) {
    if (cmi5Controller.launchMode.toUpperCase() !== "NORMAL") {
      // Only initialized and terminated are allowed per section 10.0 of the spec.
      console.log("When launchMode is " + cmi5Controller.launchMode + "only Initialized and Terminated verbs are allowed");
      return false;
    }
    // get H5P statement
    let H5PXapiStmt = event.data.statement,
      page, title, stmt, cid = H5PIntegration.contents[Object.keys(H5PIntegration.contents)[0]].mainId,
      stmtObject = JSON.parse(sessionStorage.getItem("stmtObject"));
    if (cmi5Controller.getContextExtensions()) {
      // add cmi5 activity ID
      stmtObject.id += "/" + location.hostname + location.pathname + "/h5pcid_" + cid + H5PXapiStmt.object.id;
      H5PXapiStmt.object.id = stmtObject.id;
      if (H5Pinit) {
        H5Ptitle = window[0].window.H5P.instances[0].libraryInfo.machineName;
        H5Pinit = false;
      }
      // add cmi5 description: "name of content type" at "name of page"
      H5PXapiStmt.object.definition.name = {
        [cmi5Controller.dLang]: cmi5Controller.dTitle + ': "' + H5Ptitle + ': ' + cid + '"' + ' at page ' + '"' + bm.pageTitle + '"'
      };
      // if (H5PXapiStmt.object.definition.name) delete H5PXapiStmt.object.definition.name;
      // create cmi5 allowed statement

      stmt = cmi5Controller.getcmi5AllowedStatement(H5PXapiStmt.verb, H5PXapiStmt.object, cmi5Controller.getContextActivities(), cmi5Controller.getContextExtensions());
      // add result to statement if applicable
      if (H5PXapiStmt.result) {
        stmt.result = H5PXapiStmt.result;
        if (H5PXapiStmt.result.completion && cmi5Controller.masteryScore && (!sessionStorage.getItem("passed") && !sessionStorage.getItem("failed"))) {
          sessionStorage.setItem("score", H5PXapiStmt.result.score.scaled);
          if (parseFloat(H5PXapiStmt.result.score.scaled) >= parseFloat(cmi5Controller.masteryScore)) sessionStorage.setItem("passed", true);
          else sessionStorage.setItem("failed", true);
          //sendDefinedStatementWrapper("Passed", "", this.attemptDuration);
        }
      }
      // send cmi5 statement
      //console.log(stmt);
      cmi5Controller.sendStatement(stmt);
    }
  };

document.onreadystatechange = function() {
  if (sessionStorage.getItem("cmi5Init") || sessionStorage.getItem("cmi5No") == "true") document.querySelector("body").style.display = "block";
  //else if (location.href.indexOf("simUser") == -1) document.querySelector("body").style.display = "none";
  else document.querySelector("body").style.display = "none";

  if ('complete' === document.readyState && typeof H5P !== 'undefined' && H5P.externalDispatcher && cmi5Controller && sessionStorage.getItem("cmi5No") == "false") H5P.externalDispatcher.on('xAPI', handleH5P);
};