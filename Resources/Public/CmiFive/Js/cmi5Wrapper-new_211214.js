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
      let launchedSessions, satisfiedSession;
      launchedSessions = this.getStatements("launched");
      satisfiedSession = this.getStatements("satisfied");
      if (satisfiedSession > 0) sessionStorage.setItem("satisfied", true);
      if (launchedSessions < 2) cmi5Controller.sendAllowedState("bookmarkingData", {});
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
  // function: follow up on resume dialog...
  resume: function() {
    if (this.pagesVisited.length > 0) location.href = this.pagesVisited[1];
  },
  // function: get pathname of current page as bookmark and save to LRS
  saveBookmarkingData: function() {
    let bookmark, lp = location.pathname,
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
    // save bookmarking data to LRS
    bookmark = {
      pagesVisited: this.pagesVisited,
      attemptDuration: this.attemptDuration,
      attemptComplete: this.attemptComplete,
      pagesTotal: this.pagesTotal,
      completed: this.completed,
      failed: this.failed,
      passed: this.passed,
      passedOrFailed: this.passedOrFailed
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
      mItems = [],
      mItemsP, dItemsP;
    // get menu items from t3 menu and skip submenu items if applicable
    // indicate relevant menu items in t3 menu as visited and add checkmarks
    for (let i = 0; i < mItemsTotal.length; i++) {
      mItemsP = mItemsTotal[i];
      // get total of pages
      if (sessionStorage.getItem("bookMarkInit")) mItems.push(mItemsP);
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
    if (sessionStorage.getItem("bookMarkInit")) sessionStorage.setItem("pagesTotal", mItems.length);
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

    if (document.querySelectorAll(".course-login").length > 0) sessionStorage.setItem("courseLoggedIn", 0);
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
      sendDefinedStatementWrapper("Initialized");
      sessionStorage.setItem("cmi5Init", "true");
      sessionStorage.setItem("cmi5No", "false");
    }
    // on init/move to a new page perform bookmarking and highlight visited pages in menu (progress)
    bookmarkingAndProgress();

    if (!sessionStorage.getItem("bookMarkInit")) document.querySelector("body").style.display = "block";

    // track video events if applicable
    if (document.querySelectorAll('video').length > 0) trackVideoEvents(document.querySelectorAll('video'), cmi5Controller.getContextExtensions());
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
    coursePw = location.pathname + "3j8d03mx7",
    courseId = location.pathname;
  formData.setAttribute("autocomplete", "off");
  if (coursePw.length > 100) coursePw = coursePw.substring(0, 100);
  if (courseId.length > 100) courseId = courseId.substring(0, 100);
  for (let i = 0; i < inp.length; i++) {
    if (inp[i].name == "user") {
      inp[i].setAttribute("autocomplete", "off");
      inp[i].setAttribute("readonly", "");
      inp[i].value = courseId;
    }
    if (inp[i].name == "pass") {
      inp[i].setAttribute("autocomplete", "off");
      inp[i].setAttribute("readonly", "");
      inp[i].value = coursePw;
    }
    if (inp[i].name == "submit") inp[i].click();
  }
  /*
  let formDataS = $.param(formData.serializeArray());
  $.ajax({
    type: 'POST',
    url: formData.action,
    data: formDataS,
    success: function(data) {
      console.log(data.href);
      window.location.href = data.href;
    }
  });*/
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

function iframeLazy(iframeObj) {
  for (let i = 0; i < iframeObj.length; i++) {
    iframeObj[i].classList.add("lozad");
    iframeObj[i].setAttribute("loading", "lazy");
  }
}

function videoLazy(videoObj) {
  for (let i = 0; i < videoObj.length; i++) {
    videoObj[i].preload = "none";
    let vsrc = videoObj[i].firstChild.src;
    videoObj[i].firstChild.removeAttribute("src");
    videoObj[i].firstChild.setAttribute("data-src", vsrc);
    videoObj[i].setAttribute("loading", "lazy");
    videoObj[i].setAttribute("playsinline", "");
    videoObj[i].firstChild.setAttribute("loading", "lazy");
    videoObj[i].classList.add("lozad");
  }
}

// function: add "exit course" button to header, style jumbotron image, style "next" button etc
// Typo3: layout, design, location, function of exit button?
var navbar = false,
  scrollDown = scrollUp = 0;

function customizeTemplate() {
  let b1 = '<div class="btn styled rules-au-button"><i class="fas fa-exclamation"></i></div>',
    b2 = '<div class="btn styled notes-au-button"><i class="fas fa-pen"></i></div>',
    b3 = '<div class="btn-close btn btn-close-white styled exit-au-button"></div>',
    navbarContainer = document.querySelectorAll('#main-navbar .container'),
    offcanvasHeader = document.querySelectorAll('.offcanvas-header'),
    offcanvasBody = document.querySelectorAll('.offcanvas-body'),
    auTitle;
  if (navbarContainer.length > 0) {
    navbar = true;
    navbarContainer[0].insertAdjacentHTML("beforeend", b1);
    navbarContainer[0].insertAdjacentHTML("beforeend", b2);
    navbarContainer[0].insertAdjacentHTML("beforeend", b3);
  }

  if (sessionStorage.getItem("cmi5No")) document.querySelector(".page-pagination").style.display = "block";
  if (document.querySelectorAll('video').length > 0) var player = new Plyr(document.querySelector('audio, video'));
  let pag = document.querySelectorAll('.pagination .page-item');
  if (pag.length > 0) {
    if (pag.length > 1) {
      pag[0].style.display = "none"; //remove();
      pag[0].classList.add("prev-page");
      pag[1].classList.add("next-page");
    } else pag[0].classList.add("next-page");
    let pagA = document.querySelectorAll('.pagination .next-page.page-item a'),
      pagArrow = document.querySelectorAll('.pagination .next-page.page-item a span');
    pagA[0].innerHTML = "Weiter";
    pagA[0].appendChild(pagArrow[0]);
    let varArrowN = document.querySelectorAll('.pagination .next-page.page-item a span i');
    varArrowN[0].className = "";
    varArrowN[0].classList.add("fas", "fa-chevron-down");
    pagA[0].classList.add("text-center", "text-grid");
  }
  let jumbotronImage = document.querySelectorAll('.jumbotron.background-image');
  if (jumbotronImage.length > 0) {
    if (pag.length > 0) pag[0].style.display = "none";
    document.querySelector("#main-navbar").style.display = "none";
    document.querySelector("#page-footer").classList.remove("py-4");
    if (document.querySelectorAll(".start-button").length > 0) {
      document.querySelector(".start-button").addEventListener("click", function() {
        document.querySelector(".next-page .page-link").click();
      });
    }
    let els = document.querySelectorAll('.jumbotron .container *');
    for (let i = 0; i < els.length; i++) {
      for (let j = 0, el = els[i]; j < el.childNodes.length; j++) {
        let currChild = el.childNodes[j];
        if (currChild.length > 0) auTitle = currChild.nodeValue;
      }
    }
    jumbotronImage = jumbotronImage[0];
    let style = jumbotronImage.currentStyle || window.getComputedStyle(jumbotronImage, false),
      bi = style.backgroundImage;
    bi = "linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), " + bi;
    jumbotronImage.insertAdjacentHTML("beforebegin", '<style> #' + jumbotronImage.id + '.jumbotron {background-image: ' + bi + '!important}</style>');
    sessionStorage.setItem("jumbotron", bi);
  } else if (offcanvasBody.length > 0) {
    offcanvasBody[0].classList.add("fs-4", "fw-light");
    offcanvasBody[0].insertAdjacentHTML("afterbegin", '<div class="progress"><div class="progress-bar" role="progressbar" style="width: 25%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">25%</div></div>');
    offcanvasHeader[0].insertAdjacentHTML("afterbegin", '<div class="module-title fs-4 fw-light" style="background-image:"></div>');
  }

  if (document.querySelectorAll(".course-description").length > 0) {
    sessionStorage.setItem("courseTitle", auTitle);
    let cmi5ObjectProperties = [],
      ObjectProperties = document.querySelectorAll(".course-description div");
    for (let i = 0; i < ObjectProperties.length; i++) {
      cmi5ObjectProperties.push(ObjectProperties[i].innerHTML);
    }
    sessionStorage.setItem("cmi5ObjectProperties", JSON.stringify(cmi5ObjectProperties));
  }
  setTimeout(function() {
      let menuImage = document.querySelectorAll('.offcanvas-header .module-title'),
        exitAuButton = document.querySelectorAll('.modal .exit-au-button'),
        resumeButton = document.querySelectorAll('.modal .resume-button'),
        rulesAuButton = document.querySelectorAll('.navbar .rules-au-button'),
        notesAuButton = document.querySelectorAll('.navbar .notes-au-button'),
        startThemeButton = document.querySelectorAll('.btn.start-theme-button'),
        navbarExitAuButton = document.querySelectorAll('.navbar .exit-au-button, .btn.exit-au-button'),
        modalNotes = document.querySelectorAll(".container button.modal-notes"),
        modalRules = document.querySelectorAll(".container button.modal-rules"),
        closeButton = document.querySelectorAll('.modal .close-button'),
        modalCloseButton = document.querySelectorAll("button.btn-close");
      if (menuImage.length > 0) {
        menuImage[0].style.backgroundImage = sessionStorage.getItem("jumbotron");
        menuImage[0].innerHTML = sessionStorage.getItem("courseTitle");
      }
      if (modalNotes.length == 0 && notesAuButton.length > 0) notesAuButton[0].style.color = "rgba(255,255,255,0.5)";
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
      if (notesAuButton.length > 0) notesAuButton[0].addEventListener("click", function() {
        modalNotesDialog();
      });
      if (startThemeButton.length > 0) startThemeButton[0].addEventListener("click", function() {
        startThemeDialog();
      });
      if (navbarExitAuButton.length > 0) {
        for (let i = 0; i < navbarExitAuButton.length; i++) {
          navbarExitAuButton[i].addEventListener("click", function() {
            exitAUDialog();
          });
        }
      }
    },
    100);
}

function exitAUDialog() {
  document.querySelector(".btn.exit-dialog").click();
}

function startThemeDialog() {
  document.querySelector(".btn.start-theme-dialog").click();
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
  // go to page bookmarked in LRS when resume course
  bm.goToBookmarkedPage();

  // indicate relevant menu items in t3 menu as visited
  // Typo3: apply to typo3 menu object?
  bm.markMenuItems();

  // get pathname of current page as bookmark, send statement "completed" if applicable
  bm.saveBookmarkingData();

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

function sendAllowedStatementWrapper(verbName, score, duration, progress) {
  let verbUpper = verbName.toUpperCase(),
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
  }
  if (cmi5Controller.launchMode.toUpperCase() !== "NORMAL") {
    // Only initialized and terminated are allowed per section 10.0 of the spec.
    console.log("When launchMode is " + cmi5Controller.launchMode + "only Initialized and Terminated verbs are allowed");
    return false;
  }

  if (verb) {
    // Context extensions were read from the State document's context template
    let stmt, dur = convertMillisecondsToMinSec(duration);
    // Get basic cmi5 allowed statement object
    stmt = cmi5Controller.getcmi5AllowedStatement(verb, JSON.parse(sessionStorage.getItem("stmtObject")), cmi5Controller.getContextActivities(), cmi5Controller.getContextExtensions());
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

    // Add UTC timestamp, required by cmi5 spec.
    stmt.timestamp = (new Date()).toISOString();
    if (duration || progress) {
      stmt.result = {};
      if (typeof(progress) === "number") stmt.result.extensions = {
        "https://w3id.org/xapi/cmi5/result/extensions/progress": progress
      };
      if (duration) stmt.result.duration = convertMillisecondsToISO8601Duration(duration);
    }
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

function trackVideoEvents(videoObj, cExtentions) {
  let result, timing = [],
    startVideo = [],
    progress = [],
    prevTime = [],
    curTime = [],
    vsrc;
  for (let i = 0; i < videoObj.length; i++) {
    timing[i] = 0;
    startVideo[i] = "";
    progress[i] = "";
    prevTime[i] = 0;
    curTime[i] = 0;
    videoObj[i].ontimeupdate = function() {
      prevTime[i] = parseFloat(timing[i].toFixed(2));
      curTime[i] = parseFloat(this.currentTime.toFixed(2));
      if (curTime[i] > prevTime[i] + 1 || curTime[i] < prevTime[i] - 1) {
        result = {
          "extensions": {
            "https://w3id.org/xapi/video/extensions/time-from": prevTime[i],
            "https://w3id.org/xapi/video/extensions/time-to": curTime[i]
          }
        };
        sendVideoStatement("seeked", this, result, cExtentions);
      }
      timing[i] = curTime[i];
    }
    videoObj[i].onplay = function() {
      if (startVideo[i] !== null) {
        let DateTime = new Date();
        startVideo[i] = DateTime.getTime();
      }
      result = {
        "extensions": {
          "https://w3id.org/xapi/video/extensions/time": parseFloat(this.currentTime.toFixed(2))
        }
      };
      sendVideoStatement("played", this, result, cExtentions);
    };
    videoObj[i].onpause = function() {
      progress[i] += prevTime[i] + ' - ' + parseFloat(this.currentTime.toFixed(2)) + ', ';
      prevTime[i] = parseFloat(this.currentTime.toFixed(2));
      result = {
        "extensions": {
          "https://w3id.org/xapi/video/extensions/time": parseFloat(this.currentTime.toFixed(2)),
          "https://w3id.org/xapi/video/extensions/progress": +((this.currentTime / this.duration).toFixed(2)),
          "https://w3id.org/xapi/video/extensions/played-segments": progress[i].substr(0, progress[i].length - 3)
        }
      };
      sendVideoStatement("paused", this, result, cExtentions);
    };
    videoObj[i].onended = function() {
      result = {
        "score": {
          "scaled": 1,
          "min": 0,
          "max": 100,
          "raw": 100
        },
        "success": true,
        "completion": true,
        "response": "",
        "duration": ISO8601_time(startVideo[i])
      };
      sendVideoStatement("ended", this, result, cExtentions);
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
    let stmt, cx, vObj = [];
    // Get basic cmi5 defined statement object
    vObj = {
      "id": location.origin + videoSrcPath,
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
      "https://w3id.org/xapi/video/extensions/volume": videoObj.volume
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
      page, title, stmt;
    if (cmi5Controller.getContextExtensions()) {
      // add cmi5 activity ID
      H5PXapiStmt.object.id = cmi5Controller.activityId + H5PXapiStmt.object.id;
      if (H5Pinit) {
        H5Ptitle = window[0].window.H5P.instances[0].libraryInfo.machineName;
        H5Pinit = false;
      }
      // add cmi5 description: "name of content type" at "name of page"
      H5PXapiStmt.object.definition.name = {
        [cmi5Controller.dLang]: cmi5Controller.dTitle + ': "' + H5Ptitle + '"' + ' at page ' + '"' + bm.pageTitle + '"'
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
      cmi5Controller.sendStatement(stmt);
    }
  };

document.onreadystatechange = function() {
  if (sessionStorage.getItem("cmi5Init") || sessionStorage.getItem("cmi5No") == "true") document.querySelector("body").style.display = "block";
  //else if (location.href.indexOf("simUser") == -1) document.querySelector("body").style.display = "none";
  else document.querySelector("body").style.display = "none";
  if ('complete' === document.readyState && typeof H5P !== 'undefined' && H5P.externalDispatcher && sessionStorage.getItem("cmi5No") == "false") H5P.externalDispatcher.on('xAPI', handleH5P);
};