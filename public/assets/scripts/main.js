let year = document.querySelectorAll('.current-year-range');
year.forEach(span => {
    let year = new Date().getFullYear();
    let startYear = 2022;
    if (year > startYear) span.innerHTML = `${startYear} - ${year}`;else span.innerHTML = startYear.toString();
});