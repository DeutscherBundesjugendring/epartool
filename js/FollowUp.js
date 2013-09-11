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
(function ($, window, document) {
    /**
     * all PageManager stuff
     * @class PageManager
     * @param pageArr
     * @required pageArr
     * @constructor
     */

    function FollowUp(kid) {

        //    console.log(kid)

        var _instance = this;
        var _colWidth;
        var _kid = kid;
        var _host = 'http://' + window.location.host;

        var _$followup = $('#followup');
        _init();

        function _init() {
            //  console.log('followUp')
            _setVerticalAlign()
            _initEventListener()
            $('.ajaxclick').trigger('click')

        }

        function _initEventListener() {
            $('.ajaxclick').live('click', function (el) {
                el.preventDefault()
                el.stopPropagation()
                var _request = $(this).attr('href');
                var _colId = parseInt($(this).parent().parent().parent().attr('data-id'));
                var _reset;

                if ($(this).hasClass('reset')) _reset = true;

                var _obj = {'request': _request, 'colid': _colId, 'reset': _reset}


                if (!$(this).hasClass('active')) {
                    _getAjaxData(_obj, _addItemCallback)

                }


                $('.ajaxclick').removeClass('active')
                $(this).addClass('active')


                $(this).parent().parent().parent().find('.timeline-box').hide()
                $(this).parent().parent('.timeline-box').show()

            })

            $('a.voting').live('click', function (el) {
                //  console.log('voting click')
                el.preventDefault()
                el.stopPropagation()
                var _thisEl = $(this);
                var _request = $(this).attr('href');
                var _target = el.target;
                var _obj = {'request': _request, 'target': _target}

                _getAjaxData(_obj, function (data, status, obj) {
                    var _amount = data.lkyea || data.lknay;
                    // obj.target.innerText = '(' + _amount + ')';
                    $(obj.target).text('(' + _amount + ')');
                })

            })

            $('.openoverlay').live('click', function () {
                //  console.log('content click')
                var _request = $(this).data('href');
                var _highlightElement = $(this).data('elementid')
                var _obj = {'request': _request}

                _getAjaxData(_obj, function (data, status, obj) {

                    //      console.log(data)
                    _addOverlay(data, _highlightElement)
                })
            })
            $('.overlayclose').live('click', function () {
                $('.overlaywrapper').remove()
            })


            $('.explbutton').click(function () {
                $('.toggleexpl').toggle()
                _setVerticalAlign()
            })


        }

        /**
         * vertical positioning of all Elements
         * @private
         */
        function _setVerticalAlign() {
            var _colItems;
            var _colHeight;

            var _posTop;
            var _followUpHeight = $('#followup').height() - 70; //maxHeight 458
            var _itemHeight;

            $('#followup .wrapper').children('.col').each(function (index, element) {
                $(this).attr('data-id', index)
                _colHeight = $(this).height();
                _colWidth = $(this).width();
                _posTop = ((_followUpHeight / 2) - (_colHeight / 2));
                $(this).css('top', _posTop)
                $(this).css('left', index * _colWidth)

            })

        }

        var wrapperHeight = 0;

        function _setHorizontalAlign() {

            $('#followup').removeAttr('style')
            $('#followup .wrapper').children('.col').each(function (index, element) {
                var _tempHeight = $(element).height();
               // console.log('wrapperheight: '+wrapperHeight)
               // console.log('tempheight: '+_tempHeight)
                if (_tempHeight > wrapperHeight) {
                    wrapperHeight = _tempHeight;
                } else {
                    //wrapperHeight = 0;
                }

            })


            // console.log(wrapperHeight)
            $('#followup').height(wrapperHeight + 200);
            
           // console.log("wrapperHeightAfter: "+ wrapperHeight)
           // console.log("followupHeightAfter: "+ $('#followup').height())
            _setVerticalAlign()
        }


        /**
         * add New Item to the View, called from ajaxRequest
         * @method _addItemCallback
         * @param data
         * @param statuscode
         * @private
         */
        function _addItemCallback(data, statuscode, obj) {

            var _jsonData = data;
            var _colId = obj.colid;
            var _statusCode = statuscode;
            if (_statusCode === 200) {
                //    console.log(_jsonData)
                $('#followup .wrapper').children('.col').each(function (index, element) {
                    //   console.log(index)
                    if (index == _colId) {
                        var _newCol = '<div class="col" data-id="' + parseInt(_colId + 1) + '" id="el-' + parseInt(_colId + 1) + '"></div>'


                        $('#followup .wrapper').append($(_newCol).html(_buildNewCol(_jsonData)).hide().fadeIn())

                        $('#followup .wrapper').animate({
                            scrollLeft: _colWidth * _colId
                        })


                    } else if (index >= _colId) {
                        $('#el-' + index).remove()

                        //  $(this).append(_buildNewCol(_jsonData))
                    }
                })

                if (obj.reset) {
                    $('.timeline-box').fadeIn()
                }


                _setVerticalAlign()

                window.setTimeout(function () {
                    _setHorizontalAlign()

                }, 300)


            } else {
                alert('Ups, die Anfrage lieferte kein Ergebnis')
            }
        }


        /**
         * @method _buildNewCol
         * @param data
         * @returns {string}
         * @private
         */
        function _buildNewCol(data) {

            // console.log(data)
            var _html = "";

            var _link = "";
            var _overlayLink = "";
            var _img = "";
            var _when = "";
            var _likeYes = "";
            var _likeNo = "";


            if (data.byinput) {

                for (var i in data.byinput.snippets) {
                    //followup/json/kid/8/fid/1
                    _likeYes = '<a class="voting like" href="' + _host + '/followup/like/fid/' + data.byinput.snippets[i].fid + '"><span class="amount">(' + data.byinput.snippets[i].lkyea + ')</span><span class="icon"></span></a>';
                    _likeNo = '<a class="voting dislike" href="' + _host + '/followup/unlike/fid/' + data.byinput.snippets[i].fid + '"><span class="amount">(' + data.byinput.snippets[i].lknay + ')</span> <span class="icon"></span></a>';

                    _overlayLink = _host + '/followup/json/kid/' + _kid + '/ffid/' + data.byinput.snippets[i].ffid;

                    _link = '<a class="ajaxclick" href="' + _host + '/followup/json/kid/' + _kid + '/fid/' + data.byinput.snippets[i].fid + '">' + data.byinput.snippets[i]['relFowupCount'] + '</a>';

                    _html += '<div class="timeline-box openoverlay" data-href="' + _overlayLink + '" data-elementid="' + data.byinput.snippets[i].fid + '">' +
                        ' <div class="content">' +
                        '     <p>' + data.byinput.snippets[i].expl + '</p>' +
                        _likeYes +
                        _likeNo +
                        ' </div>' +
                        ' <div class="timeline-countlink sprite">' +
                        _link +
                        ' </div>' +
                        '</div>';

                }
            } else if (data.inputs) {
                //nothing to do, just show the timeline-box
            }
            else if (data.refs) {

                for (var i in data.refs.docs) {
                    //followup/json/kid/8/fid/1
                    // console.log('docs')
                    // console.log(data)
                    var whendate = data.refs.docs[i].show_no_day == 'y' ? _dateConverter(data.refs.docs[i].when, 'my') : _dateConverter(data.refs.docs[i].when, 'dmy');
                    if (data.refs.docs.length != 0) {
                        _overlayLink = _host + '/followup/json/kid/' + _kid + '/ffid/' + data.refs.docs[i].ffid;
                        _when = '<p>' + whendate + '</p>'

                    } else {
                        _overlayLink = '';
                        _when = '';
                    }


                    if (data.refs.docs.length != 0) {
                        _img = '<img class="refimg" src="' + _host + data.mediafolder + data.refs.docs[i].gfx_who + '" />'
                    } else {
                        _img = '';
                    }


                    _html += '<div class="timeline-box openoverlay" data-href="' + _overlayLink + '" data-elementid="' + data.refs.docs[i].ffid + '">' +
                        ' <div class="content">' +
                        _img +
                        '     <p class="">' + data.refs.docs[i].titl + '</p>' +
                        _when +


                        ' </div>' +

                        '</div>';

                }
                for (var i in data.refs.snippets) {
                    var snippet = data.refs.snippets[i];
                    if (snippet.relFowupCount != 0 && snippet.typ !== "r" && snippet.typ !== "e") {

                        _link = '<div class="timeline-countlink sprite"><a class="ajaxclick" href="' + _host + '/followup/json/kid/' + _kid + '/fid/' + data.refs.snippets[i].fid + '">' + data.refs.snippets[i].relFowupCount + '</a></div>';
                    } else {
                        _link = '';
                    }

                    _likeYes = '<a class="voting like" href="' + _host + '/followup/like/fid/' + data.refs.snippets[i].fid + '"><span class="amount">(' + data.refs.snippets[i].lkyea + ')</span><span class="icon"></span></a>';
                    _likeNo = '<a class="voting dislike" href="' + _host + '/followup/unlike/fid/' + data.refs.snippets[i].fid + '"><span class="amount">(' + data.refs.snippets[i].lknay + ')</span> <span class="icon"></span></a>';

                    _overlayLink = _host + '/followup/json/kid/' + _kid + '/ffid/' + data.refs.snippets[i].ffid;

                    _html += '<div class="timeline-box openoverlay" data-href="' + _overlayLink + '" data-elementid="' + data.refs.snippets[i].fid + '">' +
                        ' <div class="content">' +

                        '     ' + data.refs.snippets[i].expl + '' +
                        _likeYes +
                        _likeNo +
                        ' </div>' +

                        _link +

                        '</div>';


                }

            }

            return _html;
        }

        function _addOverlay(data, id) {
            var _snippets = '';

            var _id = id;
            //  console.log(_id)

            var _activeSnippetClass = '';
            var _activeDocClass = '';

            /**
             *
             * Snippets
             */
            for (var i in data.doc.fowups) {

                var _likeYes = '<a class="voting like" href="http://dev.dbjr/followup/like/fid/' + data.doc.fowups[i].fid + '"><span class="amount">(' + data.doc.fowups[i].lkyea + ')</span><span class="icon"></span></a>';
                var _likeNo = '<a class="voting dislike" href="http://dev.dbjr/followup/unlike/fid/' + data.doc.fowups[i].fid + '"><span class="amount">(' + data.doc.fowups[i].lknay + ')</span> <span class="icon"></span></a>';

                if (data.doc.fowups[i].fid == _id) {
                    _activeSnippetClass = 'active';
                } else {
                    _activeSnippetClass = '';
                }
                if (data.doc.ffid == _id) {
                    _activeDocClass = 'active';
                } else {
                    _activeDocClass = '';
                }

                _snippets += '<div class="snippet ' + _activeSnippetClass + '">' +
                    '<div>' + data.doc.fowups[i].expl + '</div>' +
                    _likeYes +
                    _likeNo +
                    '<div class="overlayclose"><p>Zurück zur Zeitleiste</p></div>' +


                    '</div>'
            }


            /**
             *
             * doc + Snippets
             *
             */

            var when = data.doc.show_no_day == 'y' ? _dateConverter(data.doc.when, 'my') : _dateConverter(data.doc.when, 'dmy');
            console.log(_dateConverter(data.doc.when, 'my'))
            console.log(_dateConverter(data.doc.when, 'dmy'))
            var _content = '<div class="overlayclose overlayclosebutton"></div><div class="overlaycontent">' +

                '<div class="">' +
                '<h1>' + data.doc.titl + '</h1>' +
                '<div class="docs ' + _activeDocClass + '">' +
                '<p>' + data.doc.who + '</p>' +
                '<p>' + when + '</p>' +
                '<a class="" target="_blank" href="' + _host + data.mediafolder + data.doc.ref_doc + '">' + data.doc.ref_doc + '</a>' +

                '</div>' +
                _snippets +

                '</div>'


            '</div>'

            var _overlay = '<div class="overlaywrapper">' +
                _content +
                '</div>'
            _$followup.append(_overlay)
            $('.overlaywrapper').fadeIn()
        }


        /**
         * AjaxRequest for Data
         * @method _getAjaxData
         * @param action
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
                success: function () {
                    $('#followuploader').hide();
                },
                statusCode: {
                    200: function (_json) {

                        _callback(_json, 200, obj)

                    },
                    400: function (_json) {


                    },
                    403: function (_json) {


                    },
                    404: function (_json) {
                        _callback(_json, 404)

                    },
                    410: function (_json) {
                        _callback(_json, 410)

                    },
                    409: function (_json) {

                    },
                    500: function () {

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
                    var time1 = Date.UTC(jetzt.getFullYear(), jetzt.getMonth(), jetzt.getDate())
                    var time2 = Date.UTC(year, a.getMonth(), day)
                    var dayDiff = (time2 - time1) / 1000 / 3600 / 24
                    var mfDays = ['Gestern', 'Heute', 'Morgen']
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
                    var time1 = Date.UTC(jetzt.getFullYear(), jetzt.getMonth(), jetzt.getDate())
                    var time2 = Date.UTC(year, a.getMonth(), day)
                    var dayDiff = (time2 - time1) / 1000 / 3600 / 24
                    var mfDays = ['Gestern', 'Heute', 'Morgen']
                    return mfDays[dayDiff + 1] || day + '. ' + month + ' | ' + hour + ':' + min;
                    break;
            }
        }

    }

    FollowUp.EVENTS = {};

    window.FollowUp = FollowUp;

}(jQuery, window, document));
