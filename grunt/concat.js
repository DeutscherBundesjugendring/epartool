'use strict';

module.exports = {

  options: {
    separator: ';',
    sourceMap: true
  },
  front: {
    src: [
      '<%= paths.bower %>/jquery/dist/jquery.js',
      '<%= paths.bower %>/pwstrength-bootstrap/dist/pwstrength-bootstrap-1.1.5.js',
      '<%= paths.bower %>/bootstrap/js/alert.js',
      '<%= paths.bower %>/bootstrap/js/collapse.js',
      '<%= paths.bower %>/bootstrap/js/dropdown.js',
      '<%= paths.bower %>/bootstrap/js/modal.js',
      '<%= paths.bower %>/bootstrap/js/transition.js',
      '<%= paths.bower %>/bootstrap-toggle/js/bootstrap-toggle.js',
      '<%= paths.npm %>/leaflet/dist/leaflet.js',
      '<%= paths.npm %>/leaflet.markercluster/dist/leaflet.markercluster.js',
      '<%= paths.src.front %>/js/FollowUp.js',
      '<%= paths.src.front %>/js/PageLeaveConfirmation.js',
      '<%= paths.src.front %>/js/SocialPopup.js',
      '<%= paths.src.front %>/js/front.js',
      '<%= paths.src.front %>/js/wise-leaflet-pip.js'
    ],
    dest: '<%= paths.dist %>/js/<%= pkg.name %>.js'
  },
  admin: {
    src: [
      '<%= paths.bower %>/jquery/dist/jquery.js',
      '<%= paths.bower %>/moment/min/moment-with-locales.min.js',
      '<%= paths.bower %>/select2/select2.min.js',
      '<%= paths.bower %>/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js',
      '<%= paths.bower %>/bootstrap/js/alert.js',
      '<%= paths.bower %>/bootstrap/js/collapse.js',
      '<%= paths.bower %>/bootstrap/js/dropdown.js',
      '<%= paths.bower %>/bootstrap/js/modal.js',
      '<%= paths.bower %>/bootstrap/js/tooltip.js',
      '<%= paths.bower %>/bootstrap/js/transition.js',
      '<%= paths.bower %>/bootstrap-colorpicker/dist/js/bootstrap-colorpicker.min.js',
      '<%= paths.bower %>/jquery.ui/ui/jquery.ui.core.js',
      '<%= paths.bower %>/jquery.ui/ui/jquery.ui.widget.js',
      '<%= paths.bower %>/jquery.ui/ui/jquery.ui.mouse.js',
      '<%= paths.bower %>/jquery.ui/ui/jquery.ui.sortable.js',
      '<%= paths.bower %>/bootstrap.ui/src/js/ckeditor-loader.js',
      '<%= paths.bower %>/bootstrap.ui/src/js/confirmation.js',
      '<%= paths.bower %>/bootstrap.ui/src/js/datetimepicker-loader.js',
      '<%= paths.bower %>/bootstrap.ui/src/js/disable.js',
      '<%= paths.bower %>/bootstrap.ui/src/js/filterable.js',
      '<%= paths.bower %>/bootstrap.ui/src/js/select2-loader.js',
      '<%= paths.bower %>/bootstrap.ui/src/js/sortable-table.js',
      '<%= paths.npm %>/leaflet/dist/leaflet.js',
      '<%= paths.npm %>/leaflet.markercluster/dist/leaflet.markercluster.js',
      '<%= paths.src.admin %>/js/facebook.embed_video.js',
      '<%= paths.src.admin %>/js/colorpicker.js',
      '<%= paths.src.admin %>/js/admin.js',
      '<%= paths.src.admin %>/js/wise-leaflet-pip.js'
    ],
    dest: '<%= paths.dist %>/js/admin.js'
  }

};
