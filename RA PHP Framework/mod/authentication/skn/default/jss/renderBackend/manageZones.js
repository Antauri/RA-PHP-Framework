$(document).ready (function () {
	// Go
	$.objectDataStorage.oTable = $('#manageZones').dataTable ({
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
		"aaSorting": [[0, "asc"], [1, "asc"]],
		"aoColumns": [{"bSortable": true, "bSearchable": false, "bVisible": false},
		              {"bSortable": true, "bSearchable": false, "bVisible": false},
		              {"bSortable": true, "bSearchable": false, "bVisible": false},
		              {"bSortable": false},
		              {"bSortable": false, "bSearchable": false}]
	});

	// Show
	$('.generic-actions').show ();
});
