function echarts2_(event, container, page, temp) {
  //if (page == 0) document.getElementById("exampleModalLabel").innerHTML = event.srcElement.innerHTML;
  let data = getStatementsPoll("interacted", page, temp);
  echartSetup(container, data, temp);
}

// function: LRS query on result of poll of H5P interaction at page "page"
function getStatementsPoll(verb, page, temp) {
  if (cmi5Controller[temp]) return;
  let h5pObjectIdAndPage = bm.getH5pObjectIdAndPage(page);
  if (!h5pObjectIdAndPage[0][0]) return;
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
      h5pObjectIdAndPage[0][0], //activity
      h5pObjectIdAndPage[1], //page
      true //more
    ),
    choice0 = 0,
    choice1 = 0,
    choice2 = 0,
    choice3 = 0,
    choices = [];
  sessionStorage.removeItem("h5ppage");
  // sessionStorage.removeItem("objectid");
  for (let i = 0; i < selection.length; i++) {
    if (selection[i].result) {
      switch (selection[i].result["response"]) {
        case "0":
          choice0++;
          break;
        case "1":
          choice1++;
          break;
        case "2":
          choice2++;
          break;
        case "3":
          choice3++;
          break;
      }
    }
  }
  choices.push({
    name: "Wusste es",
    value: Math.round((choice0 * 100) / selection.length)
  });
  choices.push({
    name: "Dachte, er weiß",
    value: Math.round((choice1 * 100) / selection.length)
  });
  choices.push({
    name: "Nicht sicher",
    value: Math.round((choice2 * 100) / selection.length)
  });
  choices.push({
    name: "Keine Ahnung",
    value: Math.round((choice3 * 100) / selection.length)
  });
  cmi5Controller[temp] = choices;
  return choices;
}

function echartSetup(container, data_, temp) {
  if (cmi5Controller[temp]) data_ = cmi5Controller[temp];
  if (!data_) return;
  if (document.getElementById(container))
    container = document.getElementById(container);
  let myChart = echarts.init(container),
    option,
    data = data_,
    datax = [],
    datay = [];
  for (let i = 0; i < data.length; i++) {
    datax.push(data[i].name);
    datay.push(data[i].value);
  }
  option = {
    xAxis: {
      type: "category",
      data: datax
    },
    yAxis: {
      type: "value",
      axisLabel: {
        formatter: "{value} %"
      }
    },
    series: [
      {
        data: datay,
        type: "bar",
        label: {
          show: true
        },
        emphasis: {
          focus: "series"
        },
        itemStyle: {
          normal: {
            //  Random display
            //color:function(d){return "#"+Math.floor(Math.random()*(256*256*256-1)).toString(16);}
            //  Custom display (in order)
            color: function (params) {
              var colorList = ["#fac858", "#5470c6", "#91cc75", "#ee6666"];
              return colorList[params.dataIndex];
            }
          }
        }
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
