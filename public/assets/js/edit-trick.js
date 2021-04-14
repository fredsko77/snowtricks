const updatePoster = id => {
    event.preventDefault();
    // Récupérer l'image 
    const file = event.target.files[0];
    filename = file.filename;

    let url = `/admin/api/trick/edit/poster/${id}`

    // Instancier FormData
    const formData = new FormData();

    if (file && filename !== "") {

        // Ajouter l'image fans le FormData
        formData.append('poster', file, filename);

        axios
            .post(url, formData)
            .then(({ data }) => {
                if (data.hasOwnProperty('message')) {
                    flash(data.message.content, data.message.type);
                }
                if (data.hasOwnProperty('uploadedFile')) {
                    let holder = document.querySelector('#trick-poster');
                    holder.style.backgroundImage = `url('/../../${data.uploadedFile}')`;
                }
            })

        return true;
    }

    return flash('Aucun fichier reçu');
}

const updateImage = id => {
    event.preventDefault();
    // Récupérer l'image 
    const file = event.target.files[0];
    filename = file.filename;

    let url = `/admin/api/trick/edit/image/${id}`

    // Instancier FormData
    const formData = new FormData();

    if (file && filename !== "") {

        // Ajouter l'image fans le FormData
        formData.append('file', file, filename);

        axios
            .post(url, formData)
            .then(({ data }) => {
                if (data.hasOwnProperty('message')) {
                    flash(data.message.content, data.message.type);
                }
                if (data.hasOwnProperty('uploadedFile')) {
                    loadImage(`#trick_image_${id}`, data.uploadedFile);
                }
            })

        return true;
    }

    return flash('Aucun fichier reçu');
}

const createImage = id => {
    event.preventDefault();
    // Récupérer l'image 
    const file = event.target.files[0];
    filename = file.filename;

    let url = `/admin/api/trick/create/image/${id}`

    // Instancier FormData
    const formData = new FormData();

    if (file && filename !== "") {

        // Ajouter l'image fans le FormData
        formData.append('file', file, filename);

        axios
            .post(url, formData)
            .then(({ data }) => {
                if (data.hasOwnProperty('message')) {
                    flash(data.message.content, data.message.type);
                }
                if (data.hasOwnProperty('image')) {
                    let newImageContainer = addItem();
                    let item = document.createElement('div');
                    let html = `
                        <img 
                            class="image-owl-item" 
                            id="trick_image_${data.image.id}" 
                            src="/../../${data.image.path}" 
                            alt="Image de la figure" 
                            srcset="/../../${data.image.path}"
                        >
                        <input 
                            type="file" 
                            class="hidden" 
                            name="image" 
                            id="image_${data.image.id}" 
                            onchange="updateImage(${data.image.id})"
                        >
                        <span class="actions-btn">
                            <label for="image_${data.image.id}" title="Modifier cette image">
                                <i class="icofont-ui-edit mr-2"></i>
                            </label>
                            <i class="icofont-ui-delete" onclick="deleteImage(${data.image.id})"></i>
                        </span>`;

                    item.classList.add('item', `trick_image_${data.image.id}`);
                    item.innerHTML = html;

                    newImageContainer.appendChild(item);
                }
            })

        return true;
    }

    return flash('Aucun fichier reçu');
}

const loadImage = (selector, image) => {
    const imageContainer = document.querySelector(selector);
    imageContainer.src = `/../../${image}`;

    return imageContainer.onload = () => imageContainer.srcset = `/../../${image}`;
}

const loadVideo = (video) => {
    const iframe = document.querySelector(`#trick_video_${video.id}`);
    return iframe.src = video.url;
}

const addItem = () => {
    const carousel = document.querySelector('.owl-carousel');
    const item = carousel.querySelector('#add-media');
    const target = item.parentNode;
    let clone = item.parentNode.cloneNode();

    const owlStage = document.querySelector('.owl-stage');
    let width = parseInt(owlStage.style.width.replace('px', '')) + parseInt(clone.style.width.replace('px', ''));
    owlStage.style.width = `${width}px`;

    target.parentNode.insertBefore(clone, target);
    clone.classList.remove('active');

    return clone;
}

