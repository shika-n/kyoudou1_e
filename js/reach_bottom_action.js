let currentPage = 2;
let loading = false;
function loadNextPosts() {
    if (loading) return;
    loading = true;
    fetch(`api/get_posts.php?page=${currentPage}`)
    .then(response => response.text())
    .then(html => {
        const content = document.getElementById('content');
        content.innerHTML += html;
        console.log(`Fetching page: ${currentPage}`);
        currentPage++;
        loading = false;
    });
}
window.addEventListener('scroll', function() {
    const isAtBottom = window.innerHeight + window.scrollY >= document.body.offsetHeight;
    if (isAtBottom) {
        loadNextPosts();
    }
});
