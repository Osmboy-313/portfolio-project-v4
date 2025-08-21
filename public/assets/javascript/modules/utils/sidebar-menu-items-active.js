export function sideBarMenuItems() {

    let menuItems = document.querySelectorAll("#sidebar ul li");

    if (!menuItems) return;
    
    menuItems.forEach(item => {
        item.addEventListener("click", function () {
            menuItems.forEach(li => li.classList.remove("active"));
            this.classList.add("active");
        });
    });


}