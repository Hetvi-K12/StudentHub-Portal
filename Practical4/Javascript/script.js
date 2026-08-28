// HOME PAGE
if (document.title === "StudentHub Portal") {

    setTimeout(function () {
        alert("Welcome to StudentHub Portal!");
    },800);

}


// LOGIN PAGE
let loginForm = document.getElementById("loginForm");

if (loginForm) {

    loginForm.addEventListener("submit", function(event) {

        event.preventDefault();

        let username = document.getElementById("username").value.trim();
        let password = document.getElementById("password").value.trim();

        let message = document.getElementById("message");

        let usernameRegex = /^25DCE\d{3}$/;
        let passwordRegex = /^charusat@\d{3}$/;

        if (usernameRegex.test(username) &&
            passwordRegex.test(password)) {

            message.innerHTML = "Login successful!";
            message.style.color = "green";

            setTimeout(function() {

                window.location.href = "dashboard.html";

            }, 200);

        }

        else {

            message.innerHTML = "Incorrect username or password!";
            message.style.color = "red";

        }

    });

}


// REGISTER PAGE
let registerForm = document.querySelector("form[action='dashboard.html']");

if (registerForm) {

    registerForm.addEventListener("submit", function(event) {

        let inputs = registerForm.querySelectorAll("input");

        let password = inputs[8].value;
        let confirmPassword = inputs[9].value;

        if (password !== confirmPassword) {

            event.preventDefault();

            alert("Passwords do not match!");

        } else {

            alert("Registration successful!");

        }

    });

}


// ABOUT PAGE
let featureList = document.querySelector("ul");

if (featureList) {

    let featureButton = document.createElement("button");

    featureButton.innerHTML = "Show / Hide Features";

    featureButton.style.margin = "20px";

    featureList.parentNode.insertBefore(
        featureButton,
        featureList
    );

    featureButton.addEventListener("click", function() {

        if (featureList.style.display === "none") {

            featureList.style.display = "block";

        } else {

            featureList.style.display = "none";

        }

    });

}


// CONTACT PAGE
let contactTextarea = document.querySelector("textarea");

if (contactTextarea) {

    let contactButton = document.querySelector("button");

    contactButton.addEventListener("click", function() {

        if (contactTextarea.value.trim() === "") {

            alert("Please enter your message.");

        } else {

            alert("Your message has been sent successfully!");

            contactTextarea.value = "";

        }

    });

}


// PROFILE PAGE
let profileButton = document.querySelector("button");

if (profileButton && document.querySelector("pre")) {

    profileButton.addEventListener("click", function() {

        let name = prompt("Enter your name:");

        if (name !== null && name.trim() !== "") {

            document.querySelector("pre").innerHTML =
                "<strong>Name         :</strong> " + name;

            alert("Profile updated successfully!");

        }

    });

}


// ASSIGNMENT PAGE
let assignmentTable = document.querySelector("#assignmentTable");

if (assignmentTable) {

    let rows = assignmentTable.querySelectorAll("tr");

    rows.forEach(function(row) {

        let cells = row.querySelectorAll("td");

        if (cells.length > 0) {

            let status = cells[3].innerText.trim();

            if (status === "Pending") {

                row.classList.add("pending");

            }

            if (status === "Submitted") {

                cells[3].classList.add("submitted");

            }

            if (status === "Done Late") {

                cells[3].classList.add("late");

            }

        }

    });

}


// COURSES PAGE
let materialLinks = document.querySelectorAll(".course-material");

if (materialLinks.length > 0) {

    materialLinks.forEach(function(link) {

        link.addEventListener("click", function(event) {

            event.preventDefault();

            alert("Study material will be available soon!");

        });

    });

}


// RESULT PAGE
let resultTable = document.querySelector("#resultTable");

if (resultTable) {

    let resultButton = document.getElementById("printResult");

    resultButton.addEventListener("click", function() {

        window.print();

    });

}


// TIMETABLE PAGE
let timetable = document.querySelector("#timetable");

if (timetable) {

    let today = new Date().getDay();

    if (today >= 1 && today <= 5) {

        let rows = timetable.querySelectorAll("tr");

        rows.forEach(function(row) {

            let cells = row.querySelectorAll("th, td");

            if (cells[today]) {

                cells[today].classList.add("today");

            }

        });

    }

}