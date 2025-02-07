const imageSelectInput = document.getElementById("image-select");
const postContent = document.getElementById("post_content");
const selectImageButton = document.getElementById("select-image-button");

function selectImage() {
	imageSelectInput.click();
}

imageSelectInput.addEventListener("change", (e) => {
	selectImageButton.setAttribute("disabled", true);
	selectImageButton.textContent = "アップロード中";
	if (imageSelectInput.files[0] != undefined) {
		const formData = new FormData();
		formData.append("file", imageSelectInput.files[0]);

		fetch("api/upload_image.php", {
			method: "POST",
			body: formData
		}).then((response) => response.json())
		.then((json) => {
			if (json.message == "OK") {
				postContent.value += `\n\n![](post_images/${json.filepath})`
			} else {
				alert(json.message);
			}
			selectImageButton.removeAttribute("disabled");
			selectImageButton.textContent = "画像を投入";
		});
	}
});
