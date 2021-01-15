
const handleRegister = (form,e) => {
    e.preventDefault();
    let url = form.action;
    let data = { ...getValues('.form-control') }
    
    axios
        .post( url, data )
        .then( ({data}) => {
            console.log(data);
            if ( data.hasOwnProperty('message') ) {
                let type = data.message.type; 
                let message = data.message.content;
                flash(message, type, true);
                if (data.hasOwnProperty('url')) {
                    let url = data.url;
                    let delay = 2000; 
                    // Faire une redirection sur la page d'édition de l'article
                    setTimeout(() => window.location = url , delay);
                }                
            } 
       })
       .catch( ({response}) => {
            let type = response.data.message.type; 
            let message = response.data.message.content;    
            if ((response.status).toString().indexOf('4') === 0) flash(message, type, true);
            if ((response.status).toString().indexOf('5') === 0) flash(message, type, true);
       }) 
}