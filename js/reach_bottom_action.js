let currentPage = 2;
let loading = false;
function loadNextPosts() {
    if (loading) return;
    loading = true;

	const url = new URL(window.location.href);
	const params = url.searchParams;

	let urlToFetch = `api/get_posts.php?page=${currentPage}`;
	if (url.pathname.endsWith("profile.php")) {
		if (params.get("id")) {
			urlToFetch += `&id=${params.get("id")}`;
		} else {
			urlToFetch += `&id=-1`;
		}
	}

    fetch(urlToFetch)
    .then(response => response.text())
    .then(html => {
        loading = false;
		if (html == "") return;
        const content = document.getElementById('content');
        content.innerHTML += html;
        console.log(`Fetching page: ${currentPage}`);
        currentPage++;
    });
}
window.addEventListener('scroll', function() {
    const isAtBottom = Math.ceil(window.innerHeight + window.scrollY + 1) >= document.body.offsetHeight;
    if (isAtBottom) {
        loadNextPosts();
    }
});
