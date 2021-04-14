const flash = (message, type = 'success', close = true) => {
    close = close === true ? `<button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                   </button>` : '';
    let alert = `<div class="alert alert-${type} alert-dismissible fade show" role="alert">
                        <strong>${message}</strong>
                        ${close}
                   </div>`;
    document.querySelector('.flash').innerHTML = alert;

    setTimeout(() => {
        document.querySelector('[role="alert"]').classList.replace('show', 'hide')
    }, 5000);
}