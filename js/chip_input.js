const chipsField = document.getElementById("chipsField");
const chipInput = document.getElementById("search-field");

function chip(value) {
	return `
		<div>
			<input type="hidden" name="tags[]" value="${value}">
			<span class="chips flex items-center gap-1 py-1 pl-2 pr-1 bg-gray-300 rounded-full min-w-0">
				${value}
				<button type="button" onclick="deleteChip(this)" class="chips p-1 rounded-full hover:bg-gray-200 w-5 h-5">
					<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-3">
						<path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
					</svg>
				</button>
			</span>
		</div>
	`;
}

function convertToChip() {
	if (chipInput.value.trim().length > 0) {
		chipInput.insertAdjacentHTML("beforebegin", chip(chipInput.value.trim()));
		chipInput.value = "";
	}
}

chipInput.addEventListener("keydown", (e) => {
	if (e.key == " " || e.key == "Enter") {
		e.preventDefault();
		convertToChip();
	} else if (e.key == "Backspace") {
		if (chipInput.value.length == 0) {
			e.preventDefault();
			const prevSibling = chipInput.previousElementSibling;
			if (prevSibling != chipsField.children[0]) {
				chipInput.value = prevSibling.children[0].value;
				prevSibling.remove();
			}
		}
	}
});

function deleteChip(element) {
	element.parentElement.parentElement.remove();
}
