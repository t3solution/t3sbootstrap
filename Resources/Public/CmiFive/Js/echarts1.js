function echarts1_(event, container, page, temp) {
  //if (page == 0) document.getElementById("exampleModalLabel").innerHTML = event.srcElement.innerHTML;
  let data = getStatementsSuccess("answered", page, temp);
  echartSetup(container, data, temp);
}
// function: LRS query on success score of H5P interaction at page "page"
function getStatementsSuccess(verb, page, temp) {
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
    success,
    successCounter = 0,
    failedCounter = 0,
    su = [];
  sessionStorage.removeItem("h5ppage");
  // sessionStorage.removeItem("objectid");
  for (let i = 0; i < selection.length; i++) {
    success = selection[i].result["success"];
    if (success) successCounter++;
    else failedCounter++;
  }
  su.push({
    name: "Erfolgreich",
    value: Math.round((successCounter * 100) / selection.length)
  });
  su.push({
    name: "Nicht erfolgreich",
    value: Math.round((failedCounter * 100) / selection.length)
  });
  cmi5Controller[temp] = su;
  return su;
}
function echartSetup(container, data_, temp) {
  if (cmi5Controller[temp]) data_ = cmi5Controller[temp];
  if (!data_) return; //{
  //userAlerts("nodata");
  //return;
  //}
  if (document.getElementById(container))
    container = document.getElementById(container);
  if (sessionStorage.getItem("cmi5No") == "false") {
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
}
