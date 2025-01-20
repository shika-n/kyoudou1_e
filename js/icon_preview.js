const iconPreview = document.getElementById("preview")
const fileSelectInput = document.getElementById("icon");

fileSelectInput.addEventListener("change", (e) => {
	if (e.currentTarget.files && e.currentTarget.files[0]) {
		let reader = new FileReader();

		reader.onload = (e) => {
			iconPreview.src = e.currentTarget.result;
		};

		reader.readAsDataURL(e.currentTarget.files[0]);
	}
});
