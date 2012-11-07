// Check key type on keypress!
function checkKey (evt, str) {
    var e = evt ? evt : event;
    return  e.which === 0   ||
            e.which == 8    ||
            e.keyCode == 13 ||
            !(new RegExp (str)).test (String.fromCharCode (e.which ? e.which : e.keyCode));
}

$(document).ready (function () {
	// Set [numeric sorting via title attribute, up]
	jQuery.fn.dataTableExt.oSort['title-numeric-asc']  = function (a,b) {
	    var x = a.match (/title="*(-?[0-9\.]+)/)[1];
	    var y = b.match (/title="*(-?[0-9\.]+)/)[1];
	    x = parseFloat (x);
	    y = parseFloat (y);
	    
	    // Return
	    return ((x < y) ? -1 : ((x > y) ?  1 : 0));
	};
	 
	// Set [numeric sorting via title attribute, down]
	jQuery.fn.dataTableExt.oSort['title-numeric-desc'] = function (a,b) {
	    var x = a.match (/title="*(-?[0-9\.]+)/)[1];
	    var y = b.match (/title="*(-?[0-9\.]+)/)[1];
	    x = parseFloat (x);
	    y = parseFloat (y);
	    
	    // Return
	    return ((x < y) ?  1 : ((x > y) ? -1 : 0));
	};
	
	function trim(str) {
	    str = str.replace(/^\s+/, '');
	    for (var i = str.length - 1; i >= 0; i--) {
	        if (/\S/.test(str.charAt(i))) {
	            str = str.substring(0, i + 1);
	            break;
	        }
	    }
	    return str;
	}
	 
	// Set date sorting
	jQuery.fn.dataTableExt.oSort['date-euro-asc'] = function (a, b) {
	    if (trim (a) != '') {
	        var frDatea = trim (a).split (' ');
	        var frTimea = frDatea[1].split (':');
	        var frDatea2 = frDatea[0].split ('/');
	        var x = (frDatea2[2] + frDatea2[1] + frDatea2[0] + frTimea[0] + frTimea[1] + frTimea[2]) * 1;
	    } else {
	        var x = 10000000000000;
	    }
	 
	    if (trim (b) != '') {
	        var frDateb = trim(b).split (' ');
	        var frTimeb = frDateb[1].split (':');
	        frDateb = frDateb[0].split ('/');
	        var y = (frDateb[2] + frDateb[1] + frDateb[0] + frTimeb[0] + frTimeb[1] + frTimeb[2]) * 1;                     
	    } else {
	        var y = 10000000000000;                    
	    }
	    
	    var z = ((x < y) ? -1 : ((x > y) ? 1 : 0));
	    return z;
	};
	 
	jQuery.fn.dataTableExt.oSort['date-euro-desc'] = function (a, b) {
	    if (trim (a) != '') {
	        var frDatea = trim (a).split (' ');
	        var frTimea = frDatea[1].split (':');
	        var frDatea2 = frDatea[0].split ('/');
	        var x = (frDatea2[2] + frDatea2[1] + frDatea2[0] + frTimea[0] + frTimea[1] + frTimea[2]) * 1;                      
	    } else {
	        var x = 10000000000000;                    
	    }
	 
	    if (trim (b) != '') {
	        var frDateb = trim (b).split (' ');
	        var frTimeb = frDateb[1].split (':');
	        frDateb = frDateb[0].split ('/');
	        var y = (frDateb[2] + frDateb[1] + frDateb[0] + frTimeb[0] + frTimeb[1] + frTimeb[2]) * 1;                     
	    } else {
	        var y = 10000000000000;                    
	    }
	    
	    var z = ((x < y) ? 1 : ((x > y) ? -1 : 0));                  
	    return z;
	};
	
	// Check
	if ($.browser.msie) {
		// Warn
		alert ('.:: You are using Internet Explorer, a.k.a. IE.\n' + 
			   '.:: We have banned every version of IE to date.\n' + 
			   '.:: Get Firefox, Chrome or Safari instead.\n' +
			   '.:: Redirecting you to getfirefox.com now.');
		
		// Redirect
		window.location = 'http://getfirefox.com';
	}
	
    // Upon focus in submits, blur please
    $('input[type=submit]').focus (function () {
        $(this).blur ();
    });

    // Clock
    $('.jQClock').jclock();

    // Target
    $('form').attr ('target', '_self');

    // Suggestions
	$('.RA_Form_Input_RA_AutoSuggest').autocomplete (window.location + '/Ajax/Suggest', {
		selectFirst: false,
		autoFill: false,
		cacheLength: 5000,
		delay: 2,
		mustMatch: false,
		minChars: 2,
		max: 1000,
		extraParams: {
			ra_form_caller: $(this).attr ('name')
		}
	});
	
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

	// Length
	aLengthMenuDefault = [[10, 25, 50, 100, 500, 1000, 5000, 10000],
	                      [10, 25, 50, 100, 500, 1000, 5000, 10000]];

	// Easy
    $.fn.EasyWidgets ({
        behaviour: {
            dragRevert : 300,
            useCookies : true
        },

		effects: {
			effectDuration : 400,
			widgetShow : 'fade',
			widgetHide : 'fade',
			widgetClose : 'fade',
			widgetExtend : 'fade',
			widgetCollapse : 'fade',
			widgetOpenEdit : 'fade',
			widgetCloseEdit : 'fade',
			widgetCancelEdit : 'fade'
		},

		i18n: {
			editText:        '<img src="mod/administration/skn/default/img/jQuery/jQWidget/edit.png" />',
			closeText:       '<img src="mod/administration/skn/default/img/jQuery/jQWidget/close.png" />',
			collapseText:    '<img src="mod/administration/skn/default/img/jQuery/jQWidget/collapse.png" />',
			cancelEditText:  '<img src="mod/administration/skn/default/img/jQuery/jQWidget/edit.png" />',
			extendText:      '<img src="mod/administration/skn/default/img/jQuery/jQWidget/extend.png" />'
        }
    });

    // Add
    $('button.add').live ('click', function (event) {
    	// Redirect
    	window.location = $(this).attr ('href');
    });

    // Edit
    $('button.edit').live ('click', function (event) {
    	// Redirect
    	window.location = $(this).attr ('href');
    });

    // Properties
    $('button.properties').live ('click', function (event) {
    	// Redirect
    	window.location = $(this).attr ('href');
    });

    // Images
    $('button.images').live ('click', function (event) {
    	// Redirect
    	window.location = $(this).attr ('href');
    });

    // Videos
    $('button.videos').live ('click', function (event) {
    	// Redirect
    	window.location = $(this).attr ('href');
    });

    // Comments
    $('button.comments').live ('click', function (event) {
    	// Redirect
    	window.location = $(this).attr ('href');
    });
    
    // Audios
    $('button.audios').live ('click', function (event) {
    	// Redirect
    	window.location = $(this).attr ('href');
    });
    
    // Attachmenst
    $('button.attachments').live ('click', function (event) {
    	// Redirect
    	window.location = $(this).attr ('href');
    });    

    // Erase
    $('button.erase').live ('click', function (event) {
    	// Confirm
        var whatDidIAnswer = confirm ('Are you sure?');

        // Check
        if (!whatDidIAnswer) {
        	// Return
            return false;
        } else {
        	// Redirect
        	window.location = $(this).attr ('href');
        }
    });

	// Notification
	$('.close-notification').click (function () {
		// Set
		$.post ($(this).attr ('href'), {
			type: "Hide Notification"
		}, function (data) {
			// Check
			if (data.length != 0) {
				// Hide
				$('[data-id=' + data + ']').parent ().parent ().fadeTo (350, 0, function () {
					$(this).slideUp (600);
				});
			}
		});

		// Return
		return false;
	});

	// Errors
	$('[rel=error]').addClass ('invalid');

	// Tipsy errors
	$('[rel=error]').tipsy ({
		gravity: $.fn.tipsy.autoNS,
		fade: false,
		html: true
	});

	// Tipsy
	$('[rel=tooltip]').tipsy ({
		gravity: $.fn.tipsy.autoNS,
		fade: false,
		html: true
	});
	
	// Facebox
	$('[rel=Box]').live ('click', function (objEvent) {
		// Set
		$.facebox ({
			div: $(this).attr ('href')
		});

		// Prevent
		objEvent.preventDefault ();
	})

	// Check all checkboxes
	$('.check-all').click (function (){
		// Set
		$(this).parents('form')
		.find('input:checkbox')
		.attr('checked', $(this).is(':checked'));
	});

	// IE7 doesn't support :disabled
	$('.ie7').find (':disabled').addClass ('disabled');

	// Dropdown
	$('#main-nav li ul').hide();
	$('#main-nav li.current a').parent().find('ul').fadeToggle ('normal');
	$('#main-nav li a').click(
		function () {
			// Set
			$(this).parent().siblings().find('ul').fadeOut ('normal');
			$(this).parent().find('ul').fadeToggle ('normal');

			// Return
			return false;
		}
	);

	// Drowdown disabled
	$('#main-nav li a.no-submenu').click(
		function () {
			// Go
			window.location.href=(this.href);

			// Return
			return false;
		}
	);

	// Widget Close Button
	$('.close-widget').click(
		function () {
			$(this).parent ().fadeTo (350, 0, function () {$(this).slideUp (600);});
			return false;
		}
	);

	// Image actions
	$('.image-frame').hover(
		function () {$(this).find('.image-actions').css ('display', 'none').fadeIn ('fast').css ('display', 'block');},
		function () {$(this).find('.image-actions').fadeOut (100);}
	);

	$('.sidetab').hide ();
	$('.default-sidetab').show ();
	$('.sidetab-switch a.default-sidetab').addClass ('current');

	$('.sidetab-switch a').click (
		function() {
			// Var
			var sidetab = $(this).attr ('href');

			// Find
			$(this).parent ().siblings ().find ('a').removeClass ('current');
			$(this).addClass ('current');
			$(sidetab).siblings ('.sidetab').hide ();
			$(sidetab).show ();

			// Return
			return false;
		}
	);

	// Minimize
	$('article header h2').css ({ "cursor":"s-resize" });
	$('article header h2').click (
		function () {
			$(this).parent().find ('nav').toggle ();
			$(this).parent().parent ().find ('section, footer').toggle ();
		}
	);

	// Catch
	$('textarea.RA_Form_Input_RA_Textarea').focus (function (objEvent) {
		// Expose
		$.mask.close ();

		// Append
		$(this).after ('<div class="RA_Textarea_Confirmation">You have focused an WYSIWYG textarea. Editor is going to ' +
		'initialize. Is it OK with you? Note that the editor is going to grow to the whole screen widht and height. If ' +
		'you desire to leave this editing mode, uncheck the fullscreen button on the editor toolbar! Enjoy and thanks!');

		// Go
		$('div.RA_Textarea_Confirmation').dialog ({
			resizable: false,
			width: 600,
			modal: true,
			buttons: {
				"Ok": function () {
					tinyMCEAdministrationSetup ($('textarea.RA_Form_Input_RA_Textarea').attr ('id'));
					$(this).dialog ("destroy");
				},
				"Not Ok": function () {
					$(this).dialog ("destroy");
				}
			}
		});
	});

	// Append
	$('legend').append ('&nbsp;|&nbsp;<abbr class="information" title="Information!">?!</abbr>');

	// Information
	$('abbr.information').live ('click', function (objEvent) {
		$(this).parent ('legend')
		.parent ('fieldset')
		.find ('[information]')
		.each (function (objI) {
			$(this).after ('<div title="Information" class="div_Hidden div_Info" id="infoDialog_' +
			$(this).attr ('id') + '">' +
			$(this).attr ('information') + '</div>');

			// Go
			$.jGrowl ($(this).attr ('information'), {
				sticky: true,
				header: 'Information',
				position: 'bottom-right'
			});
		});
	});

	// Quick Editing
	$('tr').live ('dblclick', function (objEvent) {
		// Go
		if (typeof ($(this)
		.find ('td')
		.find ('button.edit')
		.attr ('href')) != 'undefined') {
			// Redirect
			window.location = $(this)
			.find ('td')
			.find ('button.edit')
			.attr ('href');
		}
	});

	// Watch out for keys
	$('tr').live ('mouseup', function (objEvent) {
		// Check
		if (objEvent.which == 2) {
			$(this)
			.find ('td')
			.find ('button.erase')
			.trigger ('click');
		}
	});

	// Hide
	$('input[type=submit]').parent ().addClass ('Invisible');

	// Memorize
	$.objectDataStorage = {};

	// Export
	$('.exportAsCSV').live ('click', function (objEvent) {
		// Roar
		$.jGrowl ("Exporting started! Depending on the number " +
		"of items you wish to export, this make take from a few " +
		"seconds to a few minutes. For 1000 items, it usually " +
		"takes 5 seconds to export. More, takes longer!", {
			position: 'bottom-right',
			header: 'Wait for it!'
		});

		// Post
		$.post (window.location, {'oExport':
			// JSONify
			$.toJSON ($.objectDataStorage
			.oTable.fnGetData ())
		}, function (objData) {
			// Roar
			$.jGrowl ('Exporting finished! A file will be downloaded ' +
			'to your browser. Save it or open it in your favourite spreadsheet editor!', {
				position: 'bottom-right',
				header: 'Finished!'
			});

			// Go
			window.location = objData;
		});

		// Prevent
		objEvent.preventDefault ();
	});

	// Focus
	$('fieldset div input:not([type=submit])').live ('focus',
	function (objEvent) {
		// Expose
		$(this).parents ('fieldset').expose ({
			loadSpeed: 'fast',
			closeSpeed: 2
		});
	});

	// Focus
	$('input.invalid').each (function (objI) {
		// Check
		if (objI == 0) {
			// Expose
			$(this).parents ('fieldset').expose ({
				loadSpeed: 'fast',
				closeSpeed: 2
			});
		}
	});

	// Hide
	$('li.toggle a').click (function (objEvent) {
		// Go
		if ($('body section[role=navigation]').is (':visible')) {
			// Fade
			$('body section[role=navigation]').fadeOut (function (objFadeEvent) {
				// Hide
				$('body section[role=main]').css ('margin-left', '0px');

				// Ajax
				$.get ($('li.toggle a').attr ('href'));
			});
		} else {
			// Fade
			$('body section[role=main]').css ('margin-left', '271px');
			$('body section[role=navigation]').fadeIn ();

			// Ajax
			$.get ($('li.toggle a').attr ('href'));
		}

		// Prevent
		objEvent.preventDefault ();
	});

	// Breadcrumb
	$('#unique_Item_Breadcrumb_Link_Data').hide ();
	
	// Set
	if ($('#unique_Item_Breadcrumb_Link_Data').html () != null) {
		// Set
		$('#unique_Item_Breadcrumb_Link')
		.html ($('#unique_Item_Breadcrumb_Link_Data')
		.html ().substr (0, 70) + " ...");
	}

	// Per input filtering
	$('table').each (function (objI, objTDom) {
		// Set
		$(objTDom).find ('tfoot').find ('input').each (function (objIT, objDom) {
			// Set
			$(objDom).keyup (function () {
				// Set
				$(this).parents ('tfoot').parents ('table')
				.dataTable ().fnFilter ($(this).val (), $('tfoot input',
				$(this).parents ('tfoot').parents ('table')).index (this));
			});
		})
	});

	// Set
	$('.exportAsPrint').click (function (objEvent) {
		// Append
		$('body > :not(.dataTables_wrapper table)').hide ();
		$('.dataTables_wrapper table').appendTo ('body');

		// Hide
		$('th:last-child').hide ();
		$('td:last-child').hide ();
		$('tfoot').hide ();

		// Erase
		$('body > *:hidden').remove ();

		// Set
		$('table').css ('margin', '30px');
		$('table').css ('width', '96%');

		// Prevent
		objEvent.preventDefault ();

		// Message
		$.jGrowl ('You are currently in "Print" mode. All other elements have been hidden. To show them again, press the ' +
		'ESC (Escape) key on your keyboard. To print this page, use the menu on your browser to start the printing!', {
			sticky: true,
			header: 'Information',
			position: 'bottom-right'
		});

		$(document).keydown (function (objKeyDownEvent) {
			// Check
			if (objKeyDownEvent.keyCode == 27) {
				// Prevent
				objKeyDownEvent.preventDefault ();

				// Refresh
				window.location = window.location;

			}
		});
	});

	// Dates
	$('#RA_Form_Input_HiddenDate_AutoUpdate').datepicker ({
		numberOfMonths: 3,
		showButtonPanel: false,
		showAnim: "fadeIn",
		dateFormat: "dd/mm/yy",
		defaultDate: +7,
		firstDay: 1,
		beforeShow: function (input, inst) {
			// Date
			objDate = new Date;
			objDate.setTime (parseInt ($('#RA_Form_Input_published').val ()) * 1000);

			// Update
			$('#RA_Form_Input_HiddenDate_AutoUpdate').datepicker ('setDate', objDate);
		},
		onSelect: function (dateText, inst) {
	        $('#RA_Form_Input_published').val ($.datepicker.formatDate ('@',
    		$(this).datepicker ('getDate')) / 1000);
	    }
	});

	// Check exists
	if ($('#RA_Form_Input_published').length != 0) {
		// Check
		if ($('#RA_Form_Input_published')
		.val ().length == 0) {
			// Now
			objNowDate = new Date;

			// Set
			$('#RA_Form_Input_published')
			.val (objNowDate.getTime () / 1000);

			// Update
			$('#RA_Form_Input_HiddenDate_AutoUpdate')
			.datepicker ('setDate', objNowDate);
		} else {
			// Date
			objDate = new Date;
			objDate.setTime (parseInt ($('#RA_Form_Input_published').val ()) * 1000);

			// Update
			$('#RA_Form_Input_HiddenDate_AutoUpdate')
			.datepicker ('setDate', objDate);
		}
	}

	// Code
	$('.RA_Form_Input_PHP_Code_Editor').each (function (objI) {
		// Set
		var myCodeMirror = CodeMirror.fromTextArea (this, {
			mode: 'php',
			theme: 'default',
			indentUnit: 4,
			lineNumbers: true,
			enterMode: 'keep',
			undoDepth: 1000
		});
	});

	// Code
	$('.RA_Form_Input_CSS_Code_Editor').each (function (objI) {
		// Set
		var myCodeMirror = CodeMirror.fromTextArea (this, {
			mode: 'css',
			theme: 'default',
			indentUnit: 4,
			lineNumbers: true,
			enterMode: 'keep',
			undoDepth: 1000
		});
	});

	// Code
	$('.RA_Form_Input_JSS_Code_Editor').each (function (objI) {
		// Set
		var myCodeMirror = CodeMirror.fromTextArea (this, {
			mode: 'javascript',
			theme: 'default',
			indentUnit: 4,
			lineNumbers: true,
			enterMode: 'keep',
			undoDepth: 1000
		});
	});

	// Make HREF inputs new window
	$('#RA_Form_Input_href').click (function (objEvent) {
		window.open ($(this).attr ('value'));
		$(this).blur ();
	});
	
	// Sizing
	$(document).everyTime ("200ms", "Navigation Size", function () {
		// Check
		if ($('section[role=main]').height () >
		$('section[role=navigation]').height ()) {
			// Set
			$('section[role=navigation]')
			.height ($('section[role=main]')
			.height ());
		}
    });	
});
