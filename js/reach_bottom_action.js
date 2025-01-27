let currentPage = 2;
let loading = false;
function loadNextPosts() {
    if (loading) return;
    loading = true;
    fetch(`api/get_posts.php?page=${currentPage}`)
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
