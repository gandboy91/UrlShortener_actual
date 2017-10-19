'use strict';

var CRSFTOKEN = document.getElementById('crsft').getAttribute('content');
var REGEXP = /^(https?:\/\/)([\w\.]+)([\/]?.*)$/;
var STATUSCODE = {
    200: 'Успешно!',
    400: 'Некорректный Url.',
    410: 'Данный Url не активен.',
    411: 'Введите Url.',
    500: 'Ошибка записи в БД.'
};

var UrlShortener = React.createClass({
    displayName: 'UrlShortener',

    getInitialState: function getInitialState() {
        return { shortUrl: '', message: '' };
    },
    urlSend: function urlSend(checkUrlResult) {
        this.setState({ shortUrl: '', message: '' });
        var thisComponent = this;
        axios.post('minUrl/new', { 'longUrl': checkUrlResult['longUrl'] }, { headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CRSFTOKEN
            }
        }).then(function (response) {
            var statusCode = 200;
            var message = void 0,
                shortUrl = void 0;
            message = shortUrl = '';
            var respdata = JSON.parse(response.request.response);
            if (respdata['status'] === 200) {
                shortUrl = respdata['shortUrl'];
            } else {
                statusCode = +respdata['status'];
            }
            message = STATUSCODE[statusCode];
            thisComponent.setState({ message: message });
            thisComponent.setState({ shortUrl: shortUrl });
        }).catch(function (error) {
            console.log(error);
        });
    },
    render: function render() {
        return React.createElement(
            'div',
            null,
            React.createElement(UrlChecker, { onCheckComplete: this.urlSend }),
            React.createElement(UrlResultContainer, { shortUrl: this.state.shortUrl, message: this.state.message })
        );
    }
});

var UrlChecker = React.createClass({
    displayName: 'UrlChecker',

    getInitialState: function getInitialState() {
        return { longUrlInput: '' };
    },
    handleChangeUrl: function handleChangeUrl(e) {
        this.setState({ longUrlInput: e.target.value });
    },
    checkUrlIfCorrect: function checkUrlIfCorrect() {
        var longUrl = this.state.longUrlInput;
        var urlParts = void 0;
        if (longUrl.length === 0) {
            alert(STATUSCODE[411]);return 0;
        } else {
            urlParts = REGEXP.exec(longUrl);
            if (+urlParts === 0) {
                alert(STATUSCODE[400]);return 0;
            }
            this.props.onCheckComplete({ longUrl: longUrl });
        }
    },
    render: function render() {
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

var UrlResultContainer = React.createClass({
    displayName: 'UrlResultContainer',

    render: function render() {
        return React.createElement(
            'div',
            null,
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