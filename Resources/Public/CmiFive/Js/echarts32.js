function echarts32_(event, container) {
  container = document.getElementById(container);
  setTimeout(() => {
    echartSetup2(container);
  }, 100);
}

function echartSetup2(container, data_) {
  if (sessionStorage.getItem("cmi5No")) {
    let myChart = echarts.init(container),
      option,
      data = cmi5Controller.st.pr,
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
        type: "value"
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
