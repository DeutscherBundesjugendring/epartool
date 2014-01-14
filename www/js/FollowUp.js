/**
 *
 * Provides the FollowUp class...
 *
 * @module      FollowUp
 *
 * @author        michaelpfutze
 * @copyright     Digitalroyal GmbH <www.digitalroyal.de>
 *
 * @date          21.05.13
 * @time          10:05
 *
 */

;
(function($, window, document) {
    /**
     * Followup Tool
     * @param kid Konsultationsid
     * @constructor
     */

    function FollowUp(kid) {


        var _instance = this;
        var _colWidth;
        var _kid = kid;
        var _host = window.location.protocol + "//" + window.location.host;

        var _$followup = $('#followup');
        _init();

        function _init() {
            _setVerticalAlign();
            _initEventListener();
          // if ($("#followup .col").length === 1) $('.ajaxclick').trigger('click');

        }

        function _initEventListener() {
            $(document).on('click', '.ajaxclick', function(el) {
                el.preventDefault();
                el.stopPropagation();
                var _request = $(this).attr('href');
                var _colId = parseInt($(this).parent().parent().parent().attr('data-id'));
                var _reset;

                if ($(this).hasClass('reset'))
                    _reset = true;

                var _obj = {'request': _request, 'colid': _colId, 'reset': _reset};


                if (!$(this).hasClass('active')) {
                    _getAjaxData(_obj, _addItemCallback);

                }


                $('.ajaxclick').removeClass('active');
                $(this).addClass('active');


                $(this).parent().parent().parent().find('.timeline-box').hide();
                $(this).parent().parent('.timeline-box').show();

            })

            $(document).on('click', 'a.voting', function(el) {
                el.preventDefault();
                el.stopPropagation();
                var _thisEl = $(this);
                var _request = $(this).attr('href');
                var _target = el.target;
                var _obj = {'request': _request, 'target': _target};

                _getAjaxData(_obj, function(data, status, obj) {

                    var _amount = data.lkyea || data.lknay;
                    // obj.target.innerText = '(' + _amount + ')';
                    _thisEl.children("span.amount").text('(' + _amount + ')');

                   var cls = _thisEl.hasClass("like") ? ".like" : ".dislike";
                    var fid = _thisEl.parents(".snippet").data('fid');
                    $("#followup .wrapper .timeline-box[data-fid="+fid+"] "+cls+" .amount").text('(' + _amount + ')');
                })

            })

            $(document).on('click', '.openoverlay', function() {
                var _request = $(this).data('href');
                var params = {};
                params.fid = $(this).data('fid');
                params.ffid = $(this).data('ffid');
                var _obj = {'request': _request};

                _getAjaxData(_obj, function(data, status, obj) {

                    _addOverlay(data, params);
                });
            });
            $(document).on('click', '.overlayclose', function() {
                $('.overlaywrapper').remove();
                return false;
            });


            $('.explbutton').click(function() {
                $('.toggleexpl').toggle();
                _setVerticalAlign();
            });


        }

        /**
         * vertical positioning of all Elements
         * @private
         */
        function _setVerticalAlign() {
            var colHeight;
            var newHeight;
            var _posTop;
            var maxColHeight = 0;

            $('#followup .wrapper').children('.col').each(function(index, element) {
                maxColHeight = $(this).height() > maxColHeight ? $(this).height() : maxColHeight;
            });

            newHeight = maxColHeight+100 < 500 ? 500 : maxColHeight;
            $('#followup').animate({height:newHeight+100+"px"},200);

            $("body").scrollTop($('#followup').position().top)
            $('#followup .wrapper').children('.col').each(function(index, element) {
                $(this).attr('data-id', index);
                colHeight = $(this).height();
                _colWidth = $(this).width();
                _posTop = ((newHeight / 2) - (colHeight / 2)) + 90;
                $(this).css('top', _posTop);
                $(this).css('left', index * _colWidth);

            });

        }


        /**
         * add New Item to the View, called from ajaxRequest
         * @method _addItemCallback
         * @param data
         * @param statuscode
         * @param obj
         * @private
         */
        function _addItemCallback(data, statuscode, obj) {
            var _jsonData = data;
            var _colId = obj.colid;
            var _statusCode = statuscode;
            if (_statusCode === 200) {
                $('#followup .wrapper').children('.col').each(function(index, element) {
                    if (index == _colId) {
                        var _newCol = '<div class="col" data-id="' + parseInt(_colId + 1) + '" id="el-' + parseInt(_colId + 1) + '"></div>';


                        $('#followup .wrapper').append($(_newCol).html(_buildNewCol(_jsonData)).hide().fadeIn());

                        $('#followup .wrapper').animate({
                            scrollLeft: _colWidth * _colId
                        });


                    } else if (index >= _colId) {
                        $('#el-' + index).remove();
                          $(this).append(_buildNewCol(_jsonData))
                    }
                });

                if (obj.reset) {
                    $('.timeline-box').fadeIn();
                }


                _setVerticalAlign();




            } else {
                alert('Ups, die Anfrage lieferte kein Ergebnis');
            }
        }


        /**
         * @method _buildNewCol
         * @param data
         * @returns {string}
         * @private
         */
        function _buildNewCol(data) {

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
                    //followup/json/kid/8/fid/1
                    _likeYes = '<a class="voting like" href="' + _host + '/followup/like/fid/' + data.byinput.snippets[i].fid + '"><span class="amount">(' + data.byinput.snippets[i].lkyea + ')</span><span class="thumb-up"></span></a>';
                    _likeNo = '<a class="voting dislike" href="' + _host + '/followup/unlike/fid/' + data.byinput.snippets[i].fid + '"><span class="amount">(' + data.byinput.snippets[i].lknay + ')</span> <span class="thumb-down"></span></a>';

                    _overlayLink = _host + '/followup/json/kid/' + _kid + '/ffid/' + data.byinput.snippets[i].ffid;
                    _edgeLeft = data.byinput.snippets[i].typ !== 'g' ? '<div class="followup-typ edge-left followup-typ-'+data.byinput.snippets[i].typ+'"> </div>' : '';
                    _gfxwho_overlay = data.byinput.snippets[i].typ !== 'g' ? '<div class="followup-typ gfx-who-overlay followup-typ-'+data.byinput.snippets[i].typ+'"> </div>' : '';
                    _hasTypOverlay = data.byinput.snippets[i].typ !== 'g' ? 'has-typ-overlay' : '';
                    if (data.byinput.snippets[i].relFowupCount > 0 && data.byinput.snippets[i].typ !== "r" && data.byinput.snippets[i].typ !== "e") {

                        _link = '<div class="timeline-countlink sprite">';
                        _link += '<a class="ajaxclick" href="' + _host + '/followup/json/kid/' + _kid + '/fid/' + data.byinput.snippets[i].fid + '">' + data.byinput.snippets[i].relFowupCount + '</a>';
                        _link += ' </div>';

                    } else
                        _link = '';

                    _html += '<div class="timeline-box openoverlay" data-href="' + _overlayLink + '" data-fid="' + data.byinput.snippets[i].fid + '">' +
                            ' <div class="content clearfix '+_hasTypOverlay+'">' +
                            _edgeLeft + _gfxwho_overlay +
                            ' <div class="followup-gfx-who-wrapper"><img class="gfx_who_thumb" src="'+data.mediafolder+data.byinput.snippets[i].gfx_who+'" /></div>' +
                              data.byinput.snippets[i].expl  +
                            '<div class="clearleft">'+_likeYes + _likeNo +'</div>'+
                            ' </div>' +
                            _link +
                            '</div>';

                }
            } else if (data.inputs) {
                //nothing to do, just show the timeline-box
            }
            else if (data.refs) {

                for (var i in data.refs.docs) {
                    var whendate = data.refs.docs[i].show_no_day === 'y' ? _dateConverter(data.refs.docs[i].when, 'my') : _dateConverter(data.refs.docs[i].when, 'dmy');
                    if (data.refs.docs.length != 0) {
                        _overlayLink = _host + '/followup/json/kid/' + _kid + '/ffid/' + data.refs.docs[i].ffid;
                        _when = '<p class="clearleft">' + whendate + '</p>';

                    } else {
                        _overlayLink = '';
                        _when = '';
                    }


                    if (data.refs.docs.length != 0) {
                        _img = '<div class="followup-gfx-who-wrapper"><img class="gfx_who_thumb refimg" src="' + data.mediafolder + data.refs.docs[i].gfx_who + '" /></div>';
                    } else {
                        _img = '';
                    }


                    _html += '<div class="timeline-box openoverlay" data-href="' + _overlayLink + '" data-ffid="' + data.refs.docs[i].ffid + '">' +
                            ' <div class="content">' +
                            _img +
                            '     <p class="">' + data.refs.docs[i].titl + '</p>' +
                            _when +
                            ' </div>' +
                            '</div>';

                }
                for (var i in data.refs.snippets) {
                    var snippet = data.refs.snippets[i];
                    if (snippet.relFowupCount > 0 && snippet.typ !== "r" && snippet.typ !== "e") {

                        _link = '<div class="timeline-countlink sprite"><a class="ajaxclick" href="' + _host + '/followup/json/kid/' + _kid + '/fid/' + data.refs.snippets[i].fid + '">' + data.refs.snippets[i].relFowupCount + '</a></div>';
                    } else {
                        _link = '';
                    }

                    _likeYes = '<a class="clearleft voting like" href="' + _host + '/followup/like/fid/' + data.refs.snippets[i].fid + '"><span class="amount">(' + data.refs.snippets[i].lkyea + ')</span><span class="thumb-up"></span></a>';
                    _likeNo = '<a class="clearleft voting dislike" href="' + _host + '/followup/unlike/fid/' + data.refs.snippets[i].fid + '"><span class="amount">(' + data.refs.snippets[i].lknay + ')</span><span class="thumb-down"></span></a>';

                    _overlayLink = _host + '/followup/json/kid/' + _kid + '/ffid/' + data.refs.snippets[i].ffid;

                    _edgeLeft = snippet.typ !== 'g' ? '<div class="followup-typ edge-left followup-typ-'+snippet.typ+'"> </div>' : '';
                    _gfxwho_overlay = snippet.typ !== 'g' ? '<div class="followup-typ gfx-who-overlay followup-typ-'+snippet.typ+'"> </div>' : '';
                    _hasTypOverlay = snippet.typ !== 'g' ? 'has-typ-overlay' : '';

                    _html += '<div class="timeline-box openoverlay" data-href="' + _overlayLink + '" data-fid="' + data.refs.snippets[i].fid + '">' +
                            ' <div class="content clearfix '+_hasTypOverlay+'">' +
                            _edgeLeft + _gfxwho_overlay +
                             ' <div class="followup-gfx-who-wrapper"><img class="gfx_who_thumb" src="'+data.mediafolder+snippet.gfx_who+'" /></div>' +
                            '     ' + data.refs.snippets[i].expl + '' +
                            '<div class="clearleft">'+_likeYes + _likeNo +'</div>' +
                            ' </div>' +
                            _link +
                            '</div>';


                }

            }

            return _html;
        }

        function _addOverlay(data, params) {
            var _snippets = '';
            var _activeSnippetClass = '';
            var _activeDocClass = '';
            var _activeSnippet;
            var _edgeRight = "";
            var _show_in_timeline_link = "";

            /**
             *
             * Snippets
             */
            for (var i in data.doc.fowups) {

                var _likeYes = '<a class="voting like" href="http://dev.dbjr/followup/like/fid/' + data.doc.fowups[i].fid + '"><span class="amount">(' + data.doc.fowups[i].lkyea + ')</span><span class="thumb-up"></span></a>';
                var _likeNo = '<a class="voting dislike" href="http://dev.dbjr/followup/unlike/fid/' + data.doc.fowups[i].fid + '"><span class="amount">(' + data.doc.fowups[i].lknay + ')</span><span class="thumb-down"></span></a>';
                _activeSnippet = typeof params.fid != "undefined"  && data.doc.fowups[i].fid == params.fid ? true : false;
                _activeSnippetClass = _activeSnippet ? 'active' : '';
                _activeDocClass = typeof params.ffid != "undefined"  && data.doc.fowups[i].ffid == params.ffid ? 'active' : '';
                if (_activeSnippet) {
                    _show_in_timeline_link = '<a class="btn overlayclose" href="'+data.doc.fowups[i].show_in_timeline_link+'">Zurück zur Zeitleiste</a>';
                } else {
                    _show_in_timeline_link = '<a class="btn" href="'+data.doc.fowups[i].show_in_timeline_link+'">Diesem Pfad folgen.</a>';

                }
                _edgeRight = data.doc.fowups[i].typ !== 'g' ? '<div class="followup-typ edge-right followup-typ-'+data.doc.fowups[i].typ+'"> </div>' : '';

                _snippets += '<div class="clearfix snippet ' + _activeSnippetClass + '" data-fid="'+data.doc.fowups[i].fid+'">' +
                        _edgeRight +
                        '<div class="span6">' + data.doc.fowups[i].expl + '</div>' +
                        '<div class="likeyes_likeno">'+_likeYes + _likeNo + '</div>' +
                        _show_in_timeline_link +
                        '</div>';
            }


            /**
             *
             * doc + Snippets
             *
             */

            var when = data.doc.show_no_day === 'y' ? _dateConverter(data.doc.when, 'my') : _dateConverter(data.doc.when, 'dmy');
            var _content = '<div class="overlayclose overlayclosebutton"></div><div class="overlaycontent">' +
                    '<div class="">' +
                    '<h1>' + data.doc.titl + '</h1>' +
                    '<div class="docs ' + _activeDocClass + '">' +
                    '<p>' + data.doc.who + '</p>' +
                    '<p>' + when + '</p>' +
                    '<a class="" target="_blank" href="' + _host + data.mediafolder + data.doc.ref_doc + '">' + data.doc.ref_doc + '</a>' +
                    '</div>' +
                    _snippets +
                    '</div>';

            '</div>';

            var _overlay = '<div class="overlaywrapper">' +
                    _content +
                    '</div>';
            _$followup.append(_overlay);
            $('.overlaywrapper').fadeIn(function() {
                try {
                    var top = $(".overlaycontent .snippet.active").position().top - 30 || 0;
                    $(".overlaycontent").scrollTop(top);

                } catch(e) {

                }
            });

        }


        /**
         * AjaxRequest for Data
         * @method _getAjaxData
         * @param obj
         * @param callback
         * @private
         */
        function _getAjaxData(obj, callback) {
            var _action = obj.request;
            var _callback = callback;
            $('#followuploader').show();
            $.ajax({
                type: 'GET',
                url: _action,
                crossDomain: false,
                dataType: 'json',
                success: function() {
                    $('#followuploader').hide();
                },
                statusCode: {
                    200: function(_json) {

                        _callback(_json, 200, obj);

                    },
                    400: function(_json) {


                    },
                    403: function(_json) {


                    },
                    404: function(_json) {
                        _callback(_json, 404);

                    },
                    410: function(_json) {
                        _callback(_json, 410);

                    },
                    409: function(_json) {

                    },
                    500: function() {

                    }
                }
            });

        }

        /**
         * @method dateConverter
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
            //var time = date + '.' + month + ' ' + year;

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
