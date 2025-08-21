
export function sidebar() {
    
    let toggleSidebarBtn = document.querySelector("#toggle-sidebar");
    let sidebar = document.querySelector("#sidebar");

    if (!toggleSidebarBtn) return;

    toggleSidebarBtn.addEventListener("click", function () {
        sidebar.classList.toggle("hidden");
    });
}
