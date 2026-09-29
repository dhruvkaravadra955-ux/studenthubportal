let name = document.getElementById("name");
let email = document.getElementById("email");

let submit = document.getElementById("submit");
let popup = document.getElementById("popup");
let close = document.getElementById("close");

let message = document.getElementById("message");

let theme = document.getElementById("theme");


// Submit
submit.addEventListener("click", function() {

    if (name.value == "" || email.value == "") {

        message.innerHTML = "Please enter all details";

    }
    else {

        message.innerHTML = "Details are valid";

        popup.style.display = "block";
    }

});


// Close popup
close.addEventListener("click", function() {

    popup.style.display = "none";

});


// Dark / Light Mode
theme.addEventListener("click", function() {

    document.body.classList.toggle("dark");

    if (document.body.classList.contains("dark")) {

        theme.innerHTML = "Light Mode";

        localStorage.setItem("theme", "dark");

    }
    else {

        theme.innerHTML = "Dark Mode";

        localStorage.setItem("theme", "light");

    }

});


// Remember theme
if (localStorage.getItem("theme") == "dark") {

    document.body.classList.add("dark");

    theme.innerHTML = "Light Mode";

}