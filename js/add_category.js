let lastSelectedCategory = document.getElementById("selectedCategory").textContent; // chosen category in the form
//console.log("last=",lastSelectedCategory);
let lastId = document.getElementById("categoryId").textContent;
    let tempSelectedCategory = lastSelectedCategory; // selected category in the category window
    let tempId = lastId;
    function openCategoryWindow() {
        document.getElementById("categoryWindow").classList.toggle("hidden");
        let categories = document.querySelectorAll(".category");
        categories.forEach(cat => {
			if (cat.textContent.includes(lastSelectedCategory)) {
				cat.classList.add("bg-blue-500", "text-white");
			} else {
                cat.classList.remove("bg-blue-500", "text-white");
            }
        });
        tempSelectedCategory = lastSelectedCategory;
        tempId = lastId;
        //console.log("temp=", tempSelectedCategory);
        //console.log("last=",lastSelectedCategory);
    }

    function closeCategoryWindow() {
        document.getElementById("categoryWindow").classList.add("hidden");
    }

    function selectCategory(element) {
        let categories = document.querySelectorAll(".category");
        categories.forEach(cat => cat.classList.remove("bg-blue-500", "text-white"));
        element.classList.add("bg-blue-500", "text-white");
		tempSelectedCategory = element.textContent;
        tempId = element.id;
    }

    function chooseCategory() {
        lastSelectedCategory = tempSelectedCategory;
        lastId = tempId;
        document.getElementById("selectedCategory").textContent = lastSelectedCategory;
        document.getElementById("categoryId").value = lastId;
        document.getElementById("categoryId").setAttribute("value", lastId);
        closeCategoryWindow();
    }

    function cancelSelection() {
        tempSelectedCategory = lastSelectedCategory;
        tempId = lastId;
        closeCategoryWindow();
    }