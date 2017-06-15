;(function ($) {

  $.fn.pageLeaveConfirmation = function () {
    var confirmationEnabled = false;
    var $inputs = $(this).find('textarea');
    var formSaved = false;
    var enableConfirmation = function () {
      if (!confirmationEnabled) {
        $(window).bind('beforeunload', function () {
          confirmationEnabled = true;
          if (formSaved === false) {
            return jsTranslations['message_contributions_save_error'];
          }
        });
      }
    };

    $inputs.each(function () {
       if ($(this).val()) {
         enableConfirmation();
       }
    });

    this.each(function () {
       $(this).find('button[type=submit]').on('click', function () {
         formSaved = true;
       })
    });

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
