function echarts33_(event, container) {
  container = document.getElementById(container);
  setTimeout(() => {
    echartSetup3(container);
  }, 100);
}
function echartSetup3(container, data_) {
  if (sessionStorage.getItem("cmi5No")) {
    let myChart = echarts.init(container),
      option,
      data = cmi5Controller.st.du,
      datax = [],
      datay = [];

    for (let i = 0; i < data.length; i++) {
      datay.push(data[i].name);
      datax.push(data[i].value);
    }
    option = {
      yAxis: {
        type: "category",
        data: datay
      },
      xAxis: {
        type: "value"
      },
      series: [
        {
          data: datax,
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
                let colorList = [
                  "#fac858",
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
