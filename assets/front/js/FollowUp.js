;(function($, window, document) {
    /**
     * Followup Tool
     * @param kid Konsultationsid
     * @constructor
     */

    function FollowUp(kid) {
        var _kid = kid;
        var _host = window.location.protocol + "//" + window.location.host;
        var $followup = $('#followup');

        _initEventListener();

        function _initEventListener() {
            $(document).on('click', '.js-ajaxclick', function(el) {
                el.preventDefault();
                el.stopPropagation();
                var _request = $(this).attr('href');
                var _colId = parseInt($(this).parent().parent().attr('data-id'));
                var _reset;

                if ($(this).hasClass('reset'))
                    _reset = true;

                var _obj = {'request': _request, 'colid': _colId, 'reset': _reset};

                if (!$(this).hasClass('active')) {
                    getData(_obj, addItemCallback);
                }

                $('.js-ajaxclick').removeClass('active');
                $(this).addClass('active');
            });

            $(document).on('click', '.js-voting', function(el) {
                el.preventDefault();
                el.stopPropagation();
                var _thisEl = $(this);
                var _request = $(this).attr('href');
                var _target = el.target;
                var _obj = {'request': _request, 'target': _target};

                  getData(_obj, function(data) {
                    var _amount = data.lkyea || data.lknay;
                    _thisEl.children(".js-amount").text(_amount);

                    var cls = _thisEl.hasClass("js-voting-like") ? ".js-like" : ".js-dislike";
                    var fid = _thisEl.parents(".snippet").data('fid');
                    $(".js-timeline-box[data-fid=" + fid + "] .js-amount" + cls).text(_amount);
                })
            });

            $(document).on('click', '.js-follow-path', function(el) {
              el.stopPropagation();
            });

            $(document).on('click', '.js-openoverlay', function() {
                var _request = $(this).data('href');
                var params = {};
                params.fid = $(this).data('fid');
                params.ffid = $(this).data('ffid');
                var _obj = {'request': _request};

                getData(_obj, function(data) {
                    _addOverlay(data, params);
                });
            });

            $(document).on('click', '.overlayclose', function() {
                $('.overlaywrapper').remove();
                return false;
            });

            $('.explbutton').click(function() {
                $('.toggleexpl').toggle();
                if ($('.toggleexpl').is(':visible')) {
                    $(this).html('<span class="glyphicon glyphicon-menu-up" aria-hidden="true"></span>');
                } else {
                    $(this).html('<span class="glyphicon glyphicon-menu-down" aria-hidden="true"></span>');
                }
            });
        }

        /**
         * Adds new column to the right on the reaction time line and hides the left most column.
         */
        function addItemCallback(data, obj) {
            var $newCol;
            var colId = obj.colid;
            var newColId = parseInt(colId + 1);

            $followup.find('.js-followup-timeline-col').each(function(index) {
                if (index === colId) {
                    $newCol = $('<div class="followup-row-col js-followup-timeline-col" data-id="' + newColId + '" id="el-' + newColId + '"></div>')
                      .html(getColumnContent(data))
                      .hide()
                      .fadeIn()
                      .css('display', 'inline-block');

                    $followup.children('.js-followup-timeline-row').append($newCol);

                    // Scroll timeline to the end.
                    var colsWidth = 0;
                    $('.js-followup-timeline-col').each(function() {
                      colsWidth += $(this).outerWidth();
                    });
                    var left = colsWidth - $('.js-followup-timeline').outerWidth();
                    $('.js-followup-timeline').animate({ scrollLeft: left });
                }
            });

            if (obj.reset) {
                $('.js-timeline-box').fadeIn();
            }
        }

      /**
       * Returns the content of a column in the reaction time line
       */
      function getColumnContent(data) {
            var _html = "";
            var _link = "";
            var _overlayLink = "";
            var _img = "";
            var _when = "";
            var _likeYes = "";
            var _likeNo = "";
            var _edgeLeft = "";
            var _gfxwho_overlay = "";
            var _hasTypOverlay = "";

            if (data.byinput) {
                for (var i in data.byinput.snippets) {
                    _likeYes = '<span class="badge js-amount js-like">' + data.byinput.snippets[i].lkyea + '</span> <a class="link-alt link-unstyled js-voting js-like" href="' + _host + '/followup/like/fid/' + data.byinput.snippets[i].fid + '"><span class="glyphicon glyphicon-thumbs-up" aria-hidden="true"></span></a>';
                    _likeNo = '<span class="badge js-amount js-dislike">' + data.byinput.snippets[i].lknay + '</span> <a class="link-alt link-unstyled js-voting js-dislike" href="' + _host + '/followup/unlike/fid/' + data.byinput.snippets[i].fid + '"><span class="glyphicon glyphicon-thumbs-down" aria-hidden="true"></span></a>';

                    _overlayLink = _host + '/followup/json/kid/' + _kid + '/ffid/' + data.byinput.snippets[i].ffid;
                    _edgeLeft = data.byinput.snippets[i].typ !== 'g' ? '<div class="followup-typ edge-left followup-typ-'+data.byinput.snippets[i].typ+'"> </div>' : '';
                    _gfxwho_overlay = data.byinput.snippets[i].typ !== 'g' ? '<div class="followup-typ gfx-who-overlay followup-typ-'+data.byinput.snippets[i].typ+'"> </div>' : '';
                    _hasTypOverlay = data.byinput.snippets[i].typ !== 'g' ? 'has-typ-overlay' : '';
                    if (data.byinput.snippets[i].relFowupCount > 0 && data.byinput.snippets[i].typ !== "r" && data.byinput.snippets[i].typ !== "e") {

                        _link = '<a class="link-unstyled followup-link followup-timeline-count followup-sprite followup-sprite-timeline-count js-ajaxclick"'
                        + ' href="' + _host + '/followup/json/kid/' + _kid + '/fid/' + data.byinput.snippets[i].fid + '">' + data.byinput.snippets[i].relFowupCount + '</a>';

                    } else
                        _link = '';

                    _html += '<div class="followup-timeline-box js-timeline-box js-openoverlay" data-href="' + _overlayLink + '" data-fid="' + data.byinput.snippets[i].fid + '">' +
                            ' <div class="well well-bordered followup-well-link '+_hasTypOverlay+'">' +
                            _edgeLeft + _gfxwho_overlay +
                            ' <div class="followup-gfx-who-wrapper"><img class="gfx_who_thumb" src="'+data.mediafolder+data.byinput.snippets[i].gfx_who+'" /></div>' +
                              data.byinput.snippets[i].expl  +
                            '<div class="offset-bottom-small">'+_likeYes + _likeNo +'</div>'+
                            ' </div>' +
                            _link +
                            '</div>';
                }
            } else if (!data.inputs && data.refs) {
                for (var i in data.refs.docs) {
                    var whendate = data.refs.docs[i].show_no_day === 'y' ? _dateConverter(data.refs.docs[i].when, 'my') : _dateConverter(data.refs.docs[i].when, 'dmy');

                    if (data.refs.docs.length != 0) {
                        _overlayLink = _host + '/followup/json/kid/' + _kid + '/ffid/' + data.refs.docs[i].ffid;
                        _when = '<p>' + whendate + '</p>';

                    } else {
                        _overlayLink = '';
                        _when = '';
                    }

                    if (data.refs.docs.length != 0) {
                        _img = '<div class="followup-gfx-who-wrapper"><img class="gfx_who_thumb refimg" src="' + data.mediafolder + data.refs.docs[i].gfx_who + '" /></div>';
                    } else {
                        _img = '';
                    }

                    _html += '<div class="followup-timeline-box js-timeline-box js-openoverlay" data-href="' + _overlayLink + '" data-ffid="' + data.refs.docs[i].ffid + '">' +
                            ' <div class="well well-bordered followup-well-link">' +
                            _img +
                            '     <p>' + data.refs.docs[i].titl + '</p>' +
                            _when +
                            ' </div>' +
                            '</div>';

                }

                for (var i in data.refs.snippets) {
                    var snippet = data.refs.snippets[i];
                    if (snippet.relFowupCount > 0 && snippet.typ !== "r" && snippet.typ !== "e") {
                        _link = '<a class="link-unstyled followup-link followup-timeline-count followup-sprite followup-sprite-timeline-count js-ajaxclick" href="' + _host + '/followup/json/kid/' + _kid + '/fid/' + data.refs.snippets[i].fid + '">' + data.refs.snippets[i].relFowupCount + '</a>';
                    } else {
                        _link = '';
                    }

                    _likeYes = '<span class="badge js-amount js-like">' + data.refs.snippets[i].lkyea + '</span> <a class="link-alt link-unstyled js-voting js-like" href="' + _host + '/followup/like/fid/' + data.refs.snippets[i].fid + '"><span class="glyphicon glyphicon-thumbs-up" aria-hidden="true"></span></a>';
                    _likeNo = '<span class="badge js-amount js-dislike">' + data.refs.snippets[i].lknay + '</span> <a class="link-alt link-unstyled js-voting js-dislike" href="' + _host + '/followup/unlike/fid/' + data.refs.snippets[i].fid + '"><span class="glyphicon glyphicon-thumbs-down" aria-hidden="true"></span></a>';

                    _overlayLink = _host + '/followup/json/kid/' + _kid + '/ffid/' + data.refs.snippets[i].ffid;

                    _edgeLeft = snippet.typ !== 'g' ? '<div class="followup-typ edge-left followup-typ-'+snippet.typ+'"> </div>' : '';
                    _gfxwho_overlay = snippet.typ !== 'g' ? '<div class="followup-typ gfx-who-overlay followup-typ-'+snippet.typ+'"> </div>' : '';
                    _hasTypOverlay = snippet.typ !== 'g' ? 'has-typ-overlay' : '';

                    _html += '<div class="followup-timeline-box js-timeline-box js-openoverlay" data-href="' + _overlayLink + '" data-fid="' + data.refs.snippets[i].fid + '">' +
                            ' <div class="well well-bordered followup-well followup-well-link '+_hasTypOverlay+'">' +
                            _edgeLeft + _gfxwho_overlay +
                             ' <div class="followup-gfx-who-wrapper"><img class="gfx_who_thumb" src="'+data.mediafolder+snippet.gfx_who+'" /></div>' +
                            '     ' + data.refs.snippets[i].expl + '' +
                            '<div class="offset-bottom-small">'+_likeYes + _likeNo +'</div>' +
                            ' </div>' +
                            _link +
                            '</div>';
                }
            }
            return _html;
        }

        function _addOverlay(data, params) {
            var _snippets = '';
            var _activeSnippetClass;
            var _activeDocClass;
            var _activeSnippet;
            var _edgeRight;
            var _showInTimelineLink;

            /**
             * Snippets
             */
            for (var i in data.doc.fowups) {
                var _likeYes = '<span class="badge js-amount js-like">' + data.doc.fowups[i].lkyea + '</span> <a class="link-alt link-unstyled js-voting js-like" href="' + baseUrl + '/followup/like/fid/' + data.doc.fowups[i].fid + '"><span class="glyphicon glyphicon-thumbs-up" aria-hidden="true"></span></a>';
                var _likeNo = '<span class="badge js-amount js-dislike">' + data.doc.fowups[i].lknay + '</span> <a class="link-alt link-unstyled js-voting js-dislike" href="' + baseUrl + '/followup/unlike/fid/' + data.doc.fowups[i].fid + '"><span class="glyphicon glyphicon-thumbs-down" aria-hidden="true"></span></a>';

                _activeSnippet = typeof params.fid != "undefined" && data.doc.fowups[i].fid == params.fid ? true : false;
                _activeSnippetClass = _activeSnippet ? 'active' : '';
                _activeDocClass = typeof params.ffid != "undefined" && data.doc.fowups[i].ffid == params.ffid ? 'active' : '';

                if (_activeSnippet) {
                  _showInTimelineLink = '<a class="btn btn-default" data-dismiss="modal" href="' + data.doc.fowups[i].show_in_timeline_link + '">Back to timeline</a>';
                } else {
                  _showInTimelineLink = '<a class="btn btn-default" href="' + data.doc.fowups[i].show_in_timeline_link + '">Follow path</a>';
                }
                _edgeRight = data.doc.fowups[i].typ !== 'g' ? '<div class="followup-typ edge-right followup-typ-'+data.doc.fowups[i].typ + '"> </div>' : '';

                _snippets += '<div class="well well-simple well-simple-light ' + _activeSnippetClass + '" data-fid="' + data.doc.fowups[i].fid + '">' +
                        _edgeRight +
                        data.doc.fowups[i].expl +
                        '<span class="offset-right">' +
                        _likeYes + ' ' +
                        _likeNo +
                        '</span>' +
                        _showInTimelineLink +
                        '</div>';
            }

            /**
             * doc + Snippets
             */
            var when = data.doc.show_no_day === 'y' ? _dateConverter(data.doc.when, 'my') : _dateConverter(data.doc.when, 'dmy');

            var _content =
                '<div class="well well-accent ' + _activeDocClass + '">' +
                '<p>' + data.doc.who + '</p>' +
                '<p class="small">' + when + '</p>' +
                  '<span class="glyphicon glyphicon-file icon-white icon-offset text-muted"></span>' +
                '<a target="_blank" href="' + _host + data.mediafolder + data.doc.ref_doc + '" class="link-unstyled link-unstyled-alt">' + data.doc.ref_doc + '</a>' +
                '</div>' +
                  '<hr />' +
                _snippets;

            var _modal = '<div class="modal fade" id="modalFollowup" tabindex="-1" role="dialog" aria-labelledby="modalFollowupLabel" aria-hidden="true">'
                + '<div class="modal-dialog">'
                + '<div class="modal-content">'
                + '<div class="modal-header">'
                + '<button type="button" class="close" data-dismiss="modal" aria-label="Close">'
                + '<span aria-hidden="true">&times;</span>'
                + '</button>'
                + '<h4 class="modal-title" id="modalFollowupLabel">'
                + data.doc.titl
                + '</h4>'
                + '</div>'
                + '<div class="modal-body">'
                + _content
                + '</div>'
                + '</div>'
                + '</div>'
                + '</div>';

            $followup.append(_modal);

            $('#modalFollowup').modal();
        }

        /**
         * Get data by ajax request
         */
        function getData(obj, callback) {
            $('#followuploader').show();
            $.ajax({
                url: obj.request,
                dataType: 'json',
                success: function(data) {
                    $('#followuploader').hide();
                    callback(data, obj);
                }
            });
        }

        /**
         * @param UNIX_timestamp
         * @param format dmy, d, m, dmy|hm
         * @return {formated timestamp}
         */
        function _dateConverter(UNIX_timestamp, format) {
            var a = new Date(UNIX_timestamp * 1000);
            var months = ['Januar', 'Februar', 'März', 'April', 'Mai', 'Juni', 'Juli', 'August', 'September', 'Oktober', 'November', 'Dezember'];
            var year = a.getFullYear();
            var month = months[a.getMonth()];
            var day = a.getDate();
            var hour = a.getHours();
            var min = a.getMinutes() < 10 ? "0" + a.getMinutes() : a.getMinutes();
            var sec = a.getSeconds();

            switch (format) {
                case "dmy":
                    return day + '. ' + month + ' ' + year;
                    break;
                case "my":
                    return month + ' ' + year;
                    break;
                case "dm":
                    var jetzt = new Date();
                    var time1 = Date.UTC(jetzt.getFullYear(), jetzt.getMonth(), jetzt.getDate());
                    var time2 = Date.UTC(year, a.getMonth(), day);
                    var dayDiff = (time2 - time1) / 1000 / 3600 / 24;
                    var mfDays = ['Gestern', 'Heute', 'Morgen'];
                    return mfDays[dayDiff + 1] || day + '. ' + month;
                    break;
                case "d":
                    return day;
                    break;
                case "m":
                    return month;
                    break;
                case "hour":
                    return hour;
                    break;
                case "min":
                    return min;
                    break;
                case "sec":
                    return sec;
                    break;
                case "hm":
                    return hour + ':' + min;
                    break;
                case "dmy|hm":
                    return day + '. ' + month + ' ' + year + ' | ' + hour + ':' + min;
                    break;
                case "dm|hm":
                    return day + '. ' + month + ' | ' + hour + ':' + min;
                    break;
                case "mfDays-dm|hm":
                    var jetzt = new Date();
                    var time1 = Date.UTC(jetzt.getFullYear(), jetzt.getMonth(), jetzt.getDate());
                    var time2 = Date.UTC(year, a.getMonth(), day);
                    var dayDiff = (time2 - time1) / 1000 / 3600 / 24;
                    var mfDays = ['Gestern', 'Heute', 'Morgen'];
                    return mfDays[dayDiff + 1] || day + '. ' + month + ' | ' + hour + ':' + min;
                    break;
            }
        }
    }

    FollowUp.EVENTS = {};
    window.FollowUp = FollowUp;

}(jQuery, window, document));
