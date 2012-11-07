$(document).ready (function () {
	// Go
	$.objectDataStorage.oTable = $('#manageCategories').dataTable ({
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

    // Process
    $('tr').live ('mouseover', function (objEvent) {
    	// Append
    	$('#manageCategories tbody tr').each (function (objI) {
        	// Tds
        	var objData = $.objectDataStorage.oTable.fnGetData (this);
        	var objId = objData[1];

        	// Attributes
        	$(this).attr ('dragDropId', objId);
        });

    	 // Drag
        $('tr').draggable ({
            revert: 'invalid',
            opacity: 0.7,
            helper: "clone",
            cursor: "pointer"
        });

        // Drop
        $('tr').droppable ({
            tolerance: 'intersect',
            drop: function (event, droppedThis) {
                var draggedId = $(droppedThis.draggable).attr ('dragDropId');
                var droppedId = $(this).attr ('dragDropId');

                $('#dragDropConfirmationDialog').dialog ({
                    bgiframe: true,
                    title: 'Confirm',
                    resizable: false,
                    closeOnEscape: true,
                    width: 600,
                    modal: true,

                    buttons: {
                        'First, under it': function () {
                            // Change address and redirect;
                        	window.location = window.location + '/Do/Move/Id/' +
                            draggedId + '/To/' + droppedId + '/Type/' + 1;
                        },
                        'Last, under it': function () {
                            // Change address and redirect;
                            window.location = window.location + '/Do/Move/Id/' +
                            draggedId + '/To/' + droppedId + '/Type/' + 2;
                        },
                        'Previous to it': function () {
                            // Change address and redirect;
                            window.location = window.location + '/Do/Move/Id/' +
                            draggedId + '/To/' + droppedId + '/Type/' + 3;
                        },
                        'Next to it': function () {
                            // Change address and redirect;
                            window.location = window.location + '/Do/Move/Id/' +
                            draggedId + '/To/' + droppedId + '/Type/' + 4;
                        }
                    },

                    close: function () {
                        // Do a page redirect to itself;
                        window.location = window.location;
                    }
                });
            }
        });
    });

    // Hide
    $('#dragDropConfirmationDialog').hide ();
});
