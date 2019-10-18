'use strict';

module.exports = {

  options: {
    separator: ';',
    sourceMap: true
  },
  front: {
    src: [
      '<%= paths.npm %>/jquery/dist/jquery.js',
      '<%= paths.npm %>/pwstrength-bootstrap/dist/pwstrength-bootstrap.js',
      '<%= paths.npm %>/bootstrap/js/alert.js',
      '<%= paths.npm %>/bootstrap/js/collapse.js',
      '<%= paths.npm %>/bootstrap/js/dropdown.js',
      '<%= paths.npm %>/bootstrap/js/modal.js',
      '<%= paths.npm %>/bootstrap/js/transition.js',
      '<%= paths.npm %>/bootstrap-toggle/js/bootstrap-toggle.js',
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
      '<%= paths.npm %>/jquery/dist/jquery.js',
      '<%= paths.npm %>/moment/min/moment-with-locales.min.js',
      '<%= paths.npm %>/select2/select2.js',
      '<%= paths.npm %>/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js',
      '<%= paths.npm %>/bootstrap/js/alert.js',
      '<%= paths.npm %>/bootstrap/js/collapse.js',
      '<%= paths.npm %>/bootstrap/js/dropdown.js',
      '<%= paths.npm %>/bootstrap/js/modal.js',
      '<%= paths.npm %>/bootstrap/js/tooltip.js',
      '<%= paths.npm %>/bootstrap/js/transition.js',
      '<%= paths.npm %>/bootstrap-colorpicker/dist/js/bootstrap-colorpicker.min.js',
      '<%= paths.npm %>/jquery-ui/ui/disable-selection.js',
      '<%= paths.npm %>/jquery-ui/ui/data.js',
      '<%= paths.npm %>/jquery-ui/ui/scroll-parent.js',
      '<%= paths.npm %>/jquery-ui/ui/version.js',
      '<%= paths.npm %>/jquery-ui/ui/widget.js',
      '<%= paths.npm %>/jquery-ui/ui/widgets/mouse.js',
      '<%= paths.npm %>/jquery-ui/ui/widgets/sortable.js',
      '<%= paths.npm %>/bootstrap-ui/src/js/ckeditor-loader.js',
      '<%= paths.npm %>/bootstrap-ui/src/js/confirmation.js',
      '<%= paths.npm %>/bootstrap-ui/src/js/datetimepicker-loader.js',
      '<%= paths.npm %>/bootstrap-ui/src/js/disable.js',
      '<%= paths.npm %>/bootstrap-ui/src/js/filterable.js',
      '<%= paths.npm %>/bootstrap-ui/src/js/select2-loader.js',
      '<%= paths.npm %>/bootstrap-ui/src/js/sortable-table.js',
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
