const scrollToTop = () => window.scrollTo({ top: 0, behavior: 'smooth' });

const displayScrollTop = () => {
    let scrollBtn = document.querySelector("#scrollTop");
    let target = window.innerHeight * 1.7;
    let scroll = document.querySelector('html').scrollTop;
    let bottom = parseInt(scroll + innerHeight);
    if ( bottom > target ) {
        return scrollBtn.classList.remove("hidden");
    }
    return scrollBtn.classList.add("hidden");
}

const changePage = () => {
    // window.lo
    document.querySelector('[data-page]');
}

const stickHeader = () => {
    let scrollTop = document.querySelector('html').scrollTop;
    let navBar = document.querySelector('#navTop');
    if ( scrollTop > window.innerHeight ) {
        return navBar.style.position = "fixed";
    }
    return navBar.style.position = "static";
}

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

const loadMore = btn => {
    let page = getUrlParams().page !== undefined ? getUrlParams().page : btn.dataset.page;
    let tricks = document.querySelectorAll('.trick');
    let nextPage = parseInt(page) + 1;
    let start = parseInt(nextPage) * 15;
    let end = (start + 15) > tricks.length ? tricks.length : (start + 15);
    for (let index = start; index < end; index++) {
        tricks[index].classList.remove('hidden')        
    }
    pushState('page', nextPage)
}

jQuery(function($){ 
    $('.owl-carousel').owlCarousel({
        margin: 10,
        responsiveClass: true,
        touchDrag: true,
        mouseDrag: true,
        autoplay: false,
        dots: true,
        loop: false,
        responsive:{
            0: {
                items: 1,
            },
            300: {
                items: 3,
            },
            430: {
                items: 4,
            },
            600: {
                items: 6,
            },
            1000: {
                items: 9,
            },
            1200: {
                items: 10,
            },
        }
    });
});