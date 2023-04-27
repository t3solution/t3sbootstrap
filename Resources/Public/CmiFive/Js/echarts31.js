function echarts31_(event, container) {
  container = document.getElementById(container);
  let d = getStatementsVpd("progressed");
  echartSetup(container, d);
}

// function: LRS query on visits of current page, on progress (pages visited) and on duration at current page
function getStatementsVpd(verb, page) {
  let selection = bm.getStatementsBase(
      verb,
      "", //agent
      "", //registration
      "", //sessionid
      "", //since
      "", //untill
      true, //relatedactivities
      true, //relatedagents
      "ids", //format
      cmi5Controller.activityId +
        "/objectid/" +
        location.hostname +
        location.pathname, //activity
      location.pathname, //page
      true //more
    ),
    actorAccount = cmi5Controller.agent.account,
    progress,
    duration,
    match,
    selectionNameVisits = {},
    selectionNameProgress = {},
    selectionNameDuration = {};
  for (let i = 0; i < selection.length; i++) {
    match = selection[i].actor.account.name;
    progress =
      selection[i].result.extensions[
        "https://w3id.org/xapi/cmi5/result/extensions/progress"
      ];
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
      selectionNameProgress[p[i]] = parseInt(
        selectionNameProgress[p[i]] / selectionNameVisits[p[i]]
      );
      selectionNameDuration[p[i]] = selectionNameDuration[p[i]] / 60;
      selectionNameDuration[p[i]] = selectionNameDuration[p[i]].toFixed(1);
      if (p[i] === actorAccount.name) user = "Me and myself";
      else user = "User " + (i + 1);
      cmi5Controller.st.vi.push({
        name: user,
        value: selectionNameVisits[p[i]]
      });
      cmi5Controller.st.pr.push({
        name: user,
        value: selectionNameProgress[p[i]]
      });
      cmi5Controller.st.du.push({
        name: user,
        value: selectionNameDuration[p[i]]
      });
    }
  }
  cmi5Controller.st.vi.sort((a, b) => (a[0] < b[0] ? 1 : -1));
  cmi5Controller.st.pr.sort((a, b) => (a[0] < b[0] ? 1 : -1));
  cmi5Controller.st.du.sort((a, b) => (a[0] < b[0] ? 1 : -1));
}

function echartSetup(container, data_) {
  let myChart = echarts.init(container),
    option;
  option = {
    tooltip: {
      trigger: "item"
    },
    legend: {
      top: "5%",
      left: "center"
    },
    series: [
      {
        name: "Access From",
        type: "pie",
        radius: ["40%", "70%"],
        avoidLabelOverlap: false,
        itemStyle: {
          borderRadius: 10,
          borderColor: "#fff",
          borderWidth: 2,
          color: function (params) {
            var colorList = [
              "#fac858",
              "#5470c6",
              "#5470c6",
              "#5470c6",
              "#5470c6",
              "#91cc75",
              "#fac858",
              "#ee6666"
            ];
            return colorList[params.dataIndex];
          }
        },
        label: {
          show: false,
          position: "center"
        },
        emphasis: {
          label: {
            show: true,
            fontSize: "40",
            fontWeight: "bold"
          }
        },
        labelLine: {
          show: false
        },
        data: cmi5Controller.st.vi
      }
    ]
  };

  if (option && typeof option === "object") myChart.setOption(option);
  if (document.querySelector(".spinner-border"))
    document.querySelector(".spinner-border").style.display = "none";
  window.addEventListener("resize", function (event) {
    myChart.resize();
  });
}
