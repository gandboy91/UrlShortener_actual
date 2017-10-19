;(function () {
    const crsfToken = document.getElementById('crsft').getAttribute('content');
    const redirectMessage = document.getElementById('msg');
    const regexp = /^(https?:\/\/)([\w\.]+)([\/]?.*)$/;
    const messages = {
        'successMessage': 'Успешно!',
        'emptyUrlMessage': 'Введите url!',
        'incorrectUrl': 'Проверьте корректность введенного url!' };
    const urlInputLabel = 'Введите оригинальный URL с указанием протокола http(s)://';

    let UrlShortener = React.createClass({
        displayName: 'UrlShortener',

        getInitialState: function () {
            return { shortUrl: '', message: '' };
        },
        urlSend: function (checkUrlResult) {
            this.setState({ shortUrl: '', message: '', status: '' });
            let thisComponent = this;
            axios.post('minUrl/new', { 'longUrl': checkUrlResult['longUrl'] }, { headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': crsfToken
                }
            }).then(function (response) {
                let shortUrl = '';
                let respdata = JSON.parse(response.request.response);
                shortUrl = respdata['shortUrl'];
                thisComponent.setState({ message: messages['successMessage'], status: 'success' });
                thisComponent.setState({ shortUrl: shortUrl });
            }).catch(function (error) {
                if (errorContainer = JSON.parse(error.response.request.response)) {
                    thisComponent.setState({ message: errorContainer['error'], status: 'warning' });
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
                React.createElement(UrlResultContainer, { shortUrl: this.state.shortUrl, status: this.state.status, message: this.state.message })
            );
        }
    });

    let UrlChecker = React.createClass({
        displayName: 'UrlChecker',

        getInitialState: function () {
            return { longUrlInput: '', validUrl: '', errorText: '' };
        },
        handleChangeUrl: function (e) {
            this.setState({ longUrlInput: e.target.value });
        },
        checkUrlIfCorrect: function () {
            redirectMessage.textContent = '';
            let longUrl = this.state.longUrlInput;
            let urlParts;
            if (longUrl.length === 0) {
                this.setState({ validUrl: 'is-invalid', 'errorText': messages['emptyUrlMessage'] });
                return 0;
            } else {
                urlParts = regexp.exec(longUrl);
                if (+urlParts === 0) {
                    this.setState({ validUrl: 'is-invalid', 'errorText': messages['incorrectUrl'] });
                    return 0;
                }
                this.setState({ validUrl: 'is-valid', errorText: '' });
                this.props.onCheckComplete({ longUrl: longUrl });
            }
        },
        render: function () {
            return React.createElement(
                'div',
                { className: 'mb-3' },
                React.createElement(
                    'div',
                    { className: 'row' },
                    React.createElement(
                        'div',
                        { className: 'col-sm-8' },
                        React.createElement('input', { type: 'text', placeholder: urlInputLabel, id: 'UrlInput', className: `form-control ${this.state.validUrl}`, onChange: this.handleChangeUrl })
                    ),
                    React.createElement(
                        'div',
                        { className: 'col-sm-4' },
                        React.createElement(
                            'button',
                            { onClick: this.checkUrlIfCorrect, type: 'button', className: 'btn btn-outline-success' },
                            React.createElement(
                                'b',
                                null,
                                'go!'
                            )
                        )
                    )
                ),
                React.createElement(
                    'div',
                    { className: 'row invalid-feedback' },
                    this.state.errorText
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
                React.createElement(
                    'div',
                    { className: 'input-group col-sm-8' },
                    React.createElement(
                        'div',
                        { className: 'input-group-addon text-success' },
                        '\xA0',
                        React.createElement(
                            'b',
                            null,
                            'short url:'
                        ),
                        '\xA0'
                    ),
                    React.createElement('input', { type: 'text', className: 'form-control font-weight-bold', value: this.props.shortUrl })
                ),
                React.createElement(
                    'div',
                    { className: `col-sm-4 message text-${this.props.status}` },
                    this.props.message
                )
            );
        }
    });

    ReactDOM.render(React.createElement(UrlShortener, null), document.getElementById('UrlShortener'));
})();