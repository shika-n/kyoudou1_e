let currentPage = 2;
let loading = false;
let reachBottomActionSourceUrl = "api/get_posts.php";
let reachBottomActionTargetContainerId = "content";
const reachBottomActionQuery = new URLSearchParams();

function resetCurrentPage() {
	currentPage = 2;
}

function loadNextPosts() {
    if (loading) return;
    loading = true;

	const url = new URL(window.location.href);
	const params = url.searchParams;

	reachBottomActionQuery.set("page", currentPage);
	
	if (url.pathname.endsWith("profile.php")) {
		if (params.get("id")) {
			reachBottomActionQuery.append("id", params.get("id"));
		} else {
			reachBottomActionQuery.append("id", -1);
		}
	}

    fetch(reachBottomActionSourceUrl + "?" + reachBottomActionQuery)
    .then(response => response.text())
    .then(html => {
        loading = false;
		if (html == "") return;
        const content = document.getElementById(reachBottomActionTargetContainerId);
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
