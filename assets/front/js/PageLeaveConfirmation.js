;(function ($) {
  $.fn.pageLeaveConfirmation = function () {
    var $inputs = $(this).find('textarea');
    var enableConfirmation = function () {
      $(window).bind('beforeunload', function() {
        return '';
      });
    };

    $inputs.on('change.pageLeaveConfirmation', function () {
      $inputs.off('change.pageLeaveConfirmation');
      enableConfirmation();
    });

    $inputs.on('keyup.pageLeaveConfirmation', function () {
      $inputs.off('keyup.pageLeaveConfirmation');
      enableConfirmation();
    });
  }
}(jQuery));
