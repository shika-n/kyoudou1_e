function changeSortOrder(orderSelect) {
	console.log(orderSelect.value);
	fetch("api/set_sort_order.php", {
		method: "POST",
		body: JSON.stringify({
			"sort_order": orderSelect.value
		})
	}).then((response) => response.json())
	.then((json) => {
		if (json.message == "OK") {
			window.location.reload();
		} else {
			console.error(json.message);
		}
	});
}
