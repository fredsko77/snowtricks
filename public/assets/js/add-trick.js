const previewPoster = (event) => {
    let image = event.target.files[0];
    let imageHolder = document.getElementById('uploaded-trick-poster');
    let src = URL.createObjectURL(image);

    imageHolder.src = src;
    imageHolder.classList.remove('hidden');

    return imageHolder.onload = () => URL.revokeObjectURL(imageHolder.src);
}

const previewImages = (event) => {
    let images = event.target.files;

    for (const image of images) {
        let imagesContainer = document.querySelector('.trick-images-holder');
        let img = document.createElement('img');
        img.classList.add('image-trick');

        let src = URL.createObjectURL(image);
        img.setAttribute('src', src);
        img.onload = () => URL.revokeObjectURL(img.src);

        imagesContainer.appendChild(img);
    }
}

const addInput = () => {
    const holder = document.getElementById('input-videos');
    const counter = holder.querySelectorAll('.video').length;
    const id = parseInt(counter) + 1;

    let newRow = document.createElement('div');
    newRow.classList.add('row', 'video', 'mb-1', 'align-items-baseline');
    newRow.setAttribute('data-video', id);

    let html = `
        <input id="trick_videos_${id}" class="form-control col-11" type="text" name="videos[]">
        <span class="col-1">
            <i class="icofont-close cursor-pointer font-weight-bold" title="Supprimer cette tache" onclick="deleteInput(${id})"></i>
        </span>
    `;

    newRow.innerHTML = html;
    return holder.appendChild(newRow);
}

const deleteInput = (id) => {
    const video = document.querySelector(`[data-video="${id}"]`);
    video.remove();

    const listVideos = document.querySelectorAll('#input-videos .video');
    let i = 0;

    listVideos.forEach(videos => {
        i++;
        videos.setAttribute('data-video', i);
        videos.querySelector('input').setAttribute('id', `trick_videos_${i}`);
        videos.querySelector('i').setAttribute('onclick', `deleteInput(${i})`)
    });

    return 'end';
}

const handleTrick = (form, e) => {
    e.preventDefault();

    const data = new FormData(form);
    const url = form.action;

    axios
        .post(url, data)
        .then(({ data }) => {
            if (data.hasOwnProperty('message')) {
                flash(data.message.content, data.message.type);
            }
            if (data.hasOwnProperty('url')) {
                setTimeout(() => {
                    window.location = data.url;
                }, 2500);
            }
        })

}