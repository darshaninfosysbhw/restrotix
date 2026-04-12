<div id="toastContainer" class="fixed top-5 right-5 z-50 flex flex-col space-y-2 w-96 max-w-[92vw]"></div>

<script>
    // 1. Function ko seedhe window par assign karein, bina kisi wrapper ke
    window.showToast = function(obj) {
        const {
            type = 'info', message = '', duration = 3000
        } = obj;

        const container = document.getElementById('toastContainer');
        if (!container) {
            console.error("Toast Container nahi mila!");
            return;
        }
        if (!message) return;

        const bgColors = {
            success: 'bg-green-500',
            error: 'bg-red-500',
            warning: 'bg-yellow-500',
            info: 'bg-blue-500'
        };
        const icons = {
            success: '✔',
            error: '❌',
            warning: '⚠',
            info: 'ℹ'
        };

        const safeDuration = Math.max(Number(duration) || 3000, 1000);
        const item = document.createElement('div');
        item.className = 'flex flex-col opacity-0 translate-x-10 transition-all duration-300 mb-3';

        const toast = document.createElement('div');
        toast.className =
            `${bgColors[type] || bgColors.info} px-4 py-2.5 rounded-t shadow-lg flex justify-between items-start gap-3`;
        toast.style.color = '#ffffff';

        toast.innerHTML = `
            <div class="flex items-center space-x-2">
                <span class="text-lg">${icons[type] || icons.info}</span>
                <span class="text-sm leading-5 font-medium">${message}</span>
            </div>
            <button type="button" class="close-toast font-bold text-xl opacity-70 hover:opacity-100 transition-opacity">&times;</button>
        `;

        const progressWrap = document.createElement('div');
        progressWrap.className = 'w-full h-1 rounded-b overflow-hidden bg-black bg-opacity-20';

        const progress = document.createElement('div');
        progress.className = 'h-full bg-white bg-opacity-80';
        progress.style.width = '100%';
        progress.style.transition = `width ${safeDuration}ms linear`;

        progressWrap.appendChild(progress);
        item.appendChild(toast);
        item.appendChild(progressWrap);
        container.appendChild(item);

        // Animations
        setTimeout(() => {
            item.classList.remove('opacity-0', 'translate-x-10');
            item.classList.add('opacity-100', 'translate-x-0');
        }, 10);

        setTimeout(() => {
            progress.style.width = '0%';
        }, 50);

        const removeToast = () => {
            item.classList.add('opacity-0', 'translate-x-10');
            setTimeout(() => item.remove(), 300);
        };

        const timer = setTimeout(removeToast, safeDuration);

        item.querySelector('.close-toast').addEventListener('click', () => {
            clearTimeout(timer);
            removeToast();
        });
    };

    // 2. Laravel Session check - isko alag block mein rakhein taaki main function disturb na ho
    try {
        const sessionData = {!! json_encode(session('toast', [])) !!};
        if (Array.isArray(sessionData) && sessionData.length > 0) {
            sessionData.forEach(t => window.showToast(t));
        }
    } catch (e) {
        console.log("No session toasts found.");
    }
</script>
