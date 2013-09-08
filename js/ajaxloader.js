;
(function($, window, document) {


    function AjaxLoader() {
        var _instance = this;
        init();

        function init() {

            initEventListener();
        }

        function initEventListener() {
            $(".movefowup").live('click', _onClickMovefowup);
           
        }

        
        function _onClickMovefowup() {

            loadContent($(this).attr("href"));
            return false;

        }



        function loadContent(uri, method, postdata) {

            var method = method || 'GET';
            var postdata = postdata || null;
            var patt, newContent;






            $.ajax({
                type: method,
                url: uri,
                data: postdata,
                success: function(data) {

                    patt = /<!--uniqueFowupsSTART-->([\n\r\w\W.]*?)<!--uniqueFowupsEND-->/gi;
                    newContent = data.match(patt).toString();

                    $('#ajaxcontent').html("");
                    $('#ajaxcontent').html(newContent);


                }
            });

        }


    }

  

    window.AjaxLoader = AjaxLoader;

}(jQuery, window, document));


$(document).ready(function(){
    
   var _ajaxloader = new AjaxLoader();
    
});