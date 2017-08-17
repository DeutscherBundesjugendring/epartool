;(function ($) {

  $.fn.socialPopup = function (e) {
    e.preventDefault();

    var left = (screen.width / 2) - 313;
    var top = (screen.height / 2) - 218;
    var strTitle = ((typeof this.attr('title') !== 'undefined') ? this.attr('title') : 'Social Share');
    var strParam = 'width=626, height=436, top=' + top + ', left=' + left;
    window.open(this.attr('href'), strTitle, strParam).focus();
  }

}(jQuery));
