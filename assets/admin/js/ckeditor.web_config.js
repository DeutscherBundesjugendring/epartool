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

  //config.contentsCss = 'vendor/ckeditor/plugins/mjAccordion/mjAccordion.css';
  config.bootstrapCollapse_managePopupContent = false;
};
