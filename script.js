const showpass = document.getElementById("show-password")
const showpassP = document.querySelector(".show-password")
function showpassword() {
    if(showpass.type == "password") {
        showpass.type = "text"
        showpassP.innerHTML = "Ocultar senha"
    } else if(showpass.type == "text") {
        showpass.type = "password"
        showpassP.innerHTML = "Mostrar senha"
    }
}
function toggleMenu() {
    const menu = document.getElementById("menuVertical");
    menu.classList.toggle("minimizado");
}