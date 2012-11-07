function tinyMCEAdministrationSetup (objId) {
  // Requirements
  var objRequirements = ["safari",
                         "spellchecker",
                         "pagebreak",
                         "style",
                         "layer",
                         "table",
                         "save",
                         "advhr",
                         "advimage",
                         "advlink",
                         "emotions",
                         "iespell",
                         "insertdatetime",
                         "preview",
                         "media",
                         "searchreplace",
                         "print",
                         "contextmenu",
                         "paste",
                         "directionality",
                         "fullscreen",
                         "noneditable",
                         "visualchars",
                         "nonbreaking",
                         "xhtmlxtras",
                         "template"];

  // #1
  var objRowOne		= ["styleselect",
                      "formatselect",
                      "fontselect",
                      "fontsizeselect","|",
                      "bold",
                      "italic",
                      "underline",
                      "strikethrough", "|",
                      "justifyleft",
                      "justifycenter",
                      "justifyright",
                      "justifyfull","|",
                      "cut",
                      "paste",
                      "pastetext",
                      "pasteword",
                      "search",
                      "replace"];

  // #2
  var objRowTwo		= ["bullist",
                      "numlist","|",
                      "undo",
                      "redo","|",
                      "sub",
                      "sup",
                      "outdent",
                      "indent",
                      "blockquote","|",
                      "link",
                      "unlink",
                      "anchor",
                      "image",
                      "cleanup",
                      "code","|",
                      "removeformat",
                      "visualaid",
                      "charmap",
                      "iespell",
                      "media",
                      "advhr","|",
                      "ltr",
                      "rtl"];

  // #3
  var objRowTre		= ["tablecontrols","|",
                      "styleprops","|",
                      "cite",
                      "abbr",
                      "acronym","|",
                      "del",
                      "ins",
                      "attribs","|",
                      "fullscreen",
                      "save"];

  // Set
  tinyMCE.init ({
      mode: 'exact',
      elements: objId,
      auto_focus: objId,
      theme: 'advanced',
      skin: 'o2k7',
      skin_variant : 'silver',
      height: 300,
      plugins : objRequirements.join (','),
      theme_advanced_buttons1 : objRowOne.join (','),
      theme_advanced_buttons2 : objRowTwo.join (','),
      theme_advanced_buttons3 : objRowTre.join (','),
      theme_advanced_toolbar_location : 'bottom',
      theme_advanced_toolbar_align : 'left',
      theme_advanced_statusbar_location : 'none',
      theme_advanced_resizing : false,
      cleanup_on_startup : true,
      convert_newlines_to_brs : false,
      fix_list_elements : true,
      fix_table_elements : true,
      fix_nesting : true,
      convert_fonts_to_spans : true,
      constrain_menus : true,
      relative_urls : false,
      remove_script_host : false,
      dialog_type : 'modal',
      strict_loading_mode : true,
      apply_source_formatting : false,
      gecko_spellcheck : true,

      // Extern
      external_image_list_url : window.location + '/Editor/Image',
      media_external_list_url : window.location + '/Editor/Media',
      external_link_list_url	: window.location + '/Editor/Link',
      content_css: '/frm/jss/editor/themes/advanced/content.css',

      // On stuff
      oninit : function (ed) {
          // Unused: tinyMCE.execCommand ('mceFullScreen', true, 'ed.id');
      }
  });
}

function tinyMCEFrontendSetup (objId) {
    // Requirements
    var objRequirements = ["safari",
                           "spellchecker",
                           "pagebreak",
                           "style",
                           "layer",
                           "table",
                           "save",
                           "advhr",
                           "advimage",
                           "advlink",
                           "emotions",
                           "iespell",
                           "inlinepopups",
                           "insertdatetime",
                           "preview",
                           "media",
                           "searchreplace",
                           "print",
                           "contextmenu",
                           "paste",
                           "directionality",
                           "fullscreen",
                           "noneditable",
                           "visualchars",
                           "nonbreaking",
                           "xhtmlxtras",
                           "template"];

    // #1
    var objRowOne		= ["bold",
                        "italic",
                        "underline",
                        "strikethrough", "|",
                        "justifyleft",
                        "justifycenter",
                        "justifyright",
                        "justifyfull","|",
                        "cut",
                        "paste",
                        "pastetext",
                        "pasteword",
                        "search",
                        "replace",
                        "bullist",
                        "numlist","|",
                        "undo",
                        "redo"];

    // #2
    var objRowTwo		= [];

    // #3
    var objRowTre		= [];

    // Set
    tinyMCE.init ({
        mode: 'exact',
        elements: objId,
        auto_focus: objId,
        theme: 'advanced',
        skin: 'o2k7',
        skin_variant : "silver",
        height: 300,
        plugins : objRequirements.join (','),
        theme_advanced_buttons1 : objRowOne.join (','),
        theme_advanced_buttons2 : objRowTwo.join (','),
        theme_advanced_buttons3 : objRowTre.join (','),
        theme_advanced_toolbar_location : 'bottom',
        theme_advanced_toolbar_align : 'left',
        theme_advanced_statusbar_location : 'none',
        theme_advanced_resizing : false,
        cleanup_on_startup : true,
        convert_newlines_to_brs : true,
        fix_list_elements : true,
        fix_table_elements : true,
        fix_nesting : true,
        convert_fonts_to_spans : true,
        constrain_menus : true,
        relative_urls : false,
        remove_script_host : false,
        dialog_type : 'modal',
        strict_loading_mode : true,
        apply_source_formatting : true,
        gecko_spellcheck : true,
        valid_elements : 'a[href|target=_blank],strong,b,br,p,ul,li,ol,em,i,u'
  });
}
