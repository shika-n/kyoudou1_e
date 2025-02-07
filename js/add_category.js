let tempCategoryArr = [];
let categoryArr = [];

const initialCategories = document.querySelectorAll("input[name='categoryIds[]']");
initialCategories.forEach(elem => categoryArr.push(document.getElementById(elem.value)));

    function openCategoryWindow() {
        document.getElementById("categoryWindow").classList.toggle("hidden");
		tempCategoryArr = [];
        let categories = document.querySelectorAll(".category");
        categories.forEach(cat => {
			if (categoryArr.find((e) => e === cat)) {
				tempCategoryArr.push(cat);
				cat.classList.add("bg-blue-500", "text-white");
			} else {
                cat.classList.remove("bg-blue-500", "text-white");
            }
        });
    }

    function closeCategoryWindow() {
        document.getElementById("categoryWindow").classList.add("hidden");
    }

    function selectCategory(element) {
	console.log(tempCategoryArr);
		elementIndex = tempCategoryArr.findIndex((val) => {
			return val === element;
		});
		if (elementIndex != -1) {
			const removedElements = tempCategoryArr.splice(elementIndex, 1);
			if (removedElements.length > 0) {
				removedElements[0].classList.remove("bg-blue-500", "text-white");
			}
		} else {
			tempCategoryArr.push(element);
			element.classList.add("bg-blue-500", "text-white");
		}
    }

    function chooseCategory() {
        const selectedCategory = document.getElementById("selectedCategory");
		const hiddenCategoryInputs = document.getElementById("hiddenCategoryInputs");
		selectedCategory.textContent = "";
		hiddenCategoryInputs.innerHTML = "";

		categoryArr = tempCategoryArr;

		categoryArr.forEach((cat, i) => {
			hiddenCategoryInputs.innerHTML += `
				<input type="hidden" name="categoryIds[]" value="${cat.id}">
			`;
			if (i > 0) {
				selectedCategory.textContent += ", ";
			}
			selectedCategory.textContent += cat.textContent;
		});

        closeCategoryWindow();
    }

    function cancelSelection() {
        closeCategoryWindow();
    }
