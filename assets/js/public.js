(function ($) {

    $(document).ready(function () {
        let mdl = window.MDL,
            conf = window.MDL_CONFIG;

        mdl._debugLog('Cookie Notice: activated', mdl, conf);

        if (typeof conf.cookieNotice === 'object' && !window.wpCookies.get('EuAccCookies') && conf.isEU) {
            let $options = {
                id: 'mdl-popup-dialog-cookies',
                title: conf.cookieNotice.title,
                text: mdl.decodeHTML(conf.cookieNotice.content),
                cancelable: false,
                positive: {
                    title: conf.cookieNotice.positive,
                    onClick: function (e) {
                        mdl._debugLog('EuAccCookies accepted', conf.cookieNotice.expires);
                        window.wpCookies.set('EuAccCookies', '1', conf.cookieNotice.expires, '/', window.location.hostname);
                    }
                }
            };

            if (typeof conf.cookieNotice.negative === 'object') {
                $options.negative = {
                    title: conf.cookieNotice.negative.title,
                    onClick: function (e) {
                        window.location.href = conf.cookieNotice.negative.url;
                    }
                }
            }

            mdl.popup_showDialog($options);
        }

    });

})(jQuery);