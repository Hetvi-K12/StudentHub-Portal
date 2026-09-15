document.getElementById("registractionform").addEventListener("submit", function (event) {

    event.preventDefault();

    let firstname = document.getElementById("firstname").value;
    let lastname = document.getElementById("lastname").value;
    let date = document.getElementById("date").value;
    let month = document.getElementById("month").value;
    let year = document.getElementById("year").value;
    let mobile = document.getElementById("mobile").value;
    let email = document.getElementById("email").value;
    let password = document.getElementById("password").value;
    let city = document.getElementById("city").value;
    let address = document.getElementById("address").value;

    // Name validation
    if (firstname == "" || middlename == "" || lastname == "") {
        alert("Please enter your full name");
        return;
    }

    // Date validation
    if (date == "" || month == "" || year == "") {
        alert("Please enter your date of birth");
        return;
    }

    // Mobile validation
    let mobilePattern = /^[0-9]{10}$/;

    if (!mobilePattern.test(mobile)) {
        alert("Please enter a valid 10 digit mobile number");
        return;
    }

    // Email validation
    let emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;

    if (!emailPattern.test(email)) {
        alert("Please enter a valid email");
        return;
    }

    // Password validation
    let passwordPattern = /^(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#$%^&*]).{8,}$/;


    if (!passwordPattern.test(password)) {

        alert("Please enter strong password");

        document.getElementById("password").focus();
        return;
    }


    // Confirm Password validation
    let confirmPassword = document.getElementById("confirmPassword").value;
    if (password !== confirmPassword) {
        alert("Passwords and confirm password do not match");
        return;
    }

    alert("Registration successful!");

});