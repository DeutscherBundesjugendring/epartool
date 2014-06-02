/**
 *  @author <dinnbier@digitalroyal.de>
 *
 */
;
(function($, window, document) {

    function SocialShare() {

        // use _instance insted of this
        var _instance = this;

        var options = {

            facebook: {

                language: 'de_DE',
                action: 'recommend'
            },
            twitter: {

                language: 'de'
            },

            gplus: {

                language: 'de'
            }

        };


        _init();

        // private methods

        function _init() {
            _initEventlistener();
        }

        function _initEventlistener() {
            $("a.share").on('click', toggleButtons);
            $(document).on('click', 'a#facebookshare', function() {
                var enc_uri = $(this).attr('href');
                var popupurl = 'https://www.facebook.com/sharer/sharer.php?u='+enc_uri;
                window.open(popupurl,'facebook-share-dialog', 'width=626,height=436');
                return false;
            });
        }

        function getFacebookBtn(uri) {
            var fb_code = '<a href="'+encodeURIComponent(uri)+'" id="facebookshare"><img src="' + baseUrl + '/images/facebook_share_button.png" /></a>';

            return fb_code;
        }
        function getTwitterBtn(uri) {

            var enc_uri = encodeURIComponent(uri);
            var text = getTweetText();
            var twitter_code = '<iframe allowtransparency="true" frameborder="0" scrolling="no" src="https://platform.twitter.com/widgets/tweet_button.html?url=' + enc_uri + '&amp;counturl=' + enc_uri + '&amp;text=' + text + '&amp;count=horizontal&amp;count=none&amp;lang=' + options.twitter.language + '" style="width:130px; height:25px;"></iframe>';

            return twitter_code;
        }
        function getGPlusBtn(uri) {
           var gplus_code = '<div class="g-plusone" data-annotation="none" data-size="tall" data-href="' + uri + '"></div><script type="text/javascript">window.___gcfg = {lang: "' + options.gplus.language + '"}; (function() { var po = document.createElement("script"); po.type = "text/javascript"; po.async = true; po.src = "https://apis.google.com/js/plusone.js"; var s = document.getElementsByTagName("script")[0]; s.parentNode.insertBefore(po, s); })(); </script>';

           return gplus_code;
        }

        function toggleButtons() {
            if (!$(this).hasClass("active")) {

                $(this).addClass("active");
                var uri = getURI();
                //Dev Uri for normal behavior behind firewalls
               // uri = 'http://tool-dev.ichmache-politik.de/input/index/kid/17';
                var fb = getFacebookBtn(uri);
                var tw = getTwitterBtn(uri);
                var gp = getGPlusBtn(uri);

                $("#sharebtn-holder").append('<div class="share-btn">'+tw+'</div>');
                $("#sharebtn-holder").append('<div class="share-btn">'+gp+'</div>');
                $("#sharebtn-holder").append('<div class="share-btn">'+fb+'</div>');

            } else {
                $(this).removeClass("active");
                 $("#sharebtn-holder").html('');
            }
            return false;


        }

        function getTweetText() {
            var title = getMeta('DC.title');
            var creator = getMeta('DC.creator');

            if (title.length > 0 && creator.length > 0) {
                title += ' - ' + creator;
            } else {
                title = $('title').text();
            }

            return encodeURIComponent(title);
        }

        function getURI() {
            var uri = document.location.href;
            uri = uri.split('#')[0];
            var canonical = $("link[rel=canonical]").attr("href");

            if (canonical && canonical.length > 0) {
                if (canonical.indexOf("http") < 0) {
                    canonical = document.location.protocol + "//" + document.location.host + canonical;
                }
                uri = canonical;
            }


            return uri;
        }

        function getMeta(name) {
            var metaContent = $('meta[name="' + name + '"]').attr('content');
            return metaContent || '';
        }

    }

    window.SocialShare = SocialShare;

}(jQuery, window, document));

$(document).ready(function(){

   var _socialsharer = new SocialShare();

});

