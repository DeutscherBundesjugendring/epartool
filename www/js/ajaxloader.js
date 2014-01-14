;
(function($, window, document) {


    function AjaxLoader() {
        var _instance = this;
        init();

        function init() {

            initEventListener();
        }

        function initEventListener() {
            $(document).on('click', '.movefowup,.hlvlarrow', _reloadEditFowup);
            $(document).on('click', '.openajaxform', _onClickOpenajaxform);
            $(document).on('click', '.openajaxformnew', _onClickOpenajaxformnew);
            $(document).on('submit', '.fsnippet-form', _onSubmitFsnippetform);
        }

        function _onSubmitFsnippetform() {
            var postdata = $(this).serialize();
            var parentid = $(this).parent().attr("id");
           // var parentid = $(this).parent().data('prev') !== "undefined" ? $(this).parent().data('prev') : $(this).parent().attr("id");
            var params = {
                uri: $(this).attr("action"),
                postdata: postdata,
                success: {
                    container: $('#ajaxcontent'),
                    pattern: /<!--uniqueEditFowupSTART-->([\n\r\w\W.]*?)<!--uniqueEditFowupEND-->/gi
                },
                error: {
                    container: $('#' + parentid),
                    pattern: /<!--uniqueEditSnippetSTART-->([\n\r\w\W.]*?)<!--uniqueEditSnippetEND-->/gi
                }
            };

            loadContent(params);

            return false;

        }

        function _reloadEditFowup() {
            var params = {
                uri: $(this).attr("href"),
                container: $('#ajaxcontent'),
                pattern: /<!--uniqueEditFowupSTART-->([\n\r\w\W.]*?)<!--uniqueEditFowupEND-->/gi
            };
            loadContent(params);
            return false;

        }
        function _onClickOpenajaxformnew() {

            $(".ajaxform").html('');
            var container = $(this).next("div.ajaxform");

            var params = {
                uri: $(this).attr("href"),
                container: container,
                pattern: /<!--uniqueEditSnippetSTART-->([\n\r\w\W.]*?)<!--uniqueEditSnippetEND-->/gi
            };
            loadContent(params);
            return false;

        }
        function _onClickOpenajaxform() {

            $(".ajaxform").html('');
            var id = $(this).attr('rel');

            var params = {
                uri: $(this).attr("href"),
                container: $('#ajaxform-' + id),
                pattern: /<!--uniqueEditSnippetSTART-->([\n\r\w\W.]*?)<!--uniqueEditSnippetEND-->/gi
            };
            loadContent(params);
            return false;

        }



        function loadContent(params) {


            var uri = params.uri;

            var method = params.postdata ? 'POST' : 'GET';
            var postdata = params.postdata || null;
            var container, pattern;
            var newContent;

            $.ajax({
                type: method,
                url: uri,
                data: postdata,
                success: function(data) {

                    container = params.success && params.success.container ? params.success.container : params.container;
                    pattern = params.success && params.success.pattern ? params.success.pattern : params.pattern;

                    if (!container || !pattern) {
                        throw "No container or pattern given."
                    }

                    newContent = data.match(pattern);

                    if (newContent) {

                        newContent = newContent.toString();

                    } else {

                        return false;

                    }

                    container.html("");
                    container.html(newContent);


                },
                statusCode: {
                    422: function(data) {

                        container = params.error && params.error.container ? params.error.container : params.container;
                        pattern = params.error && params.error.pattern ? params.error.pattern : params.pattern;

                         if (!container || !pattern) {
                                    throw "No container or pattern given."
                        }

                        data = data.responseText;

                        newContent = data.match(pattern);


                        if (newContent) {

                            newContent = newContent.toString();

                        } else {

                            return false;

                        }

                        container.html("");
                        container.html(newContent);


                    }

                }

            });

        }


    }



    window.AjaxLoader = AjaxLoader;

}(jQuery, window, document));


$(document).ready(function() {

    var _ajaxloader = new AjaxLoader();

});
