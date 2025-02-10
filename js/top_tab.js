function changeTopTab(e, type) {
	const contentPanel = document.getElementById("content");
	const postPanels = contentPanel.querySelectorAll(".post-panel");
	postPanels.forEach(e => e.remove());

	const tabButtons = document.querySelectorAll(".tab-button");
	tabButtons.forEach(e => {
		e.classList.remove("border-b-4", "border-b-blue-400");
	});
	
	reachBottomActionQuery.set("type", type);
	const params = new URLSearchParams({
		"type": type
	});
	e.target.classList.add("border-b-4", "border-b-blue-400");
	fetch("api/get_posts.php?" + params)
		.then((response) => response.text())
		.then((text) => {
			contentPanel.innerHTML += text;
			resetCurrentPage(); // reach_bottom_action.js
		});
}
