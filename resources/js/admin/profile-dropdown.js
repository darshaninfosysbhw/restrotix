// Profile Dropdown Logic
const profileBtn = document.getElementById("profileBtn");
const profileMenu = document.getElementById("profileMenu");

if (profileBtn && profileMenu) {
    const positionProfileMenu = () => {
        if (profileMenu.classList.contains("hidden")) return;

        const buttonRect = profileBtn.getBoundingClientRect();
        profileMenu.style.position = "fixed";
        profileMenu.style.top = `${buttonRect.bottom + 12}px`;
        profileMenu.style.right = `${Math.max(window.innerWidth - buttonRect.right, 8)}px`;
        profileMenu.style.left = "auto";
        profileMenu.style.marginTop = "0";
        profileMenu.style.zIndex = "2147483647";
    };

    document.body.appendChild(profileMenu);

    profileBtn.addEventListener("click", (e) => {
        e.stopPropagation();
        profileMenu.classList.toggle("hidden");
        positionProfileMenu();
    });

    window.addEventListener("click", (e) => {
        if (!profileBtn.contains(e.target) && !profileMenu.contains(e.target)) {
            profileMenu.classList.add("hidden");
        }
    });

    window.addEventListener("resize", positionProfileMenu);
}
