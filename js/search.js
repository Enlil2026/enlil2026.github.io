function filterBooks() {
    let input = document.getElementById('bookSearch').value.toLowerCase();
    let container = document.getElementById('bookGrid');
    let links = container.getElementsByTagName('a');

    for (let i = 0; i < links.length; i++) {
        let img = links[i].getElementsByTagName('img')[0];
        if (img) {
            let title = img.getAttribute('alt').toLowerCase();
            // Shows/hides the entire link (the book cover)
            links[i].style.display = title.includes(input) ? "" : "none";
        }
    }
}