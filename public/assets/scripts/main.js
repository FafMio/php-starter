'use strict';

let year = document.querySelectorAll('.current-year-range');
year.forEach(span => {
    let year = new Date().getFullYear();
    let startYear = 2022;
    if (year > startYear) span.innerHTML = `${startYear} - ${year}`; else span.innerHTML = startYear.toString();
});


const forms = document.querySelectorAll('.needs-validation')
forms.forEach(form => {
    console.log("New form registered !")
    form.addEventListener('submit', event => {
        if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
        }

        form.classList.add('was-validated')
    }, false);
})