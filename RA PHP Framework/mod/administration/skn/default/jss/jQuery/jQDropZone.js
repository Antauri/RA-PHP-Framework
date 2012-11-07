$(document).ready (function () {
  // Outfocus
  $('.dropZoneInput').focus (function () {
        $(this).blur ();
    });

  // Set
    $('.dropZoneInput').live ('drop', function (objEvent) {
      // Stop
      objEvent.stopPropagation ();
      objEvent.preventDefault ();

      // Rename
      $(this).attr ('name', $(this).attr ('name')
    .replace ('RA_dropZoneInactive_', ''));

      // List
        var files = objEvent.dataTransfer.files;

        // For each
        for (var i = 0, f; f = files[i]; i++) {
          // Process
      if (f.type.match ('image.*')) {
        // Reader
        var objReader = new FileReader ();

        // Set
        objReader.onload = (function (theFile) {
              return function (objReaderEvent) {
                $.jGrowl ('<img class="dropZoneThumb" src="' + objReaderEvent.target.result +
                '" title="' + theFile.name + '" align="right" />You have dropped an image. In the ' +
                'mean time, transfer of this file has already begun. Wait a moment for it to finish! Thanks!', {
                  sticky: true,
              header: 'Dropped (image): ' + theFile.name.substr (0, 20) + ' ...',
              position: 'bottom-right'
            });
                };
            })(f);

        // Read
        objReader.readAsDataURL (f);
      }

      if (f.type.match ('.*ogg')) {
        // Reader
        var objReader = new FileReader ();

        // Set
        objReader.onload = (function (theFile) {
              return function (objReaderEvent) {
                $.jGrowl ('You have dropped a video. In the mean time, transfer of this file has already begun. ' +
                'Wait for it to finish! Thanks! <div class="videoThumb"><video controls="controls"><source src="' +
                objReaderEvent.target.result + '" type="application/ogg" /></video></div>', {
                  sticky: true,
              header: 'Dropped (video): ' + theFile.name.substr (0, 20) + ' ...',
              position: 'bottom-right'
            });
                };
            })(f);

        // Read
        objReader.readAsDataURL (f);
      }
        }

        // For each
        var xhrTransfers = function (files, objSource) {

          if (typeof xhrTransfers.counter == 'undefined' ) {
                // Initialize
            xhrTransfers.counter = 0;
            i = xhrTransfers.counter;
            } else {
              // Set
              xhrTransfers.counter++;
              i = xhrTransfers.counter;
            }

          if (i == files.length) {
            // Return
            return true;
          }

          // Get
          f = files[i];

      // Check
      if (i == 0) {
        // Set
        $(objSource).val ($(objSource).val ().replace ('Drag items from your desktop over this input, to submit this form!',
        'Transfered: No: 1 - 0% # You\'ve dragged: ' + f.name + ' having size of ' + f.size + ' bytes, type: ' + f.type));
      }

      // Data
      var objData = new FormData;

      // Set
      $('.dropZoneInput').parents ('form')
      .find ('input, textarea, radio, checkbox, select')
      .each (function () {
        // Check
        if ($(this).attr ('class') != 'dropZoneInput dropZoneActive') {
          // Set
          /* if ($(this).attr ('name') == 'title') {
            // Append name of file
            objData.append ($(this).attr ('name'),
            '# Id: ' + f.name + ' # ' +
            f.size + ' bytes # ' + f.type +
            ' # Name: '+ $(this).val ());
          } else {
            // Append defaults
            objData.append ($(this).attr ('name'), $(this).val ());
          } */

          // Set
          objData.append ($(this).attr ('name'), $(this).val ());
        } else {
          // Set
          objData.append ($(this).attr ('name'), f);
        }
      });

      // Specific
      objData.append ('RA_dropZoneSet', '1');
      objData.append ('RA_dropZoneNum', files.length);
      objData.append ('RA_dropZoneCur', i);

      // XHR
      var xhr = new XMLHttpRequest ();

      // Event
      xhr.upload.addEventListener ('progress', function (objEvent) {
        // Check
        if (objEvent.lengthComputable) {
          // Set
          var percentComplete = Math.round ((objEvent.loaded * 100) / objEvent.total);
          $('.dropZoneInput').val ($('.dropZoneInput').val ()
          .replace (/Transfered:(.*)#/, 'Transfered: No: ' + (i + 1) + ' - ' +
          percentComplete + '% (' + objEvent.loaded + ' out of ' + objEvent.total + ' bytes) #')
          .replace (/dragged: (.*) having/, 'dragged: ' + f.name + ' having')
          .replace (/size of (.*) bytes/, 'size of ' + f.size + ' bytes')
          .replace (/type: (.*)$/, 'type: ' + f.type));
        }
      }, false);

      // State
      xhr.onreadystatechange = function (aEvt) {
        // Check
        if (xhr.readyState == 4) {
          // Status
          if (xhr.status == 200) {
            // Array
            var objExpString = xhr.responseText.split (' :: ');

            // Response
            if (objExpString[0] == 'RA_dropZoneInput_Ok') {
              // Redirect
              window.location = objExpString[1];
            } else if (objExpString [0] == 'RA_dropZoneInput_Continue') {
              // Do nothing
              xhrTransfers (files, objSource);
            } else {
              // Error
              $.jGrowl ('Wrong file type or unexpected transfer error! You\'re better advised to try again or ' +
              'report this as a bug if you think something is wrong! Thanks!', {
                sticky: true,
                header: 'Warning on: ' + files[i].name.substr (0, 20) + ' ...',
                position: 'bottom-right'
              });

              // Continue
              xhrTransfers (files, objSource);
            }
          }
        }
      };

      // Send
      xhr.open ('POST', window.location);
      xhr.send (objData);
        };

        // Recurse
        xhrTransfers (files, this);
    });

    $('.dropZoneInput').live ('dragover', function (objEvent) {
      // Stop
      objEvent.stopPropagation ();
      objEvent.preventDefault ();
    });

    // Push attributes
    jQuery.event.props.push ('dataTransfer');

    // Wrap
    $('input[type=file]').each (function (objI) {
      $.jGrowl ('First, enter required form fields. When ready, choose files from your desktop and drop them on the proper input. ' +
    'For each item you chose to drop, an entry is going to be created, with the filename prepended to the title!', {
      sticky: true,
      header: 'What you must know',
      position: 'bottom-right'
    });

      $.jGrowl ('Some files may fail to upload, due to content type restrictions. Don\'t bother, as the process continues with files ' +
    'left in the queue. After it finishes, you can debug those files individidually to see why they did not upload!', {
      sticky: true,
      header: 'In case of errors',
      position: 'bottom-right'
    });

      // Add
      $(this).after ('<input type="text" class="dropZoneInput" id="uniqueDropZoneId_' + objI + '" name="' +
    $(this).attr ('name') + '" value="Drag items from your desktop over this input, to submit this form!" />');

      // Disabled
      if ($(this).prop ('disabled') == true) {
        // Set
        $('#uniqueDropZoneId_' + objI)
        .attr ('name', 'RA_dropZoneInactive_' +
      $('#uniqueDropZoneId_' + objI).attr ('name'));
      }

      // Rd-only
      $('#uniqueDropZoneId_' + objI).prop ('readonly', true);

      // Focus
      $('#uniqueDropZoneId_' + objI).live ('focus', function (objEvent) {
        // Defocus
        $(this).blur ();
        objEvent.preventDefault ();
      });

    // Add
    $('.dropZoneInput').addClass ('dropZoneActive');

      // Remove
      $(this).remove ();
    });
});
