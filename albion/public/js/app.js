(function() {
  $("#LCgeneral").change(function() {
    var n;
    n = $(this).val();
    switch (n) {
      case "week":
        Morris.Area;
        return {
          element: 'acc-lineChart-gen',
          data: $LCGeneralWeek,
          xkey: 'date',
          ykeys: ['total'],
          xLabels: "day",
          labels: ['Total'],
          resize: true
        };
    }
  });

}).call(this);

//# sourceMappingURL=app.js.map