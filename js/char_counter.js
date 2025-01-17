const charCounterElement = document.getElementById("charCounter");
const textAreaElement = document.getElementById("content");

charCounterElement.textContent = textAreaElement.textLength + "/255";
textAreaElement.addEventListener("input", (_) => {
	charCounterElement.textContent = textAreaElement.textLength + "/255";
	if (textAreaElement.textLength > 255) {
		charCounterElement.style.color = "red";
	} else {
		charCounterElement.style.color = "black";
	}
});