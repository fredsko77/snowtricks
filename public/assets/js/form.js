const getCookie = cname => {
    var name = cname + "=";
    var decodedCookie = decodeURIComponent(document.cookie);
    var ca = decodedCookie.split(';');
    for(var i = 0; i <ca.length; i++) {
         var c = ca[i];
         while (c.charAt(0) == ' ') {
              c = c.substring(1);
         }
         if (c.indexOf(name) == 0) {
              return c.substring(name.length, c.length);
         }
    }
    return "";
}

const getValues = selector => {
    let object = {};
    elements = document.querySelectorAll(selector);
    elements.forEach(element => {
         object[element.name] = element.value;
    });
    return object;
}


const flash = (message, type = 'success', close = true) => {
    close = close === true ? `<button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                   </button>` : '';
    let alert =    `<div class="alert alert-${type} alert-dismissible fade show" role="alert">
                        <strong>${message}</strong>
                        ${close}
                   </div>`;
    document.querySelector('.flash').innerHTML = alert;
}


const loadUploadedImage = (elt, event) => {
    let imageContainer = document.querySelector('#uploaded-img');
    let errorMessage = document.querySelector('#error-upload');
    let acceptedFile = ["gif", "jpg", "jpeg", "png", "svg"];     
    if (elt.files && elt.files[0]) {
         let filename = elt.files[0].name;
         let extension = (filename.split('.')[1]).toLowerCase();
         if (acceptedFile.includes(extension)) {
              imageContainer.classList.remove('hidden');
              imageContainer.src = URL.createObjectURL(event.target.files[0]); 
              imageContainer.srcset = URL.createObjectURL(event.target.files[0]); 
              imageContainer.onload = () => URL.revokeObjectURL(imageContainer.src); // Free memory 
              errorMessage.classList.add('hidden');
              errorMessage.innerHTML = '';
         } else {
              imageContainer.classList.add('hidden');
              errorMessage.classList.remove('hidden');
              errorMessage.innerHTML = `Seuls les fichiers .gif, .jpg, .jpeg, .png ou .svg sont acceptés, votre fichier est fichier <i>.${extension}</i>`
         }
    } else {
         imageContainer.classList.add('hidden');
    }
}