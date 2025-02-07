const searchField = document.getElementById("search-field");
const suggestionList = document.getElementById("suggestion-list");
const searchResult = document.getElementById("searchResult");

let selection = -1;
let suggestions = [];
let mouseOverSuggestion = false;

function searchTags() {
	if (searchField.dataset.enableSearch === undefined) {
		return;
	}
	const query = searchField.value.trim().split(" ");
	const params = new URLSearchParams({
		"query": query,
		"type": "tags"
	});
	reachBottomActionQuery.set("query", query); // reach_bottom_action.js
	fetch("api/get_posts.php?" + params)
		.then((response) => response.text())
		.then((text) => {
			searchResult.innerHTML = text;
			resetCurrentPage(); // reach_bottom_action.js
		});
}

function suggestionItem(value, frequency) {
	return `
		<li class="rounded-md px-1 hover:bg-blue-400/30" onclick="complete('${value}')">
			<div class="flex justify-between gap-2 select-none">
				<span>${value}</span>
				<span>${frequency}</span>
			</div>
		</li>
	`;
}

function updateSelection() {
	for (let i = 0; i < suggestionList.childElementCount; ++i) {
		const listItem = suggestionList.children[i];
		if (i == selection) {
			listItem.classList.add("bg-blue-400");
		} else {
			listItem.classList.remove("bg-blue-400");
		}
	}
}

function updateSuggestionsElements(json) {
	suggestions = json;
	suggestionList.innerHTML = "";
	selection = -1;
	for (let i = 0; i < suggestions.length; ++i) {
		suggestionList.insertAdjacentHTML("beforeend", suggestionItem(suggestions[i]["name"], suggestions[i]["frequency"]));
	}
	if (suggestions.length > 0) {
		suggestionList.classList.remove("hidden");
	} else {
		suggestionList.classList.add("hidden");
	}
}

function complete(value) {
	searchField.value = searchField.value.substring(0, searchField.value.lastIndexOf(" ") + 1) + value + " ";
	updateSuggestionsElements([]);
	searchField.focus();
	if (typeof convertToChip === "function") { // chip_input.js
		convertToChip();
	}
	mouseOverSuggestion = false;
}

function updateSuggestions(searchValue) {
	fetch("api/tag_search.php?search=" + searchValue)
		.then((response) => response.json())
		.then((json) => updateSuggestionsElements(json));
}

searchField.addEventListener("keypress", (e) => {
	let searchValue = searchField.value.substring(searchField.value.lastIndexOf(" ") + 1) + e.key;
	if (e.key == " ") {
		searchValue = "";
	}
	updateSuggestions(searchValue);
});

searchField.addEventListener("keydown", (e) => {
	if (e.key == "ArrowDown") {
		e.preventDefault();
		selection = Math.max(-1, Math.min(selection + 1, suggestions.length - 1));
		updateSelection();
	} else if (e.key == "ArrowUp") {
		e.preventDefault();
		selection = Math.max(-1, Math.min(selection - 1, suggestions.length - 1));
		updateSelection();
	} else if (e.key == "Enter") {
		e.preventDefault();
		if (selection > -1) {
			complete(suggestions[selection]["name"]);
		} else {
			searchTags();
		}
	} else if (e.key == "Backspace") {
		updateSuggestions(searchField.value.substring(searchField.value.lastIndexOf(" ") + 1, searchField.value.length - 1));
	}
});


suggestionList.addEventListener("mouseover", (_) => mouseOverSuggestion = true);
suggestionList.addEventListener("mouseout", (_) => mouseOverSuggestion = false);

searchField.addEventListener("focusout", (_) => {
	selection = -1;
	if (!mouseOverSuggestion) {
		suggestionList.classList.add("hidden");
	}
});
