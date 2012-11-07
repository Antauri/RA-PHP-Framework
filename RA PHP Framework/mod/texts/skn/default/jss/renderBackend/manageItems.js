$(document).ready (function () {
	// Go
	$.objectDataStorage.oTable = $('#manageItems').dataTable ({
		"sDom": '<"clear">lfrptip',
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
		"aaSorting": [[4, "desc"]],
		"aoColumns": [{"bSortable": true, "bSearchable": false, "bVisible": false},
		              null,
		              {"sWidth": "20%"},
		              null,
		              null,
		              null,
		              {"bSortable": false, "bSearchable": false}]
	});

	// Show
	$('.generic-actions').show ();
});