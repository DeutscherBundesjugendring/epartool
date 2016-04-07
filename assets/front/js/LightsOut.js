;(function ($, document, window) {

  $.fn.lightsOut = function () {
    var $overlay;
    var storageName = 'dbjr-voting-lights-out';
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
    }

    if (window.sessionStorage.getItem(storageName) === 'true') {
      addOverlay();
    }

    $(this).each(function () {
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
