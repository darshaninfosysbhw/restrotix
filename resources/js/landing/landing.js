document.addEventListener('DOMContentLoaded', function () {
    const monthlyBtn = document.getElementById('monthly-btn');
    const yearlyBtn = document.getElementById('yearly-btn');
    const toggleSlider = document.getElementById('toggle-slider');
    const pricingCards = Array.from(document.querySelectorAll('[data-pricing-card]'));
    const checkoutLinks = Array.from(document.querySelectorAll('[data-checkout-link]'));

    const formatAmount = (value) => {
        const amount = Number.parseFloat(value);
        if (!Number.isFinite(amount)) {
            return 'N/A';
        }

        return new Intl.NumberFormat('en-IN', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
        }).format(amount);
    };

    const setBillingCycle = (cycle) => {
        const isYearly = cycle === 'yearly';

        if (toggleSlider) {
            toggleSlider.style.transform = isYearly ? 'translateX(100%)' : 'translateX(0)';
        }

        if (monthlyBtn) {
            monthlyBtn.classList.toggle('text-orange-600', !isYearly);
            monthlyBtn.setAttribute('aria-pressed', String(!isYearly));
        }

        if (yearlyBtn) {
            yearlyBtn.classList.toggle('text-orange-600', isYearly);
            yearlyBtn.setAttribute('aria-pressed', String(isYearly));
        }

        pricingCards.forEach((card) => {
            const priceNode = card.querySelector('[data-plan-price]');
            if (!priceNode) return;

            const suffixNode = card.querySelector('[data-plan-price-suffix]');
            const symbol = priceNode.dataset.currencySymbol || '₹';
            const monthlyPrice = Number.parseFloat(priceNode.dataset.monthlyPrice || '');
            const yearlyPrice = Number.parseFloat(priceNode.dataset.yearlyPrice || '');
            const fallbackYearlyPrice = Number.isFinite(monthlyPrice) ? monthlyPrice * 10 : NaN;
            const amount = isYearly
                ? (Number.isFinite(yearlyPrice) ? yearlyPrice : fallbackYearlyPrice)
                : monthlyPrice;

            priceNode.textContent = Number.isFinite(amount)
                ? `${symbol} ${formatAmount(amount)}`
                : 'N/A';

            if (suffixNode) {
                suffixNode.textContent = isYearly ? '/year' : '/month';
            }
        });

        checkoutLinks.forEach((link) => {
            const baseUrl = link.dataset.checkoutBaseUrl;
            if (!baseUrl) return;

            const url = new URL(baseUrl, window.location.origin);
            url.searchParams.set('billing_cycle', cycle);
            link.href = url.toString();
        });
    };

    if (monthlyBtn && yearlyBtn) {
        monthlyBtn.addEventListener('click', function (event) {
            event.preventDefault();
            setBillingCycle('monthly');
        });

        yearlyBtn.addEventListener('click', function (event) {
            event.preventDefault();
            setBillingCycle('yearly');
        });

        setBillingCycle('monthly');
    }



    // Auto-order toggle
    const autoOrderToggle = document.getElementById('auto-order');
    if (autoOrderToggle) {
        autoOrderToggle.addEventListener('change', function () {
            const label = document.querySelector('.toggle-label');
            if (!label) return;

            if (this.checked) {
                label.classList.remove('bg-gray-300');
                label.classList.add('toggle-checked');
            } else {
                label.classList.remove('toggle-checked');
                label.classList.add('bg-gray-300');
            }
        });
    }

    // Mobile Menu Toggle
    const mobileMenuButton = document.getElementById('mobile-menu-button');
    const closeMenuButton = document.getElementById('close-menu');
    const mobileMenu = document.getElementById('mobile-menu');

    if (mobileMenuButton && closeMenuButton && mobileMenu) {
        const openMenu = () => {
            mobileMenu.classList.add('active');
            document.body.style.overflow = 'hidden';
            mobileMenuButton.setAttribute('aria-expanded', 'true');
        };

        const closeMenu = () => {
            mobileMenu.classList.remove('active');
            document.body.style.overflow = 'auto';
            mobileMenuButton.setAttribute('aria-expanded', 'false');
        };

        mobileMenuButton.addEventListener('click', openMenu);

        closeMenuButton.addEventListener('click', closeMenu);

        // Close menu when clicking on a link
        const mobileLinks = mobileMenu.querySelectorAll('a');
        mobileLinks.forEach(link => {
            link.addEventListener('click', function () {
                closeMenu();
            });
        });

        // Close menu on desktop resize
        window.addEventListener('resize', function () {
            if (window.innerWidth > 768) {
                closeMenu();
            }
        });
    }

    // Smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();

            const targetId = this.getAttribute('href');
            if (targetId === '#') return;

            const targetElement = document.querySelector(targetId);
            if (targetElement) {
                window.scrollTo({
                    top: targetElement.offsetTop - 80,
                    behavior: 'smooth'
                });
            }
        });
    });

    // Add hover effect to pricing cards on desktop only
    if (window.innerWidth > 768) {
        const pricingCards = document.querySelectorAll('.card-hover');
        pricingCards.forEach(card => {
            card.addEventListener('mouseenter', function () {
                this.style.transform = 'translateY(-8px)';
                this.style.boxShadow = '0 20px 40px rgba(0, 0, 0, 0.15)';
            });

            card.addEventListener('mouseleave', function () {
                this.style.transform = 'translateY(0)';
                this.style.boxShadow = '';
            });
        });
    }

    // Simulate real-time updates for the landing dashboard preview only
    const dashboardPreview = document.querySelector('[data-dashboard-preview]');

    function updateDashboardStats() {
        if (!dashboardPreview) return;

        const todayStat = dashboardPreview.querySelector('[data-dashboard-stat="today"]');
        const weekStat = dashboardPreview.querySelector('[data-dashboard-stat="week"]');

        if (todayStat && weekStat) {
            // Simulate small fluctuations
            const todayChange = (Math.random() * 2000 - 1000).toFixed(0);
            const weekChange = (Math.random() * 5000 - 2500).toFixed(0);

            const todayValue = 42850 + parseInt(todayChange);
            const weekValue = 298400 + parseInt(weekChange);

            todayStat.textContent = `$${todayValue.toLocaleString()}`;
            weekStat.textContent = `$${weekValue.toLocaleString()}`;
        }
    }

    // Update stats every 10 seconds
    setInterval(updateDashboardStats, 10000);
});
