export function dropdown() {

    let toggleDropdown = document.querySelector("#user-dropdown");
    let dropdown = document.querySelector("#main-dropdown");

    if (!toggleDropdown) return;
    toggleDropdown.addEventListener('click', function () {
        dropdown.classList.toggle("show");
    });

    document.addEventListener("click", function (e) {
        if (!toggleDropdown.contains(e.target)) {
            dropdown.classList.remove("show");
        }
    })

}