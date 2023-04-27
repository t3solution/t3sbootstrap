var cmi5Controller = (function () {
  // **********************
  // Private variables
  // **********************
  var stmt_;
  var Agent_;
  var endPointConfig;
  var contextActivities;
  var contextExtensions;
  var contextTemplate;
  var initializedCallback;
  var startDateTime;

  // **********************
  // Public properties
  // **********************

  // Initialize properties that are passed on the launch command line
  this.endPoint = "";
  this.fetchUrl = "";
  this.registration = "";
  this.activityId = "";
  this.actor = "";
  this.authToken = "";
  this.object = "";

  // Initialize state properties
  this.sessionId = "";
  this.masteryScore = 0;
  this.launchMode = "";
  this.launchMethod = "";
  this.publisherId = "";
  this.moveOn = "";
  this.launchParameters = "";
  this.returnURL = "";
  this.entitlementKey = {
    courseStructure: "",
    alternate: ""
  };
  this.languagePreference = "";
  this.audioPreference = "";

  // **********************
  // Private functions
  // **********************
  function GetBasicStatement(verb_, object_) {
    // This function creates a basic xAPI statement with actor, verb and object.
    var s = new ADL.XAPIStatement(Agent_, verb_, object_);

    // cmi5 requires that the AU generate the Id.
    s.generateId();
    return s;
  }

  function AuthTokenFetched() {
    // This is the callback function referenced in call to cmi5Controller.getAuthToken();
    // Now that we have the auth token, we can fetch the State Document.
    cmi5Controller.getStateDocument(setStateDocument);
  }

  function SetConfig() {
    // Set LRS endpoint configuration
    endPointConfig = {
      endpoint: cmi5Controller.endPoint,
      auth: "Basic " + cmi5Controller.authToken
    };
  }

  function setAgentProfile(r) {
    // This is the callback method referenced in call to cmi5Controller.getAgentProfile()
    if (r.response.languagePreference || r.response.audioPreference) {
      //var obj = JSON.parse(r.response);
      var obj = r.response;
      if (obj.languagePreference) {
        cmi5Controller.languagePreference = obj.languagePreference;
      }
      if (obj.audioPreference) {
        cmi5Controller.audioPreference = obj.audioPreference;
      }
    } //else console.log("No agent profile found");
    // All activites required by cmi5Controller.startUp() have been performed.  We can return
    // to the calling AU by executing the callback passed to cmi5Controller.startUp().
    startDateTime = Date.now(); // Time of launch
    //startDateTime = new Date(); // Time of launch
    initializedCallback();
  }

  function setStateDocument(r) {
    // This is the callback method referenced in call to cmi5Controller.getStateDocument()
    if (r && r.response !== "xAPI Resource Not Found.") {
      // Parse the State document into an object.

      var obj = JSON.parse(r.response);

      // Get context activities
      contextTemplate = obj.contextTemplate;
      contextActivities = obj.contextTemplate.contextActivities;

      // Get context extensions
      contextExtensions = obj.contextTemplate.extensions;

      // Get returnUrl
      var t = typeof obj["returnURL"];
      if (t === "string") {
        cmi5Controller.returnURL = obj["returnURL"];
        sessionStorage.setItem("returnurl", cmi5Controller.returnURL);
      }
      // Get other state properties into cmi5Controller properties.
      cmi5Controller.moveOn = obj["moveOn"];
      cmi5Controller.masteryScore = obj["masteryScore"];
      cmi5Controller.launchMode = obj["launchMode"];
      cmi5Controller.launchMethod = obj["launchMethod"];
      cmi5Controller.sessionId =
        contextExtensions[
          "https://w3id.org/xapi/cmi5/context/extensions/sessionid"
        ];
      cmi5Controller.publisherId = contextActivities.grouping[0].id;
      cmi5Controller.launchParameters = obj["launchParameters"];
      cmi5Controller.entitlementKey = obj["entitlementKey"];

      // Now, get the agent profile.
      cmi5Controller.getAgentProfile(setAgentProfile);
    } else {
      console.log("No state document found");
    }
  }

  // **********************
  // Public functions
  // **********************
  return {
    // cmi5 controller initialization
    startUp: function (callBack, errorCallBack) {
      // This function fetches the authorization token, reads the State document into cmi5Controller propreties, and fetches the agent Profile.
      // Since the calls are async, they are performed in sequence by calling
      // 1) getAuthToken(), which in turn calls 2) getStateDocument(), which in turn calls 3) getAgentProfile().
      initializedCallback = callBack;
      cmi5Controller.object = {
        objectType: "Activity",
        id: cmi5Controller.activityId
      };
      cmi5Controller.getAuthToken(AuthTokenFetched, errorCallBack);
    },
    getAUActivityId: function () {
      // "getter" for retrieving the AU's activityId passed from the LMS
      return cmi5Controller.activityId;
    },
    getMasteryScore: function () {
      // "getter" for retrieving the masteryScore passed from the LMS
      return cmi5Controller.masteryScore;
    },
    getReturnUrl: function () {
      // "getter" for retrieving the returnURL passed from the LMS
      return cmi5Controller.returnURL;
    },
    getContextActivities: function () {
      // "getter" for retrieving the contextActivities fetched from the State
      return contextActivities;
    },
    getContextExtensions: function () {
      // "getter" for retrieving the contextExtensions fetched from the State
      return contextExtensions;
    },
    getStartDateTime: function () {
      // "getter" for retrieving the time the AU was launched and cmi5Controller.startUp() was completed.
      return startDateTime;
    },
    goLMS: function () {
      // This function returns to the LMS if the returnURL property was provided.  If not, it attempts to close the browser.
      var returnUrl;
      if (cmi5Controller.getReturnUrl())
        returnUrl = cmi5Controller.getReturnUrl();
      else returnUrl = sessionStorage.getItem("returnurl");
      if (typeof returnUrl == "string" && returnUrl.length > 0) {
        var href = decodeURIComponent(returnUrl);
        document.location.href = href;
        return false;
      }
      self.close(); // Not allowed in FireFox
      return false;
    },
    setEndPoint: function (endpoint) {
      // "setter" for the xAPI endpoint property
      if (endpoint) {
        cmi5Controller.endPoint = endpoint;
        //console.log("Endpoint set to " + endpoint);
      } else {
        console.log("Invalid value passed to setEndpoint()");
      }
    },
    setFetchUrl: function (fetchUrl) {
      // "setter" for the fetchUrl property
      // Note: This should not be used by the AU.  The fetchUrl is a command line property
      // that must not change.
      if (fetchUrl) {
        cmi5Controller.fetchUrl = fetchUrl;
        //console.log("fetchUrl set to " + fetchUrl);
      } else {
        console.log("Invalid value passed to setFetchUrl()");
      }
    },
    setObjectProperties: function (language_, type_, name_, description_) {
      // Set additional properties for the xAPI object.
      // The cmi5Controller already knows the object ID to use in cmi5-defined statements
      // since it is passed on the launch command.
      // It does not know:
      // 1) The langstring used by the AU.
      // 2) The actitityType
      // 3) The name of the AU
      // 4) The description of the AU
      //
      cmi5Controller.object.definition = {};
      if (type_) {
        cmi5Controller.object.definition.type = type_;
      }
      if (!language_) language_ = "und";
      if (name_) {
        cmi5Controller.object.definition.name = {};
        cmi5Controller.object.definition.name[language_] = name_;
      }
      if (description_) {
        cmi5Controller.object.definition.description = {};
        cmi5Controller.object.definition.description[language_] = description_;
      }
    },
    setRegistration: function (registration) {
      // "setter" for the registration property.
      // Note: This should not be used by the AU.  The registration is a command line property
      // that must not change.
      if (registration) {
        cmi5Controller.registration = registration;
        //console.log("Registration set to " + registration);
      } else {
        console.log("Invalid value passed to setRegistration()");
      }
    },
    setActivityId: function (activityId) {
      // "setter" for the activityId property.
      // Note: This should not be used by the AU.  The value is a command line property
      // that must not change.
      if (activityId) {
        cmi5Controller.activityId = activityId;
        //console.log("Activity ID set to " + activityId);
      } else {
        console.log("Invalid value passed to setActivityId()");
      }
    },
    setActor: function (actor) {
      // "setter" for the activityId property.
      // Note: This should not be used by the AU.  The value is a command line property
      // that must not change.
      Agent_ = JSON.parse(actor);
      if (actor) {
        if (Agent_.objectType !== "Agent") {
          console.log("In cmi5, the actor must have an objectType of Agent.");
          return;
        }
        if (Agent_.account === null) {
          console.log("In cmi5, the account property of an Agent is required.");
          return;
        }
        if (!Agent_.account.homePage) {
          console.log(
            "In cmi5, the homePage property of an account is required."
          );
          return;
        }
        if (!Agent_.account.name) {
          console.log("In cmi5, the name property of an account is required.");
          return;
        }

        cmi5Controller.agent = Agent_;
      } else {
        console.log("Invalid value passed to setActor()");
      }
    },
    getAuthToken: function (successCallback, tokenErrorCallBack) {
      // getAuthToken() calls the fetch url to get the authorization token. Two callback functions
      // may be provided. The first is what to call upon success, the other when there is an error.
      // If cmi5Controller.startUp() is used, this method should not be called by the AU.

      // First, check if we have already retrieved the auth token.
      var token = sessionStorage.getItem(cmi5Controller.fetchUrl);
      if (token) {
        // We already have the auth token.  Do not call the fetchUrl again.
        cmi5Controller.authToken = token;
        SetConfig();
        if (successCallback && typeof successCallback === "function") {
          successCallback();
          return;
        }
      }
      // We do not already have the auth token.  Make call to fetchUrl to get it.
      var myRequest = new XMLHttpRequest();
      myRequest.open("POST", cmi5Controller.fetchUrl, true);
      //myRequest.withCredentials = true; // RK: "Cannot use wildcard in Access-Control-Allow-Origin when credentials flag is true.""
      myRequest.onreadystatechange = function () {
        if (this.readyState === 4) {
          if (this.status === 200) {
            var data = JSON.parse(myRequest.responseText);

            // Check for error
            var e = typeof data["error-code"];
            if (e === "string") {
              console.log(
                "error-code " + data["error-code"] + ": " + data["error-text"]
              );
              if (
                tokenErrorCallBack &&
                typeof tokenErrorCallBack === "function"
              ) {
                tokenErrorCallBack("");
              }
            }

            e = typeof data["auth-token"];
            if (e === "string") {
              cmi5Controller.authToken = data["auth-token"];

              // Store the token to sessionStorage
              sessionStorage.setItem(
                cmi5Controller.fetchUrl,
                cmi5Controller.authToken
              );

              // Set the endPointConfig to use this auth token
              SetConfig();
              if (successCallback && typeof successCallback === "function") {
                successCallback();
              }
            } else {
              console.log("Invalid structure returned: " + data.toString());
              if (
                tokenErrorCallBack &&
                typeof tokenErrorCallBack === "function"
              )
                tokenErrorCallBack("");
            }
          } else
            console.log(
              "Call to fetchUrl failed with status " + this.status.toString()
            );
        }
      };
      myRequest.send();
    },
    getAgentProfile: function (callback) {
      // Retrieves the agent profile document and sets cmi5Controller properties accordingly.
      // If cmi5Controller.startUp() is used, this method should not be called by the AU.
      ADL.XAPIWrapper.changeConfig(endPointConfig);
      ADL.XAPIWrapper.getAgentProfile(
        Agent_,
        "cmi5LearnerPreferences",
        null,
        callback
      );
    },
    getStateDocument: function (callback) {
      // Retrieves the cmi5 State document and sets cmi5Controller properties accordingly.
      // If cmi5Controller.startUp() is used, this method should not be called by the AU.
      ADL.XAPIWrapper.changeConfig(endPointConfig);
      ADL.XAPIWrapper.getState(
        cmi5Controller.activityId,
        Agent_,
        "LMS.LaunchData",
        cmi5Controller.registration,
        null,
        callback
      );
    },
    getcmi5AllowedStatement: function (
      verb_,
      object_,
      contextActivities_,
      contextExtensions_
    ) {
      // This method returns a basic "cmi5 allowed" statement that can be extended as needed by the AU.
      stmt_ = GetBasicStatement(verb_, object_);

      // Add registration
      stmt_.context = {
        registration: cmi5Controller.registration
      };

      // If context parms are not passed, use defaults from cmi5 State document.
      if (!contextActivities_) {
        contextActivities_ = cmi5Controller.getContextActivities();
      }

      if (!contextExtensions_) {
        contextExtensions_ = cmi5Controller.getContextExtensions();
      }

      if (typeof contextActivities_ !== "undefined") {
        if (contextActivities_.hasOwnProperty("category")) {
          // Do not include the cmi5 category for "allowed" statements
          delete contextActivities_.category;
        }
      }

      stmt_.context.contextActivities = contextActivities_;

      // Extensions
      stmt_.context.extensions = contextExtensions_;

      return stmt_;
    },
    getcmi5DefinedStatement: function (verb_, contextExtentions_) {
      // This function builds a "cmi5 defined" statement
      stmt_ = GetBasicStatement(verb_, cmi5Controller.object);

      // If context extensions not passed, use default.
      if (!contextExtentions_) {
        contextExtentions_ = cmi5Controller.getContextExtensions();
      }

      // Add registration
      stmt_.context = {
        registration: cmi5Controller.registration
      };

      // Context activities from State API
      var z = contextActivities;
      stmt_.context.contextActivities = z;

      // cmi5 Context activity
      stmt_.context.contextActivities.category = [];
      stmt_.context.contextActivities.category.push({
        id: "https://w3id.org/xapi/cmi5/context/categories/cmi5"
      });

      // Extensions
      stmt_.context.extensions = contextExtentions_;

      return stmt_;
    },
    sendAllowedState: function (
      stateid_,
      statevalue_,
      matchHash_,
      noneMatchHash_,
      callback_
    ) {
      // This function may be used to write an xAPI State document
      ADL.XAPIWrapper.changeConfig(endPointConfig);
      //ADL.XAPIWrapper.sendState(cmi5Controller.activityId, stateid_, statevalue_, matchHash_, noneMatchHash_, callback_);
      // RK: Fehler in "sendAllowedState" behoben!
      //ADL.XAPIWrapper.sendState(cmi5Controller.activityId, stateid_, statevalue_, matchHash_, noneMatchHash_, callback_);
      ADL.XAPIWrapper.sendState(
        cmi5Controller.activityId,
        Agent_,
        stateid_,
        cmi5Controller.registration,
        statevalue_,
        matchHash_,
        noneMatchHash_,
        callback_
      );
    },
    getAllowedState: function (stateid_, since_, callback_) {
      // This function retrieves an xAPI State document created by the AU.
      ADL.XAPIWrapper.changeConfig(endPointConfig);
      // RK: "return" ergänzt!
      // ADL.XAPIWrapper.getState(cmi5Controller.activityId, Agent_, stateid_, cmi5Controller.registration, since_, callback_);
      return ADL.XAPIWrapper.getState(
        cmi5Controller.activityId,
        Agent_,
        stateid_,
        cmi5Controller.registration,
        since_,
        callback_
      );
    },
    sendStatement: function (statement_, callback_) {
      // Send an xAPI statement.  Option paremeter to have a callback function.  This is mostly a passthrough to the xAPIWrapper's
      // sendStatement() method.  However, if an array of statements named "statementsSent is defined in the AU, the statement
      // will be pushed to that array.

      // If array statementsSent is defined, push statements to the array.
      if (
        window.statementsSent &&
        typeof window.statementsSent === "object" &&
        Array.isArray(window.statementsSent)
      ) {
        window.statementsSent.push(statement_);
      }
      ADL.XAPIWrapper.changeConfig(endPointConfig);
      ADL.XAPIWrapper.sendStatement(statement_, callback_);
    },
    sendStatements: function (statements_, callback_) {
      ADL.XAPIWrapper.changeConfig(endPointConfig);
      ADL.XAPIWrapper.sendStatements(statements_, callback_);
    }
  };
})();
