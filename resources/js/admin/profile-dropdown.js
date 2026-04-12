
// Profile Dropdown Logic
const profileBtn = document.getElementById('profileBtn');
const profileMenu = document.getElementById('profileMenu');

if (profileBtn && profileMenu) {
    profileBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        profileMenu.classList.toggle('hidden');
    });

    // Bahar click karne par dropdown band ho jaye
    window.addEventListener('click', (e) => {
        if (!profileBtn.contains(e.target)) {
            profileMenu.classList.add('hidden');
        }
    });
}
