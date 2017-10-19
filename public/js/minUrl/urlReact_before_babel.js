var CRSFTOKEN = document.getElementById('crsft').getAttribute('content');
var REGEXP = /^(https?:\/\/)([\w\.]+)([\/]?.*)$/; 
var STATUSCODE = {
    200 : 'Успешно!',
    400: 'Некорректный Url.',
    410: 'Данный Url не активен.',
    411: 'Введите Url.',
    500: 'Ошибка записи в БД.'   
};  

var UrlShortener = React.createClass ({
    getInitialState: function () {
        return { shortUrl: '', message: '' };
    },
    urlSend: function(checkUrlResult) {
        this.setState({ shortUrl: '', message: '' });
        var thisComponent = this;
        axios.post(
            'minUrl/new', 
            { 'longUrl': checkUrlResult['longUrl'] }, 
            {  headers: { 
                    'Content-Type':  'application/json',
                    'X-CSRF-TOKEN':  CRSFTOKEN,
                }
        })
        .then(function (response) {
            let statusCode = 200;
            let message, shortUrl;
            message = shortUrl = '';
            let respdata = JSON.parse( response.request.response );
            if (respdata['status']===200) {
                shortUrl = respdata['shortUrl'];
            } else {
                statusCode = +respdata['status'];
            }
            message = STATUSCODE[statusCode];
            thisComponent.setState({ message: message });
            thisComponent.setState({ shortUrl: shortUrl });
        })
        .catch(function (error) {
        console.log(error);
        });
    },           
    render: function() {
        return (
            <div>
                <UrlChecker onCheckComplete={this.urlSend} />
                <UrlResultContainer shortUrl={this.state.shortUrl} message={this.state.message} />
            </div>      
        );
    }
});

var UrlChecker =  React.createClass ({
    getInitialState: function () {
        return { longUrlInput: '' };
    },
    handleChangeUrl: function(e) {
        this.setState({ longUrlInput: e.target.value });
    },
    checkUrlIfCorrect: function() {
        let longUrl = this.state.longUrlInput;
        let urlParts;
        if(longUrl.length===0) {
            alert(STATUSCODE[411]); return 0;
        } else {
            urlParts = REGEXP.exec(longUrl);
            if(+urlParts===0) {
                alert(STATUSCODE[400]); return 0;
            }
            this.props.onCheckComplete({longUrl: longUrl});
        }           
    }, 
    render: function() { 
        return (
            <div>
                <div className='row'>
                    <input type='text' className='UrlInput' onChange={this.handleChangeUrl}></input>
                </div>
                <div className='row'>
                    <button onClick={this.checkUrlIfCorrect} id='Shorten'>Сгенерировать</button>            
                </div>
            </div>
        );
    }
});

var UrlResultContainer =  React.createClass({
    render: function() {
        return (    
            <div>
                <input type='text' className='UrlOutput' value={this.props.shortUrl}></input>
                <span className='message'>{this.props.message}</span>
            </div>
        );
    }
});
        
ReactDOM.render(<UrlShortener />, document.getElementById('UrlShortener'));