const createVideo = (form, e) => {
    e.preventDefault();

    const url = form.action;
    let video = form.querySelector('[name=url]').value;

    axios
        .post(url, { url: video })
        .then(({ data }) => {
            if (data.hasOwnProperty('message')) {
                flash(data.message.content, data.message.type);
            }
            if (data.hasOwnProperty('video')) {
                const newVideoContainer = addItem();
                let item = document.createElement('div');
                let html = `
                    <iframe 
                        class="trick-video" 
                        id="trick_video_${data.video.id}"
                        src="${data.video.url}" 
                        type="text/html" 
                        frameborder="0" 
                        allow="autoplay; fullscreen; picture-in-picture" 
                        allowfullscreen
                    ></iframe>
                    <span class="actions-btn">
                        <i class="icofont-ui-edit" 
                            title="Modifier cette vidéo" 
                            data-action="/admin/api/trick/edit/video/${data.video.id}" 
                            data-video="${data.video.id}" 
                            data-url="${data.video.url}" 
                            onclick="hydrateForm(this)" 
                            data-dialog="editVideo"
                        ></i>
                        <i class="icofont-ui-delete" onclick="deleteVideo(${data.video.id})"></i>
                    </span> 
                `;

                item.classList.add('item', `trick_video_${data.video.id}`);
                item.innerHTML = html;

                newVideoContainer.appendChild(item);
            }
        })

}

const editVideo = (form, e) => {
    e.preventDefault();

    const url = form.action;
    let video = form.querySelector('[name=url]').value;

    axios
        .post(url, { url: video })
        .then(({ data }) => {
            if (data.hasOwnProperty('message')) {
                flash(data.message.content, data.message.type);
            }
            if (data.hasOwnProperty('video')) {
                const video = data.video;
                loadVideo(video);
            }
        });

}

const hydrateForm = (el) => {
    const id = el.dataset.video;
    const url = el.dataset.url;
    const action = el.dataset.action;
    const dialog = document.querySelector(`[aria-dialog="${el.dataset.dialog}"]`);

    const form = dialog.querySelector('form');
    form.action = action;
    form.querySelector('#video-id').innerHTML = `#${id}`;
    form.querySelector('[name=url]').value = url;

    return dialog.classList.replace('fade', 'show');
}

const deleteImage = (id) => {
    x = confirm('Voulez vous vraiment supprimer cette image ? ');
    const url = `/admin/api/trick/delete/image/${id}`;

    if (x === true) {
        axios
            .delete(url)
            .then(({ data, status }) => {
                if (status === 200) {
                    deleteItem(`.trick_image_${id}`);
                }
                if (data.hasOwnProperty('message')) {
                    flash(data.message.content, data.message.type);
                }
            })
            .catch(({ response }) => {
                console.log(response)
            });
    }
}

const deleteItem = (selector) => {
    // Supprimer l'élément dans le carousel
    const item = document.querySelector(selector).parentNode;

    return item.remove();
}

const deletePoster = (id) => {
    x = confirm('Voulez vous vraiment supprimer cette vidéo ? ');
    const url = `/admin/api/trick/delete/poster/${id}`;

    if (x === true) {
        axios
            .delete(url)
            .then(({ data, status }) => {
                if (status === 200) {
                    let holder = document.querySelector('#trick-poster');
                    holder.style.backgroundImage = '';
                }
                if (data.hasOwnProperty('message')) {
                    flash(data.message.content, data.message.type);
                }
            })
            .catch(({ response }) => {
                console.log(response)
            });
    }
}

const deleteVideo = (id) => {
    x = confirm('Voulez vous vraiment supprimer cette vidéo ? ');
    const url = `/admin/api/trick/delete/video/${id}`;

    if (x === true) {
        axios
            .delete(url)
            .then(({ data, status }) => {
                if (status === 200) {
                    deleteItem(`.trick_video_${id}`);
                }
                if (data.hasOwnProperty('message')) {
                    flash(data.message.content, data.message.type);
                }
            })
            .catch(({ response }) => {
                console.log(response)
            });
    }
}

const editTrick = (id) => {

    const url = `/admin/api/trick/edit/${id}`;

    const data = {
        group: document.getElementById('group').value,
        description: document.getElementById('description').value,
    };

    axios.post(url, data).then(({ data }) => {
        if (data.hasOwnProperty('message')) {
            flash(data.message.content, data.message.type);
        }
    })

    return console.log({ data, url });

}