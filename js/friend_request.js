document.querySelectorAll(".accept-btn").forEach(button => {
    button.addEventListener("click", function() {
        const targetId = this.getAttribute("data-user-id");
        const buttonElement = this;
        const params = new URLSearchParams({
            "request_from_user_id": targetId
        });
        fetch("api/friend_accept.php?" + params)
        .then (response => response.json())
        .then(data =>{
            console.log("サーバーからのレスポンス:", data);
            if(data.message === "OK") {
                document.getElementById("user" + targetId).remove();
            }
        })
        
    })
});
document.querySelectorAll(".deny-btn").forEach(button => {
    button.addEventListener("click", function() {
        const targetId = this.getAttribute("data-user-id");
        const buttonElement = this;
        const params = new URLSearchParams({
            "request_from_user_id": targetId
        });
        fetch("api/friend_reject.php?" + params)
        .then (response => response.json())
        .then(data =>{
            console.log("サーバーからのレスポンス:", data);
            if(data.message === "OK") {
                document.getElementById("user" + targetId).remove();
            }
        })
        
    })
});