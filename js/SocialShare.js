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
           
        }
        
        function getFacebookBtn(uri) {
            
            var fb_code = '<iframe src="http://www.facebook.com/plugins/like.php?locale=' + options.facebook.language + '&amp;href=' + uri + '&amp;send=false&amp;layout=button_count&amp;width=120&amp;show_faces=false&amp;action=' + options.facebook.action + '&amp;colorscheme=light&amp;font&amp;height=21" scrolling="no" frameborder="0" style="border:none;" allowTransparency="true"></iframe>';
            
            return fb_code;
        }
        function getTwitterBtn(uri) {
                       
            var enc_uri = encodeURIComponent(uri);
            var text = getTweetText();
            var twitter_code = '<iframe allowtransparency="true" frameborder="0" scrolling="no" src="http://platform.twitter.com/widgets/tweet_button.html?url=' + enc_uri + '&amp;counturl=' + enc_uri + '&amp;text=' + text + '&amp;count=horizontal&amp;lang=' + options.twitter.language + '" style="width:130px; height:25px;"></iframe>';
            
            return twitter_code;
        }
        function getGPlusBtn(uri) {           
                      
           var gplus_code = '<div class="g-plusone" data-size="medium" data-href="' + uri + '"></div><script type="text/javascript">window.___gcfg = {lang: "' + options.gplus.language + '"}; (function() { var po = document.createElement("script"); po.type = "text/javascript"; po.async = true; po.src = "https://apis.google.com/js/plusone.js"; var s = document.getElementsByTagName("script")[0]; s.parentNode.insertBefore(po, s); })(); </script>';
                      
           return gplus_code;
        }
        
        function toggleButtons() {
            if (!$(this).hasClass("active")) {
                
                $(this).addClass("active");
                var uri = getURI();
                //uri = 'http://tool-dev.ichmache-politik.de/input/index/kid/17';
                var fb = getFacebookBtn(uri);
                var tw = getTwitterBtn(uri);
                var gp = getGPlusBtn(uri);

                $("#sharebtn-holder").append('<div class="social-share-btn">'+fb+'</div>');
                $("#sharebtn-holder").append('<div class="social-share-btn">'+tw+'</div>');
                $("#sharebtn-holder").append('<div class="social-share-btn">'+gp+'</div>');
                
            } else {
                $(this).removeClass("active");
                 $("#sharebtn-holder").html('');
            }
            
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

