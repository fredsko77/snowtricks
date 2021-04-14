window.onload = () => {
    console.clear();
    // Charger les tricks au chargement de la page en fonction de ?page
    onloadTricks();


}

// window.onpopstate = () => {
//     console.log(getUrlParam('page'))
//     onloadTricks()
// }

const getUrlParam = (key = '') => {
    const queries = (new URL(window.location.href)).searchParams;
    return queries.get(key);
}

const onloadTricks = () => {
    const page = getUrlParam('page');

    if (page !== null && page !== undefined) {
        for (let i = 1; i <= parseInt(page); i++) {
            getTricks(i);
        }
    }
}

const loadMoreTricks = (el) => {
    loading();

    let page = parseInt(el.dataset.page);

    window.history.pushState('', '', `?page=${page}`);

    getTricks(page);

    stopLoading();
}

const getTricks = (page) => {

    const url = `/api/trick/pagination?page=${page}`;
    const btn = document.querySelector('#loadButton');

    axios
        .get(url)
        .then(({ data }) => {
            if (data.hasOwnProperty('tricks')) {
                for (const trick of data.tricks) {
                    loadTrick(trick, data.connected);
                }
            }
            if (data.hasOwnProperty('last')) {
                if (data.last === false) {
                    page = page + 1;
                    btn.setAttribute('data-page', page);
                }
                if (data.last === true) {
                    btn.parentNode.remove();
                }
            }
        })
        .catch(({ response }) => {
            console.log(response);
        });

}

const loadTrick = ({ id, poster, slug, name }, connected = false) => {
    const container = document.querySelector('.tricks-container');
    const newTrick = document.createElement('div');
    let actionsBtn = '';

    if (poster === "" || poster === null) {
        poster = '/assets/img/snowboard-113784_640.jpg';
    }

    if (connected === true) {
        actionsBtn = `
            <span class="actions-btn">
                <a href="/admin/trick/${id}/edit">
                    <i class="icofont-ui-edit"></i>
                </a>
                <form method="post" action="/trick/${id}" onsubmit="return confirm('Are you sure you want to delete this item?');">
                    <input type="hidden" name="_method" value="DELETE">
                    <input type="hidden" name="_token" value="{{ csrf_token('delete' ~ trick.id) }}">
                    <button type="submit" class="delete-btn">
                        <i class="icofont-ui-delete"></i>
                    </button>
                </form>
            </span>
        `;
    }

    let trickHTML = `
        <div class="card-tricks-body">
            <img src="${poster}" alt="Poster du trick" srcset="${poster}" class="card-tricks-img">
        </div>
        <div class="card-tricks-footer">
            <a class="card-tricks-title" href="/trick/${slug}/${id}">${name}</a>
            ${actionsBtn}
        </div>
    `;
    newTrick.classList.add('card-tricks');

    newTrick.innerHTML = trickHTML;

    return container.appendChild(newTrick);
}