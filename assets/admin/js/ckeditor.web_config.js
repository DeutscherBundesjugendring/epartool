CKEDITOR.editorConfig = function( config ) {
  config.extraPlugins = 'bootstrapCollapse,colorbutton,colordialog,div,find,flash,font,iframe,image2,selectall,smiley';
  config.allowedContent = true,
  config.toolbar = [
    {name: 'document', items: ['Source']},
    {name: 'clipboard', items: ['PasteText', 'PasteFromWord']},
    {name: 'editing', items: ['Find', 'Replace', '-', 'SelectAll']},
    {
      name: 'basicstyles',
      items: ['Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat']
    },
    {
      name: 'paragraph',
      items: ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', 'CreateDiv']
    },
    {name: 'links', items: ['Link', 'Unlink', 'Anchor']},
    {
      name: 'insert',
      items: ['Image', 'Flash', 'Table', 'HorizontalRule', 'Smiley', 'SpecialChar', 'Iframe', 'BootstrapCollapse']
    },
    {name: 'styles', items: ['Format', 'Font', 'FontSize']},
    {name: 'colors', items: ['TextColor', 'BGColor']},
    {name: 'tools', items: ['Maximize', 'ShowBlocks']},
    {name: 'about', items: ['About']}
  ];

  config.removeDialogTabs = 'link:advanced';

  config.bootstrapCollapse_managePopupContent = false;

  config.image2_alignClasses = ['align-left', 'align-center', 'align-right'];

  config.entities = false;
  config.entities_latin = false;
  config.entities_greek = false;
  config.entities_additional = false;
};
