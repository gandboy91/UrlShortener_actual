;(function () {
    const crsfToken = document.getElementById('crsft').getAttribute('content');
    const regexp = /^(https?:\/\/)([\w\.]+)([\/]?.*)$/;
    const messages = {
        'successMessage': 'Успешно!',
        'emptyUrlMessage': 'Введите url!',
        'incorrectUrl': 'Проверьте корректность введенного url!' };

    let UrlShortener = React.createClass({
        displayName: 'UrlShortener',

        getInitialState: function () {
            return { shortUrl: '', message: '' };
        },
        urlSend: function (checkUrlResult) {
            this.setState({ shortUrl: '', message: '' });
            let thisComponent = this;
            axios.post('minUrl/new', { 'longUrl': checkUrlResult['longUrl'] }, { headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': crsfToken
                }
            }).then(function (response) {
                let shortUrl = '';
                let respdata = JSON.parse(response.request.response);
                shortUrl = respdata['shortUrl'];
                thisComponent.setState({ message: messages['successMessage'] });
                thisComponent.setState({ shortUrl: shortUrl });
            }).catch(function (error) {
                if (errorContainer = JSON.parse(error.response.request.response)) {
                    thisComponent.setState({ message: errorContainer['error'] });
                } else {
                    console.log(error);
                }
            });
        },
        render: function () {
            return React.createElement(
                'div',
                null,
                React.createElement(UrlChecker, { onCheckComplete: this.urlSend }),
                React.createElement(UrlResultContainer, { shortUrl: this.state.shortUrl, message: this.state.message })
            );
        }
    });

    let UrlChecker = React.createClass({
        displayName: 'UrlChecker',

        getInitialState: function () {
            return { longUrlInput: '' };
        },
        handleChangeUrl: function (e) {
            this.setState({ longUrlInput: e.target.value });
        },
        checkUrlIfCorrect: function () {
            let longUrl = this.state.longUrlInput;
            let urlParts;
            if (longUrl.length === 0) {
                alert(messages['emptyUrlMessage']);return 0;
            } else {
                urlParts = regexp.exec(longUrl);
                if (+urlParts === 0) {
                    alert(messages['incorrectUrl']);return 0;
                }
                this.props.onCheckComplete({ longUrl: longUrl });
            }
        },
        render: function () {
            return React.createElement(
                'div',
                null,
                React.createElement(
                    'div',
                    { className: 'row' },
                    React.createElement('input', { type: 'text', className: 'UrlInput', onChange: this.handleChangeUrl })
                ),
                React.createElement(
                    'div',
                    { className: 'row' },
                    React.createElement(
                        'button',
                        { onClick: this.checkUrlIfCorrect, id: 'Shorten' },
                        '\u0421\u0433\u0435\u043D\u0435\u0440\u0438\u0440\u043E\u0432\u0430\u0442\u044C'
                    )
                )
            );
        }
    });

    let UrlResultContainer = React.createClass({
        displayName: 'UrlResultContainer',

        render: function () {
            return React.createElement(
                'div',
                { className: 'row' },
                React.createElement('input', { type: 'text', className: 'UrlOutput', value: this.props.shortUrl }),
                React.createElement(
                    'span',
                    { className: 'message' },
                    this.props.message
                )
            );
        }
    });

    ReactDOM.render(React.createElement(UrlShortener, null), document.getElementById('UrlShortener'));
})();