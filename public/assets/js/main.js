const scrollToTop = () => window.scrollTo({ top: 0, behavior: 'smooth' });

const getUrlParams = () => {
    let params = {};
    let url = (window.location.search).split('?').pop();
    let queries = url.split('&');
    queries.forEach(query => {
        let parts = query.split('=');
        params[parts[0]] = parts[1];
    })
    return params;
}

const pushState = (key, value) => {
    let url = new URL(window.location);
    url.searchParams.set(key, value);
    window.history.pushState({}, '', url);
}

const handleComment = (form, e) => {
    e.preventDefault();

    const comment = form.querySelector('#comment').value
    const url = form.action;

    axios
        .post(url, { comment })
        .then(({ data }) => {
            if (data.hasOwnProperty('message')) {
                flash(data.message.content, data.message.type);
            }
            if (data.hasOwnProperty('comment')) {
                loadComment(data.comment, data.connected, 'before')
            }
        }).catch(({ response }) => {
            console.log(response)
        });

}

const deleteComment = (comment, e) => {
    e.preventDefault();
    const url = comment.href;
    const x = confirm('Voulez-vous supprimer ce commentaire ?');

    if (x === true) {
        axios.delete(url).then(({ data }) => {
            if (data.hasOwnProperty('message')) {
                flash(data.message.content, data.message.type);
            }
            if (data.hasOwnProperty('comment')) {
                deleteCommentElement(data.comment);
            }
        });
    }
}

const deleteCommentElement = (comment) => {
    document.querySelector(`[data-comment="${comment}"]`).remove();
    const countElement = document.getElementById('count-comments');
    let count = parseInt(countElement.innerText) - 1;

    return countElement.innerText = count;
}

const loadComment = ({ user, created_at, id, comment }, connected, pos = 'after') => {
    const container = document.querySelector('.trick-comments');
    const newComment = document.createElement('div');
    let commentHTML = '<div class="comment-user">';
    let deleteButton = '';
    newComment.classList.add('comment');
    newComment.setAttribute('data-comment', id);

    if (connected !== null && user.id === connected.id) {
        deleteButton = `<a href="/admin/comments/delete/${id}" class="btn btn-link" onclick="deleteComment(this,event)">Supprimer</a>`
    }

    if (pos !== 'after') {
        const countElement = document.getElementById('count-comments');
        let count = parseInt(countElement.innerText) + 1;

        countElement.innerText = count;
    }


    let userImage = `
        <div class="alt-user">
            <i class="icofont-ui-user"></i>
        </div>`;

    if (user.image !== null) {
        userImage = `<img class="comment-img" src="/../${user.image}" alt="Poster de l'utilisateur du commentaire" srcset="/../${user.image}">`
    }

    commentHTML +=
        `${userImage}
    </div>
    <div class="comment-content">
    <p class="comment-author">${user.pseudo}</p>
        <p class="comment-text">
            ${comment}
        </p>
        <div class="comment-metas">
            <small>Publié le ${formatDate(created_at.date)}</small>
            ${deleteButton}
        </div>
    </div>`;

    newComment.innerHTML = commentHTML;

    if (pos === 'after') {
        return container.appendChild(newComment);
    }

    return container.prepend(newComment);

}

const formatDate = (date) => {
    date = new Date(date);

    const day = date.getDate() < 10 ? `0${date.getDate()}` : date.getDate();
    let month = parseInt(date.getMonth()) + 1;
    month = month < 10 ? `0${month}` : month;
    const year = date.getFullYear();

    const formatedDate = `${day}-${month}-${year}`;

    return formatedDate;
}

const loading = () => {
    const container = document.getElementById('loader-container');
    const loader = document.createElement('div');
    loader.classList.add('loader');

    container.classList.remove('hidden');

    return container.appendChild(loader);
}

const stopLoading = () => {
    const container = document.getElementById('loader-container');

    container.classList.add('hidden');

    return container.innerHTML = "";
}

const loadMoreComments = (el) => {
    loading();

    let page = parseInt(el.dataset.page);
    const baseUrl = el.dataset.url;
    const url = `${baseUrl}?page=${page}`;

    axios
        .get(url)
        .then(({ data }) => {
            if (data.hasOwnProperty('comments')) {
                for (const comment of data.comments) {
                    loadComment(comment, data.connected);
                }
            }
            if (data.hasOwnProperty('last')) {
                if (data.last === false) {
                    page = page + 1;
                    el.setAttribute('data-page', page);
                }
                if (data.last === true) {
                    el.parentNode.remove();
                }
            }
        }).catch(({ response }) => {
            console.log(response)
        });

    stopLoading();
}

const displayCarousel = (el) => {
    document.querySelector('.owl-carousel.owl-loaded').style.cssText = 'display: block !important';
    return el.parentNode.remove();
}