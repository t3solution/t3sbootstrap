// function: Wann und wie lange waren einzelne Nutzer im Lernmodul?
function echarts5_(event, container, page, echartQuery) {
  let data = getStatementsDuration("terminated", page, echartQuery);
  if (typeof data !== "undefined") echartSetup(container, data, echartQuery);
}

// function: get duration of terminated statements
function getStatementsDuration(verb, page, echartQuery) {
  let stmtsQ = sessionStorage.getItem("statements"),
    since,
    until,
    stmtsQ_,
    qStored;
  if (stmtsQ) {
    stmtsQ = JSON.parse(stmtsQ);
    for (let i = 0; i < stmtsQ.length; i++) {
      if (Object.keys(stmtsQ[i]).includes(echartQuery)) stmtsQ_ = stmtsQ[i];
    }
  }
  stmtsQ = stmtsQ_;
  if (
    stmtsQ &&
    Object.keys(stmtsQ) !== "undefined" &&
    Object.keys(stmtsQ).includes(echartQuery)
  ) {
    until = new Date();
    let l = stmtsQ[echartQuery].length - 1;
    since = stmtsQ[echartQuery][l]["date"];
    stmtsQ = stmtsQ[echartQuery];
    cmi5Controller[echartQuery] = stmtsQ;
    qStored = true;
  }
  let selection = bm.getStatementsBase(
      verb,
      "", //agent
      "", //registration
      "", //sessionid
      since,
      until,
      false, //relatedactivities
      true, //relatedagents
      "ids", //format
      cmi5Controller.activityId,
      "", //page
      true, //more
      "", //extensionsActivityId
      echartQuery //query
    ),
    dur = [];
  /*var adlc = new ADL.Collection(selection);
  console.log(adlc);
  var activities = adlc
    // remove statements with duplicate object ids
    .groupBy("actor.account.name")
    .select("group");
  activities.exec(function (data) {
    console.log(data);
  });*/
  for (let i = 0; i < selection.length; i++) {
    dur.unshift({
      ["name"]: selection[i].actor.account.name,
      ["date"]: selection[i].stored,
      ["duration"]: parseInt(
        parseISO8601Duration(selection[i].result["duration"]) / 60
      )
    });
  }
  if (qStored) dur.push(...stmtsQ);
  if (
    cmi5Controller[echartQuery] &&
    cmi5Controller[echartQuery].length !== dur.length
  )
    cmi5Controller[echartQuery].push(...dur);
  else cmi5Controller[echartQuery] = dur;
  let statements = [];
  if (
    sessionStorage.getItem("statements") &&
    sessionStorage.getItem("statements") !== []
  ) {
    statements = JSON.parse(sessionStorage.getItem("statements"));
    let i_ = -1;
    for (let i = 0; i < statements.length; i++) {
      if (Object.keys(statements[i]).includes(echartQuery)) i_ = i;
    }
    if (i_ > -1)
      statements[i_] = { [echartQuery]: cmi5Controller[echartQuery] };
    else statements.push({ [echartQuery]: cmi5Controller[echartQuery] });
  }
  sessionStorage.setItem("statements", JSON.stringify(statements));
  return cmi5Controller[echartQuery];
}

