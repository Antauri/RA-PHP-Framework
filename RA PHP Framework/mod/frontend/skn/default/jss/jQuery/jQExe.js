$(document).ready (function () {
  // Hostname
  $.objectSystemConstants = {};

  // Timer status
  var objTimerStatus = 0;
  var objTimerXHR;

  // Set
  $.post ('Type/Settings/Kind/Json/Method/GetHostname', {
    type: "GetHostname"
  }, function (data) {
    // Set
    $.objectSystemConstants.GetHostname = data;

    // Cron
    $(document).everyTime ("3s", "Every N Seconds", function () {
      // Request
      objTimerXHR = $.post ($.objectSystemConstants.GetHostname +
      'Cron/Go/Type/Settings/Execute/Timer', {
        type: "Cron Timer"
      }, function (data) {
        // Check
        if (data == "Stop" 	 ||
          data == "Timer Reset") {
          // Set
          objTimerStatus = 1;

          // Stop
          $(document).stopTime ("Every N Seconds");
        } else {
          // Trigger
          alert ('Error in cron trigger!');
        }
      });
    });

    // Cron again
    $(document).oneTime ("15s", "Check Timer Status", function () {
      // Check
      if (objTimerStatus == 0) {
        // Stop
        $(document).stopTime ("Every N Seconds");
        objTimerXHR.abort ();
      }
    });
  });

  $('#div_Offers').hide ();

  // Facebox
  $('[rel=Box]').facebox ();
});
