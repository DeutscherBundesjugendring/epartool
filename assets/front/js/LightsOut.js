;(function ($, document, window) {

  /**
   * Displays "lighs out" overlay.
   * If the user visits another page then the overlay is displayed only if the trigger button is present.
   * @param storageName The key to be used in the session storage
   */
  $.fn.lightsOut = function (storageName) {
    var $overlay;
    var removeOverlay = function ($overlay) {
      $overlay.remove();
      window.sessionStorage.removeItem(storageName);
    };
    var addOverlay = function () {
      $overlay = $('<div class="lights-out">xxx</div>');
      $('body').parent().append($overlay);
      $overlay.on('click', function () {
        removeOverlay($overlay);
      })
    };

    if (window.sessionStorage.getItem(storageName) === 'true') {
      if (this.length > 0) {
        addOverlay();
      }
    }

    this.each(function () {
       $(this).on('click', function () {
         window.sessionStorage.setItem(storageName, 'true');
         addOverlay();
       })
    });

    $(document).on('keyup', function (e) {
      if (e.keyCode == 27) { //esc key
        removeOverlay($overlay);
      }
    })
  };

}(jQuery, document, window));
