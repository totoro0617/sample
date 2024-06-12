function togglePasswordVisibility() { //console.log("見える見えない");
    let passwordInput = document.getElementById("inputPassword");
    let passwordInput2 = document.getElementById("inputPassword2");
    let showPasswordCheckbox = document.getElementById("showPassword");

    if (showPasswordCheckbox.checked) {
        passwordInput.type = "text";
        passwordInput2.type = "text";
    } else {
        passwordInput.type = "password";
        passwordInput2.type = "password";
    }
}

// function togglePasswordVisibility2() {
//     let passwordInput = document.getElementById("inputPassword2");
//     let showPasswordCheckbox = document.getElementById("showPassword");

//     if (showPasswordCheckbox.checked) {
//         passwordInput.type = "text";
//     } else {
//         passwordInput.type = "password";
//     }
// }