// function: draw echart
function echartSetup(container, data_, echartQuery) {
  if (cmi5Controller[echartQuery]) data_ = cmi5Controller[echartQuery];
  if (document.getElementById(container))
    container = document.getElementById(container);
  if (sessionStorage.getItem("cmi5No") == "false") {
    let myChart = echarts.init(container),
      option,
      dmin = data_[0]["date"],
      dmax = data_[data_.length - 1]["date"],
      dn = [],
      series = [],
      pieData = [],
      day = new Date(dmin),
      freqUsers = getFrequencyOf(data_, "name"),
      numberOfDays;
    dn[0] = subAddDays(day, day.getDay(), -1);
    numberOfDays = getNumberOfDays(dn[0], dmax) + 1;

    for (let i = 0; i < numberOfDays; i++) {
      if (i > 0) dn[i] = subAddDays(dn[i - 1]);
    }
    for (let j = 0; j < Object.keys(freqUsers).length; j++) {
      series.push({
        name: "User " + (j + 1), //Object.keys(freqUsers)[j],
        type: "bar",
        emphasis: {
          focus: "series"
        },
        barGap: 0,
        label: {
          show: true,
          position: "top",
          distance: "-20",
          align: "center",
          verticalAlign: "middle",
          fontSize: 14,
          color: "white",
          rotate: "90"
        },
        stack: "",
        data: []
      });
    }

    for (let i = 0; i < numberOfDays; i++) {
      if (i > 0) dn[i] = subAddDays(dn[i - 1]);
      for (let j = 0; j < data_.length; j++) {
        if (getNumberOfDays(dn[0], data_[j]["date"]) === i) {
          for (let k = 0; k < Object.keys(freqUsers).length; k++) {
            if (Object.keys(freqUsers)[k] === data_[j]["name"]) {
              series[k].data.push({
                value: [i, data_[j]["duration"]],
                name: "Bearbeitungsdauer (min):"
              });
              series[k].stack = i.toString();
            }
          }
        }
      }
    }
    let totalTimePerUser = 0,
      totalTimeOfUsers = [];
    for (let j = 0; j < series.length; j++) {
      for (let k = 1; k < series[j].data.length; k++) {
        totalTimePerUser += series[j].data[k].value[1];
        if (series[j].data[k - 1].value[0] === series[j].data[k].value[0]) {
          series[j].data[k].value[1] += series[j].data[k - 1].value[1];
          delete series[j].data[k - 1];
        }
      }
      if (series[j].data.length < 2) {
        if (typeof series[j].data !== "undefined")
          totalTimePerUser = series[j].data[0].value[1];
      }
      totalTimeOfUsers.push(totalTimePerUser);
      totalTimePerUser = 0;
    }
    for (let i = 0; i < Object.keys(freqUsers).length; i++) {
      pieData.push({
        value: totalTimeOfUsers[i],
        name: "User " + (i + 1), //Object.keys(freqUsers)[i],
        itemStyle: {
          //color: color
        }
      });
    }

    series.push({
      name: "Bearbeitungsdauer",
      type: "pie",
      radius: [0, 70],
      center: ["87%", "27%"],
      itemStyle: {
        borderRadius: 5
      },
      label: {
        show: true
      },
      emphasis: {
        label: {
          show: true
        }
      },
      data: pieData
    });
    for (let i = 0, m; i < numberOfDays; i++) {
      m = dn[i].getMonth() + 1;
      dn[i] =
        dn[i].getDate().toString().padStart(2, "0") +
        "." +
        m.toString().padStart(2, "0") +
        "." +
        dn[i].getFullYear();
    }

    option = {
      title: {
        text: "Wann und wie lange waren einzelne Nutzer im Lernmodul?",
        left: "3%"
      },
      toolbox: {
        show: true,
        feature: {
          dataZoom: {
            yAxisIndex: "false"
          },
          dataView: {
            readOnly: false
          },
          magicType: {
            type: ["line", "bar", "stack"]
          },
          restore: {},
          saveAsImage: {}
        }
      },
      dataZoom: [
        {
          type: "slider",
          xAxisIndex: 0,
          filterMode: "none"
        },
        {
          type: "inside",
          xAxisIndex: 0,
          filterMode: "none"
        }
      ],
      legend: {
        orient: "vertical",
        left: "75%",
        top: "48%",
        type: "scroll"
      },
      tooltip: {
        //trigger: "axis",
        axisPointer: {
          type: "shadow"
        }
      },
      grid: {
        left: "3%",
        right: "27%",
        bottom: "3%",
        top: "12%",
        containLabel: true
      },
      xAxis: [
        {
          type: "category",
          data: dn,
          minInterval: 1,
          axisTick: {
            alignWithLabel: true
          },
          splitArea: {
            interval: 0,
            show: true,
            areaStyle: {
              color: [
                "rgba(0,0,0,0)",
                "rgba(0,0,0,0)",
                "rgba(0,0,0,0)",
                "rgba(0,0,0,0)",
                "rgba(0,0,0,0)",
                "rgba(0,0,0,0.05)",
                "rgba(0,0,0,0.05)"
              ]
            }
          }
        }
      ],
      yAxis: [
        {
          type: "value",
          minInterval: 1
        }
      ],
      series: series
    };

    if (option && typeof option === "object") {
      myChart.setOption(option);
    }
    document.querySelector(".spinner-border").style.display = "none";
    window.addEventListener("resize", myChart.resize);

    var zoomSize = 10,
      click = true,
      sv,
      ev;
    myChart.on("click", function (params) {
      if (params.componentSubType === "bar") {
        if (click) {
          click = false;
          sv = params.value[0] - zoomSize / 2;
          ev = params.value[0] + zoomSize / 2;
        } else {
          click = true;
          ev = 1000;
          sv = 0;
        }
        myChart.dispatchAction({
          type: "dataZoom",
          startValue: sv,
          endValue: ev
        });
      }
    });
  }
}
