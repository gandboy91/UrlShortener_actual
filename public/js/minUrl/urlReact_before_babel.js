;(function(){
const crsfToken = document.getElementById('crsft').getAttribute('content');
const regexp = /^(https?:\/\/)([\w\.]+)([\/]?.*)$/;
const messages = {
    'successMessage': 'Успешно!',
    'emptyUrlMessage': 'Введите url!',
    'incorrectUrl': 'Проверьте корректность введенного url!' };

let UrlShortener = React.createClass ({
    getInitialState: function () {
        return { shortUrl: '', message: '' };
    },
    urlSend: function(checkUrlResult) {
        this.setState({ shortUrl: '', message: '' });
        let thisComponent = this;
        axios.post(
            'minUrl/new', 
            { 'longUrl': checkUrlResult['longUrl'] }, 
            {  headers: { 
                    'Content-Type':  'application/json',
                    'X-CSRF-TOKEN':  crsfToken,
                }
        })
        .then(function (response) {
            let shortUrl = '';
            let respdata = JSON.parse(response.request.response);
            shortUrl = respdata['shortUrl'];
            thisComponent.setState({ message: messages['successMessage'] });
            thisComponent.setState({ shortUrl: shortUrl });
        }).catch(function (error) {
            if(errorContainer = JSON.parse(error.response.request.response)) {
                thisComponent.setState({ message: errorContainer['error'] });
            } else {
                console.log(error);
            }
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

let UrlChecker =  React.createClass ({
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
            alert(messages['emptyUrlMessage']); return 0;
        } else {
            urlParts = regexp.exec(longUrl);
            if(+urlParts===0) {
                alert(messages['incorrectUrl']); return 0;
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

let UrlResultContainer =  React.createClass({
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
})();