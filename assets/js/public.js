class MDL_CookieNotice extends MDL {

    constructor(obj) {
        super(obj);
    }


    /**
     * Initial base scripts
     */
    _init() {
        super._debugLog('Cookie Notice: _init() =>', this);

        this._EuAccCookies();
    }


    /**
     * Open Popups
     * @param self object
     * @private
     */
    _openPopup(self) {
        let $options = {
            id: 'mdl-popup-dialog-cookies',
            title: self.cookieNotice.title,
            text: self.decodeHTML(self.cookieNotice.content),
            cancelable: false,
            positive: {
                title: self.cookieNotice.positive,
                onClick: function (e) {
                    window.MDL._debugLog('EuAccCookies accepted', self.cookieNotice.expires);
                    window.wpCookies.set('EuAccCookies', '1', self.cookieNotice.expires, '/', window.location.hostname);
                }
            }
        };

        if (typeof self.cookieNotice.negative === 'object') {
            $options.negative = {
                title: self.cookieNotice.negative.title,
                onClick: function (e) {
                    window.location.href = self.cookieNotice.negative.url;
                }
            }
        }

        self.popup_showDialog($options);

    }


    /**
     * Set EU cookies
     * @private
     */
    _EuAccCookies(show = false) {
        if (show || (typeof this.cookieNotice === 'object' && !window.wpCookies.get('EuAccCookies') && this.isEU)) {
            this._openPopup(this);
        }
    }
}

window.MDL_CookieNotice = new MDL_CookieNotice(window.MDL_CONFIG);

window.MDL_CookieNotice._init();
