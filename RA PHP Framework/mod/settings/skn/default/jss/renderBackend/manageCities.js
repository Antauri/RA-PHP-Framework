$(document).ready (function () {
  // Go
  $.objectDataStorage.oTable = $('#manageCities').dataTable ({
    "sDom": '<"clear">lfrtip',
    "aLengthMenu": aLengthMenuDefault,
    "bPaginate": true,
    "bLengthChange": true,
    "bFilter": true,
    "bSort": true,
    "bInfo": true,
    "bDeferRender": true,
    "bAutoWidth": false,
    "bProcessing": false,
    "bServerSide": true,
    "sAjaxSource": window.location,
    "oLanguage": {
      "sSearch": "Search any:"
    },
    "fnServerData": function (sSource, aoData, fnCallback) {
      $.ajax ({
        "dataType": 'json',
        "type": "POST",
        "url": sSource,
        "data": aoData,
        "success": fnCallback
      });
    },
    "aaSorting": [[2, "asc"]],
    "aoColumns": [{"bSortable": true, "bSearchable": false, "bVisible": false},
                  null,
                  null,
                  {"bSortable": false, "bSearchable": false}]
  });

  // Show
  $('.generic-actions').show ();
});
