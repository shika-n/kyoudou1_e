const charCounterElement = document.getElementById("charCounter");
const textAreaElement = document.getElementById("content");

charCounterElement.textContent = textAreaElement.textLength + "/8192";
textAreaElement.addEventListener("input", (_) => {
	charCounterElement.textContent = textAreaElement.textLength + "/8192";
	if (textAreaElement.textLength > 8192) {
		charCounterElement.style.color = "red";
	} else {
		charCounterElement.style.color = "black";
	}
});