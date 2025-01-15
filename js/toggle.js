const navMenu = document.getElementById("navMenu");

let ignoreHideNavOnce = false;

window.addEventListener("click", (_) => {
	if (ignoreHideNavOnce) {
		ignoreHideNavOnce = false;
		return;
	}
	if (navMenu.classList.contains("flex")) {
		navMenu.classList.add("hidden");
		navMenu.classList.remove("flex");
	}
});

function toggleNavMenu() {
	navMenu.classList.toggle("hidden");
	navMenu.classList.toggle("flex");
	ignoreHideNavOnce = true;
}

