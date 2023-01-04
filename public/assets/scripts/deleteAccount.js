let deleteAccountBtn = document.querySelector('#deleteAccount');
if (deleteAccountBtn) {
    deleteAccountBtn.addEventListener('click', e => {
       let oldText = deleteAccountBtn.innerHTML;
       let loadingText = `<div class="spinner-border text-danger" role="status"></div>`;

        let headers = new Headers({
            "Content-Type": "application/json; charset=UTF-8",
        });

        let data = JSON.stringify({
            'confirmed': true,
        })

       fetch(
           '/security/delete-account', {
               method: 'POST',
               headers: headers,
               body: data,
           }
       )
    });
}