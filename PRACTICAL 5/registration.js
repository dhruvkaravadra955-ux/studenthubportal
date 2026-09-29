let name = document.getElementById("name");
let email = document.getElementById("email");
let mobile = document.getElementById("mobile");
let password = document.getElementById("password");
let confirm = document.getElementById("confirm");
let course = document.getElementById("course");
let year = document.getElementById("year");
let register = document.getElementById("register");

register.addEventListener("click", function() {

    let valid = true;

    let namePattern = /^[A-Za-z ]+$/;
    let emailPattern = /^[a-zA-Z0-9._]+@[a-zA-Z0-9]+\.[a-zA-Z]+$/;
    let mobilePattern = /^[0-9]{10}$/;
    let passwordPattern = /^(?=.*[A-Za-z])(?=.*[0-9]).{6,}$/;

    document.getElementById("nameError").innerHTML = "";
    document.getElementById("emailError").innerHTML = "";
    document.getElementById("mobileError").innerHTML = "";
    document.getElementById("passwordError").innerHTML = "";
    document.getElementById("confirmError").innerHTML = "";

    if (name.value == "") {
        document.getElementById("nameError").innerHTML = "Enter name";
        valid = false;
    }
    else if (!namePattern.test(name.value)) {
        document.getElementById("nameError").innerHTML = "Enter valid name";
        valid = false;
    }

    if (email.value == "") {
        document.getElementById("emailError").innerHTML = "Enter email";
        valid = false;
    }
    else if (!emailPattern.test(email.value)) {
        document.getElementById("emailError").innerHTML = "Enter valid email";
        valid = false;
    }

    if (mobile.value == "") {
        document.getElementById("mobileError").innerHTML = "Enter mobile number";
        valid = false;
    }
    else if (!mobilePattern.test(mobile.value)) {
        document.getElementById("mobileError").innerHTML = "Enter 10 digit number";
        valid = false;
    }

    if (password.value == "") {
        document.getElementById("passwordError").innerHTML = "Enter password";
        valid = false;
    }
    else if (!passwordPattern.test(password.value)) {
        document.getElementById("passwordError").innerHTML =
            "Password must have 6 characters and a number";
        valid = false;
    }

    if (confirm.value == "") {
        document.getElementById("confirmError").innerHTML =
            "Confirm password";
        valid = false;
    }
    else if (password.value != confirm.value) {
        document.getElementById("confirmError").innerHTML =
            "Password does not match";
        valid = false;
    }

    if (course.value == "") {
        document.getElementById("courseError").innerHTML =
            "Select course";
        valid = false;
    }

    if (year.value == "") {
        document.getElementById("yearError").innerHTML =
            "Select year";
        valid = false;
    }

    if (valid == true) {
        document.getElementById("success").innerHTML =
            "Registration successful";
    }
});