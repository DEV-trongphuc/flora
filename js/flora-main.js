/* ==========================================================================
   FLORA DENTAL CLINIC - INTERACTIVE CONTROLLER (SAFE & ROBUST)
   ========================================================================== */

// Web App URL for Google Sheets Sync
const GOOGLE_SCRIPT_URL = 'https://script.google.com/macros/s/AKfycbwynAqJc_hyqWmMvFz1QEf6c-8ZKyIlexvg_NCOM2fZ8oGLa579QV3hTMJ0BTySuR6YiA/exec';

// ─── COOKIE & URL PARAM HELPERS ───
function getCookie(name) {
    const value = `; ${document.cookie}`;
    const parts = value.split(`; ${name}=`);
    if (parts.length === 2) return parts.pop().split(';').shift();
    return null;
}

function getFbcQueryParam() {
    const urlParams = new URLSearchParams(window.location.search);
    const fbclid = urlParams.get('fbclid');
    if (fbclid) {
        return `fb.1.${Date.now()}.${fbclid}`;
    }
    return null;
}

// Send Lead to Google Sheets via fetch
async function submitLeadToSheets(data) {
    if (!GOOGLE_SCRIPT_URL || GOOGLE_SCRIPT_URL === '' || GOOGLE_SCRIPT_URL.includes('YOUR_GOOGLE_APPS_SCRIPT_URL_HERE')) {
        console.warn('Google Sheets script URL is not configured.');
        return;
    }
    try {
        await fetch(GOOGLE_SCRIPT_URL, {
            method: 'POST',
            mode: 'no-cors',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        });
        console.log('Lead sync OK.');
    } catch (error) {
        console.error('Lead sync failed:', error);
    }
}

// ─── DOM INITS ───
document.addEventListener('DOMContentLoaded', () => {
    
    // 1. Mobile Hamburger Navigation Drawer
    const hamburger = document.getElementById('drawerTrigger');
    const drawerNav = document.getElementById('drawerNav');
    const drawerOverlay = document.getElementById('drawerOverlay');

    if (hamburger && drawerNav && drawerOverlay) {
        const toggleDrawer = () => {
            hamburger.classList.toggle('active');
            drawerNav.classList.toggle('open');
            drawerOverlay.classList.toggle('visible');
            document.body.style.overflow = drawerNav.classList.contains('open') ? 'hidden' : '';
        };

        hamburger.addEventListener('click', toggleDrawer);
        drawerOverlay.addEventListener('click', toggleDrawer);

        // Mobile Accordion Toggle
        const accordionHeader = drawerNav.querySelector('.drawer-accordion-header');
        if (accordionHeader) {
            accordionHeader.addEventListener('click', () => {
                const accordion = accordionHeader.parentElement;
                accordion.classList.toggle('active');
            });
        }

        drawerNav.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', () => {
                hamburger.classList.remove('active');
                drawerNav.classList.remove('open');
                drawerOverlay.classList.remove('visible');
                document.body.style.overflow = '';
            });
        });
    }

    // 2. Sticky Header scroll behaviors
    const backToTopBtn = document.getElementById('backToTopBtn');
    const headerBar = document.querySelector('.header-bar');
    let lastScrollY = window.scrollY;
    let isScrolling = false;

    if (headerBar) {
        if (window.scrollY <= 50) {
            headerBar.classList.add('header-transparent');
        }
        
        window.addEventListener('scroll', () => {
            if (!isScrolling) {
                window.requestAnimationFrame(() => {
                    const currentScroll = window.scrollY;

                    if (currentScroll > 50) {
                        headerBar.classList.remove('header-transparent');
                        headerBar.classList.add('scrolled');
                    } else {
                        headerBar.classList.add('header-transparent');
                        headerBar.classList.remove('scrolled');
                    }

                    if (backToTopBtn) {
                        if (currentScroll > 400) {
                            backToTopBtn.classList.add('visible');
                        } else {
                            backToTopBtn.classList.remove('visible');
                        }
                    }

                    if (currentScroll > 150) {
                        if (currentScroll - lastScrollY > 10) {
                            headerBar.classList.add('header-hidden');
                        } else if (currentScroll - lastScrollY < -10) {
                            headerBar.classList.remove('header-hidden');
                        }
                    } else {
                        headerBar.classList.remove('header-hidden');
                    }

                    lastScrollY = currentScroll;
                    isScrolling = false;
                });
                isScrolling = true;
            }
        }, { passive: true });
    }

    if (backToTopBtn) {
        backToTopBtn.addEventListener('click', (e) => {
            e.preventDefault();
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }

    // 3. Testimonial Slider Carousel Setup
    const track = document.getElementById('testimonialTrack');
    const dotsContainer = document.getElementById('testimonialDots');
    const prevBtn = document.getElementById('sliderPrevBtn');
    const nextBtn = document.getElementById('sliderNextBtn');
    const sliderWrapper = document.querySelector('.slider-wrapper');

    if (track && dotsContainer) {
        const slides = Array.from(track.children);
        let currentIdx = 0;

        slides.forEach((_, idx) => {
            const dot = document.createElement('button');
            dot.className = 'slider-dot' + (idx === 0 ? ' active' : '');
            dot.addEventListener('click', () => {
                moveToSlide(idx);
            });
            dotsContainer.appendChild(dot);
        });

        const dots = Array.from(dotsContainer.children);

        const moveToSlide = (idx) => {
            track.style.transition = 'transform 0.5s cubic-bezier(0.25, 1, 0.5, 1)';
            track.style.transform = `translateX(-${idx * 100}%)`;
            dots[currentIdx].classList.remove('active');
            dots[idx].classList.add('active');
            currentIdx = idx;

            const activeCard = slides[idx].querySelector('.testimonial-card');
            const wrapper = document.querySelector('.slider-wrapper');
            if (activeCard && wrapper) {
                requestAnimationFrame(() => {
                    wrapper.style.height = activeCard.offsetHeight + 'px';
                });
            }
        };

        // Set initial testimonial slider height
        const firstCard = slides[0].querySelector('.testimonial-card');
        const testimonialWrapper = document.querySelector('.slider-wrapper');
        if (firstCard && testimonialWrapper) {
            setTimeout(() => {
                testimonialWrapper.style.height = firstCard.offsetHeight + 'px';
            }, 300);
        }

        window.addEventListener('resize', () => {
            const activeCard = slides[currentIdx].querySelector('.testimonial-card');
            const wrapper = document.querySelector('.slider-wrapper');
            if (activeCard && wrapper) {
                wrapper.style.height = activeCard.offsetHeight + 'px';
            }
        });

        let slideInterval = setInterval(() => {
            moveToSlide((currentIdx + 1) % slides.length);
        }, 5000);

        function resetInterval() {
            clearInterval(slideInterval);
            slideInterval = setInterval(() => {
                moveToSlide((currentIdx + 1) % slides.length);
            }, 5000);
        }

        dots.forEach(dot => {
            dot.addEventListener('click', () => {
                resetInterval();
            });
        });

        if (prevBtn) {
            prevBtn.addEventListener('click', () => {
                moveToSlide((currentIdx - 1 + slides.length) % slides.length);
                resetInterval();
            });
        }

        if (nextBtn) {
            nextBtn.addEventListener('click', () => {
                moveToSlide((currentIdx + 1) % slides.length);
                resetInterval();
            });
        }

        // Swipe & Drag Behavior for Carousel
        if (sliderWrapper) {
            let isDragging = false;
            let startX = 0;

            const dragStart = (event) => {
                isDragging = true;
                startX = event.pageX || event.touches[0].clientX;
                clearInterval(slideInterval);
                sliderWrapper.style.cursor = 'grabbing';
                track.style.transition = 'none';
            };

            const dragMove = (event) => {
                if (!isDragging) return;
                const currentX = event.pageX || event.touches[0].clientX;
                const diffX = currentX - startX;
                track.style.transform = `translateX(calc(-${currentIdx * 100}% + ${diffX}px))`;
            };

            const dragEnd = (event) => {
                if (!isDragging) return;
                isDragging = false;
                sliderWrapper.style.cursor = 'grab';
                const endX = event.pageX || (event.changedTouches && event.changedTouches[0].clientX) || startX;
                const diffX = endX - startX;

                if (diffX < -50) {
                    moveToSlide((currentIdx + 1) % slides.length);
                } else if (diffX > 50) {
                    moveToSlide((currentIdx - 1 + slides.length) % slides.length);
                } else {
                    moveToSlide(currentIdx);
                }
                resetInterval();
            };

            // Touch events
            sliderWrapper.addEventListener('touchstart', dragStart, { passive: true });
            sliderWrapper.addEventListener('touchend', dragEnd);
            sliderWrapper.addEventListener('touchmove', dragMove, { passive: true });

            // Mouse events
            sliderWrapper.addEventListener('mousedown', dragStart);
            sliderWrapper.addEventListener('mouseup', dragEnd);
            sliderWrapper.addEventListener('mouseleave', dragEnd);
            sliderWrapper.addEventListener('mousemove', dragMove);
        }
    }

    // 4. Modal/Popup logic
    const promoPopup = document.getElementById('promoPopup');
    const successPopup = document.getElementById('successPopup');
    const registrationPopup = document.getElementById('registrationPopup');

    // Auto-popup promo modal disabled per request
    /*
    if (promoPopup && successPopup) {
        setTimeout(() => {
            if (!successPopup.classList.contains('active')) {
                promoPopup.classList.add('active');
            }
        }, 12000);
    }
    */

    // Close promo button
    const closePromoBtn = document.getElementById('closePromoBtn');
    if (closePromoBtn && promoPopup) {
        closePromoBtn.addEventListener('click', () => {
            promoPopup.classList.remove('active');
        });
    }

    if (promoPopup) {
        promoPopup.addEventListener('click', (e) => {
            if (e.target === promoPopup) promoPopup.classList.remove('active');
        });
    }

    // Mobile registration popup link behaviors
    const handleRegisterClick = (e) => {
        if (window.innerWidth <= 768) {
            e.preventDefault();
            e.stopPropagation();
            if (promoPopup) promoPopup.classList.remove('active');
            if (registrationPopup) registrationPopup.classList.add('active');
        }
    };

    document.querySelectorAll('a[href="#dang-ky"]').forEach(link => {
        link.addEventListener('click', handleRegisterClick);
    });

    const closeRegistrationBtn = document.getElementById('closeRegistrationBtn');
    if (closeRegistrationBtn && registrationPopup) {
        closeRegistrationBtn.addEventListener('click', () => {
            registrationPopup.classList.remove('active');
        });
    }

    if (registrationPopup) {
        registrationPopup.addEventListener('click', (e) => {
            if (e.target === registrationPopup) {
                registrationPopup.classList.remove('active');
            }
        });
    }

    // Promo registration action button
    const promoRegisterBtn = document.getElementById('promoRegisterBtn');
    if (promoRegisterBtn) {
        promoRegisterBtn.addEventListener('click', (e) => {
            if (window.innerWidth <= 768) {
                handleRegisterClick(e);
            } else {
                if (promoPopup) promoPopup.classList.remove('active');
                const formSection = document.getElementById('dang-ky');
                if (formSection) {
                    const rect = formSection.getBoundingClientRect().top;
                    const offset = window.scrollY + rect - 70;
                    window.scrollTo({
                        top: offset,
                        behavior: 'smooth'
                    });
                }
            }
        });
    }

    // Success popup buttons
    const closeSuccessBtn = document.getElementById('closeSuccessBtn');
    if (closeSuccessBtn && successPopup) {
        closeSuccessBtn.addEventListener('click', () => {
            successPopup.classList.remove('active');
        });
    }

    const successOkBtn = document.getElementById('successOkBtn');
    if (successOkBtn && successPopup) {
        successOkBtn.addEventListener('click', () => {
            successPopup.classList.remove('active');
        });
    }

    if (successPopup) {
        successPopup.addEventListener('click', (e) => {
            if (e.target === successPopup) successPopup.classList.remove('active');
        });
    }

    // Close modals on Escape key
    window.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            if (promoPopup) promoPopup.classList.remove('active');
            if (successPopup) successPopup.classList.remove('active');
            if (registrationPopup) registrationPopup.classList.remove('active');
        }
    });

    // 5. Scroll lock during active modal/overlay toggles
    if (typeof MutationObserver !== 'undefined') {
        const scrollLockObserver = new MutationObserver(() => {
            const activeLocks = document.querySelector('.modal.active') || document.querySelector('.drawer-nav.open');
            document.body.style.overflow = activeLocks ? 'hidden' : '';
        });

        const lockTargets = [promoPopup, successPopup, registrationPopup, drawerNav];
        lockTargets.forEach(target => {
            if (target) {
                scrollLockObserver.observe(target, { attributes: true, attributeFilter: ['class'] });
            }
        });
    }

    // 6. Universal Booking triggers for CTA buttons
    const openBtns = document.querySelectorAll('.btn-booking');
    if (openBtns.length > 0 && promoPopup) {
        openBtns.forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                promoPopup.classList.add('active');
            });
        });
    }

    // ─── FORM SUBMIT HANDLERS ───
    const handleFormSubmit = (e, formElement, formTypeName) => {
        e.preventDefault();
        
        const eventId = 'lead_' + Date.now() + '_' + Math.random().toString(36).substring(2, 9);
        const formData = new FormData(formElement);

        // Safely retrieve form fields by name or fallback IDs
        const fullname = formData.get('name') || (document.getElementById('fullname') ? document.getElementById('fullname').value : '') || (document.getElementById('popupFullname') ? document.getElementById('popupFullname').value : '');
        const phone = formData.get('phone') || (document.getElementById('phone') ? document.getElementById('phone').value : '') || (document.getElementById('popupPhone') ? document.getElementById('popupPhone').value : '');
        const service = formData.get('service') || (document.getElementById('toothStatus') ? document.getElementById('toothStatus').value : '') || (document.getElementById('popupToothStatus') ? document.getElementById('popupToothStatus').value : '');
        const preferredTime = formData.get('preferredTime') || '';

        const birthYear = document.getElementById('birthYear') ? document.getElementById('birthYear').value : (document.getElementById('popupBirthYear') ? document.getElementById('popupBirthYear').value : '');
        const genderEl = document.querySelector('input[name="gender"]:checked') || document.querySelector('input[name="popupGender"]:checked');
        const gender = genderEl ? genderEl.value : '';
        const address = document.getElementById('address') ? document.getElementById('address').value : (document.getElementById('popupAddress') ? document.getElementById('popupAddress').value : '');
        const userNote = document.getElementById('note') ? document.getElementById('note').value : (document.getElementById('popupNote') ? document.getElementById('popupNote').value : '');

        // Collect checkboxed interests if any exist
        const selectedNeeds = [];
        document.querySelectorAll('input[name="need"]:checked, input[name="popupNeed"]:checked').forEach(cb => {
            selectedNeeds.push(cb.value);
        });
        const needStr = selectedNeeds.length > 0 ? selectedNeeds.join(', ') : service;

        const data = {
            formType: formTypeName,
            fullname: fullname,
            phone: phone,
            email: "",
            clinic: service,
            city: address || "Hồ Chí Minh",
            interest: needStr,
            date: new Date().toLocaleDateString('vi-VN'),
            timeSlot: preferredTime || "Bất kỳ lúc nào",
            note: `Năm sinh: ${birthYear} | Giới tính: ${gender} | Ghi chú thêm: ${userNote}`,
            clientUserAgent: navigator.userAgent,
            eventSourceUrl: window.location.href,
            fbp: getCookie('_fbp'),
            fbc: getCookie('_fbc') || getFbcQueryParam(),
            eventId: eventId
        };

        // Submit async in background
        submitLeadToSheets(data);

        // Find submit button in this form to show spinner state
        const submitBtn = formElement.querySelector('button[type="submit"]');
        if (submitBtn) {
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = 'Đang gửi đăng ký... <i class="fa-solid fa-spinner fa-spin" style="margin-left: 8px;"></i>';
            
            setTimeout(() => {
                if (registrationPopup) registrationPopup.classList.remove('active');
                if (promoPopup) promoPopup.classList.remove('active');
                if (successPopup) successPopup.classList.add('active');
                
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
                formElement.reset();
            }, 1800);
        }
    };

    // Bind bottom inline registration form
    const inlineForm = document.getElementById('floraRegistrationForm');
    if (inlineForm) {
        inlineForm.addEventListener('submit', (e) => {
            handleFormSubmit(e, inlineForm, "Đăng ký khám website (Flora Portal)");
        });
    }

    // Bind popup modal registration form
    const popupForm = document.getElementById('popupRegistrationForm');
    if (popupForm) {
        popupForm.addEventListener('submit', (e) => {
            handleFormSubmit(e, popupForm, "Đăng ký khám modal (Flora Portal Popup)");
        });
    }
});

// ─── DYNAMIC ACCORDIONS, ANIMATIONS & SNOWFALL (PAGE-LEVEL SAFE INITS) ───

// Live Countdown Timer (Only runs if elements exist)
const initCountdown = () => {
    const d = document.getElementById('countdown-days');
    const h = document.getElementById('countdown-hours');
    const m = document.getElementById('countdown-minutes');
    const s = document.getElementById('countdown-seconds');
    if (!d || !h || !m || !s) return;

    const targetDate = new Date('2026-07-25T08:30:00+07:00').getTime();

    const updateCountdown = () => {
        const now = new Date().getTime();
        const distance = targetDate - now;

        if (distance < 0) {
            d.innerText = '00';
            h.innerText = '00';
            m.innerText = '00';
            s.innerText = '00';
            return;
        }

        const days = Math.floor(distance / (1000 * 60 * 60 * 24));
        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);

        d.innerText = String(days).padStart(2, '0');
        h.innerText = String(hours).padStart(2, '0');
        m.innerText = String(minutes).padStart(2, '0');
        s.innerText = String(seconds).padStart(2, '0');
    };

    updateCountdown();
    setInterval(updateCountdown, 1000);
};
initCountdown();

// Initialize mobile sliders for grids
function initMobileSliders() {
    const sliders = document.querySelectorAll('.mobile-slider');

    sliders.forEach(slider => {
        if (slider.classList.contains('no-dots')) return;
        const cards = slider.children;
        if (cards.length === 0) return;

        const dotsContainer = document.createElement('div');
        dotsContainer.className = 'mobile-slider-dots';
        slider.parentNode.insertBefore(dotsContainer, slider.nextSibling);

        const dots = [];
        for (let i = 0; i < cards.length; i++) {
            const dot = document.createElement('button');
            dot.className = 'mobile-slider-dot' + (i === 0 ? ' active' : '');
            dot.setAttribute('aria-label', `Trượt tới slide ${i + 1}`);

            dot.addEventListener('click', () => {
                const cardWidth = cards[0].offsetWidth;
                const style = window.getComputedStyle(slider);
                const gap = parseFloat(style.gap) || 16;
                slider.scrollTo({
                    left: i * (cardWidth + gap),
                    behavior: 'smooth'
                });
            });

            dotsContainer.appendChild(dot);
            dots.push(dot);
        }

        let scrollTimeout;
        slider.addEventListener('scroll', () => {
            window.clearTimeout(scrollTimeout);
            scrollTimeout = setTimeout(() => {
                const cardWidth = cards[0].offsetWidth;
                const style = window.getComputedStyle(slider);
                const gap = parseFloat(style.gap) || 16;
                const scrollLeft = slider.scrollLeft;
                const activeIndex = Math.min(
                    Math.round(scrollLeft / (cardWidth + gap)),
                    cards.length - 1
                );

                dots.forEach((dot, idx) => {
                    if (idx === activeIndex) {
                        dot.classList.add('active');
                    } else {
                        dot.classList.remove('active');
                    }
                });
            }, 50);
        }, { passive: true });
    });
}
initMobileSliders();

// Ease counter animation
function animateCounter(el) {
    const target = parseInt(el.getAttribute('data-target'), 10);
    if (!target) return;
    const duration = 2000;
    const startTime = performance.now();

    function update(currentTime) {
        const elapsed = currentTime - startTime;
        const progress = Math.min(elapsed / duration, 1);
        const easeProgress = progress * (2 - progress);
        const currentValue = Math.floor(easeProgress * target);

        el.innerText = currentValue.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.') + '+';

        if (progress < 1) {
            requestAnimationFrame(update);
        } else {
            el.innerText = target.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.') + '+';
        }
    }
    requestAnimationFrame(update);
}

// Scroll Reveal Observer
function initScrollReveal() {
    const reveals = document.querySelectorAll('.reveal, .reveal-left, .reveal-right, .reveal-scale');
    const observerOptions = {
        root: null,
        rootMargin: '0px',
        threshold: 0.1
    };
    const observer = new IntersectionObserver((entries, obs) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('active');
                const counters = entry.target.querySelectorAll('.counter-number');
                counters.forEach(counter => {
                    animateCounter(counter);
                });
                obs.unobserve(entry.target);
            }
        });
    }, observerOptions);

    reveals.forEach(reveal => {
        if (reveal.closest('#banner')) {
            setTimeout(() => {
                reveal.classList.add('active');
                const counters = reveal.querySelectorAll('.counter-number');
                        counters.forEach(counter => {
                            animateCounter(counter);
                        });
            }, 150);
        } else {
            observer.observe(reveal);
        }
    });
}
document.addEventListener('DOMContentLoaded', initScrollReveal);

// FAQ Accordion
function initFAQ() {
    const faqItems = document.querySelectorAll('.faq-item');
    faqItems.forEach(item => {
        const button = item.querySelector('.faq-question');
        const answer = item.querySelector('.faq-answer');
        if (!button || !answer) return;

        // If the item is initially active, expand it
        if (item.classList.contains('active')) {
            answer.style.maxHeight = answer.scrollHeight + 'px';
        }

        button.addEventListener('click', () => {
            const isActive = item.classList.contains('active');

            faqItems.forEach(otherItem => {
                otherItem.classList.remove('active');
                const otherAns = otherItem.querySelector('.faq-answer');
                if (otherAns) otherAns.style.maxHeight = null;
            });

            if (!isActive) {
                item.classList.add('active');
                answer.style.maxHeight = answer.scrollHeight + 'px';
            }
        });
    });
}
document.addEventListener('DOMContentLoaded', initFAQ);

// Gentle Snowfall Effect
function initSnowfall() {
    const canvas = document.getElementById('snow-canvas');
    if (!canvas) return;
    const ctx = canvas.getContext('2d');
    let width = canvas.offsetWidth;
    let height = canvas.offsetHeight;
    canvas.width = width;
    canvas.height = height;

    window.addEventListener('resize', () => {
        width = canvas.offsetWidth;
        height = canvas.offsetHeight;
        canvas.width = width;
        canvas.height = height;
    });

    const maxFlakes = 45;
    const flakes = [];

    for (let i = 0; i < maxFlakes; i++) {
        flakes.push({
            x: Math.random() * width,
            y: Math.random() * height,
            r: Math.random() * 2 + 1,
            speed: Math.random() * 0.4 + 0.15,
            wind: Math.random() * 0.2 - 0.1
        });
    }

    function draw() {
        ctx.clearRect(0, 0, width, height);
        ctx.fillStyle = 'rgba(255, 255, 255, 0.75)';
        ctx.beginPath();
        for (let i = 0; i < maxFlakes; i++) {
            const f = flakes[i];
            ctx.moveTo(f.x, f.y);
            ctx.arc(f.x, f.y, f.r, 0, Math.PI * 2, true);
        }
        ctx.fill();
        update();
        requestAnimationFrame(draw);
    }

    function update() {
        for (let i = 0; i < maxFlakes; i++) {
            const f = flakes[i];
            f.y += f.speed;
            f.x += f.wind;

            if (f.y > height) {
                flakes[i] = {
                    x: Math.random() * width,
                    y: -10,
                    r: f.r,
                    speed: f.speed,
                    wind: f.wind
                };
            }
            if (f.x > width + 5 || f.x < -5) {
                f.x = f.x > width ? -5 : width + 5;
            }
        }
    }
    draw();
}
document.addEventListener('DOMContentLoaded', initSnowfall);


// ─── CLINICAL CONSULTING MODALS DATABASE ───
const solutionModals = {
    "implant-don-le": {
        title: "Mất một hoặc nhiều răng",
        badge: "Giải pháp Cấy ghép Implant Swiss",
        desc: "Khôi phục khoảng mất răng, cải thiện khả năng ăn nhai và hạn chế ảnh hưởng đến các răng kế cận.",
        treatments: [
            "<strong>Trụ Implant Titanium:</strong> Trụ răng đóng vai trò như chân răng thật, tích hợp vĩnh viễn vào xương hàm.",
            "<strong>Mão răng sứ cao cấp:</strong> Gắn cố định trên trụ Implant qua khớp nối Abutment, chịu lực nhai lớn, màu sắc tự nhiên.",
            "<strong>Ăn nhai trọn đời:</strong> Phục hồi 100% khả năng nhai, ngăn ngừa tiêu xương ổ răng hiệu quả."
        ],
        link: "implant.html"
    },
    "implant-toan-ham": {
        title: "Mất răng toàn hàm",
        badge: "Giải pháp All-on-4 & All-on-6 Thụy Sĩ",
        desc: "Giải pháp phục hồi cố định trên Implant, hướng đến khả năng ăn nhai ổn định và sự thuận tiện trong sinh hoạt.",
        treatments: [
            "<strong>All-on-4 / All-on-6 Thụy Sĩ:</strong> Sử dụng 4 hoặc 6 trụ Implant chính hãng định vị tối ưu trên xương hàm.",
            "<strong>Khôi phục cơ mặt trẻ trung:</strong> Nâng đỡ môi má, lấy lại lực nhai khỏe mạnh giúp ăn uống ngon miệng.",
            "<strong>Quy trình Pro-Implant:</strong> Công nghệ kiểm soát sưng đau y khoa và máy đo vững ổn xương hàm."
        ],
        link: "implant.html"
    },
    "cuoi-ho-loi": {
        title: "Cười lộ nhiều nướu",
        badge: "Kiến tạo cung cười nướu thẩm mỹ",
        desc: "Đánh giá nguyên nhân từ nướu, răng, môi hoặc cấu trúc xương để lựa chọn phương pháp điều trị phù hợp.",
        treatments: [
            "<strong>Cắt nướu thẩm mỹ bằng Laser:</strong> Loại bỏ phần nướu thừa nhẹ nhàng, không chảy máu, không để lại sẹo.",
            "<strong>Mài xương ổ răng gồ:</strong> Xử lý tận gốc nguyên nhân gồ xương để bảo đảm kết quả duy trì trọn đời.",
            "<strong>Kiến tạo sự hài hòa:</strong> Phù hợp đường cười tự nhiên giữa răng, nướu, môi và khuôn mặt."
        ],
        link: "ho-loi.html"
    },
    "nieng-rang-so": {
        title: "Răng hô, thưa hoặc lệch lạc",
        badge: "Niềng răng Invisalign & Mắc cài số hóa",
        desc: "Cải thiện vị trí răng, khớp cắn và sự hài hòa của nụ cười bằng mắc cài hoặc khay trong suốt.",
        treatments: [
            "<strong>Khay niềng trong suốt Invisalign:</strong> Khay niềng vô hình nhập khẩu Hoa Kỳ, tháo lắp linh hoạt, thẩm mỹ tuyệt đối.",
            "<strong>Hệ thống mắc cài thông minh:</strong> Tối ưu chi phí điều trị, rút ngắn thời gian và số lần tái khám.",
            "<strong>Scan hàm TRIOS & ClinCheck:</strong> Mô phỏng lộ trình di chuyển răng 3D trực quan trước khi gắn khay/mắc cài."
        ],
        link: "nieng-rang.html"
    },
    "tham-my-rang-su": {
        title: "Muốn cải thiện thẩm mỹ & kiểm tra tình trạng răng miệng",
        badge: "Khám phá thẩm mỹ nụ cười",
        desc: "Kiểm tra chuyên sâu tình trạng răng, đưa ra giải pháp cá nhân hóa cho từng khách hàng.",
        treatments: [
            "<strong>Mặt dán sứ Veneer E.max:</strong> Độ dày siêu mỏng chỉ từ 0.3mm, bảo tồn tối đa răng gốc và tủy răng.",
            "<strong>Bọc răng sứ toàn sứ cao cấp:</strong> Phục hình răng sứ chịu lực lớn Cercon/Zirconia, bảo hành chính hãng 10-15 năm.",
            "<strong>Thiết kế Smile Design:</strong> Cân đối màu sắc và dáng răng hài hòa theo phong thủy khuôn mặt từng khách hàng."
        ],
        link: "veneer.html"
    }
};

function initSolutionModals() {
    const cards = document.querySelectorAll('.value-card[data-modal]');
    const modal = document.getElementById('solution-detail-modal');
    if (!modal) return;
    
    const closeBtn = document.getElementById('close-solution-modal');
    const modalBadge = document.getElementById('modal-badge');
    const modalTitle = document.getElementById('modal-title');
    const modalDesc = document.getElementById('modal-desc');
    const modalList = document.getElementById('modal-list');
    const modalDetailsBtn = document.getElementById('modal-details-btn');
    const modalBookBtn = document.getElementById('modal-book-btn');

    cards.forEach(card => {
        card.addEventListener('click', (e) => {
            // Prevent navigating if clicking directly on the child link (let them click Chi tiết normal)
            if (e.target.tagName.toLowerCase() === 'a' || e.target.tagName.toLowerCase() === 'i') {
                return;
            }
            
            const key = card.getAttribute('data-modal');
            const data = solutionModals[key];
            if (!data) return;

            modalBadge.textContent = data.badge;
            modalTitle.textContent = data.title;
            modalDesc.textContent = data.desc;
            
            modalList.innerHTML = '';
            data.treatments.forEach(t => {
                const li = document.createElement('li');
                li.innerHTML = `<i class="fa-solid fa-circle-check"></i> <span>${t}</span>`;
                modalList.appendChild(li);
            });

            modalDetailsBtn.setAttribute('href', data.link);
            modal.classList.add('active');
        });
    });

    if (closeBtn) {
        closeBtn.addEventListener('click', () => {
            modal.classList.remove('active');
        });
    }

    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.classList.remove('active');
        }
    });

    if (modalBookBtn) {
        modalBookBtn.addEventListener('click', () => {
            modal.classList.remove('active');
            const bookSec = document.getElementById('dang-ky');
            if (bookSec) {
                bookSec.scrollIntoView({ behavior: 'smooth' });
            }
        });
    }
}
document.addEventListener('DOMContentLoaded', initSolutionModals);


// ─── HOMEPAGE HERO INTERACTIVE SLIDESHOW LOGIC ───
function initHeroSlideshow() {
    const slides = document.querySelectorAll('.hero-slide');
    const dots = document.querySelectorAll('.hero-dot');
    if (slides.length === 0 || dots.length === 0) return;
    
    let currentSlide = 0;
    let slideInterval = setInterval(nextSlide, 4500);
    
    function showSlide(index) {
        slides.forEach(slide => slide.classList.remove('active'));
        dots.forEach(dot => dot.classList.remove('active'));
        
        slides[index].classList.add('active');
        dots[index].classList.add('active');
        currentSlide = index;
    }
    
    function nextSlide() {
        let next = (currentSlide + 1) % slides.length;
        showSlide(next);
    }
    
    dots.forEach((dot, idx) => {
        dot.addEventListener('click', () => {
            clearInterval(slideInterval);
            showSlide(idx);
            slideInterval = setInterval(nextSlide, 5000); // restart auto rotate
        });
    });

    const prevBtn = document.getElementById('hero-slide-prev');
    const nextBtn = document.getElementById('hero-slide-next');
    
    if (prevBtn) {
        prevBtn.addEventListener('click', () => {
            clearInterval(slideInterval);
            let prevIndex = (currentSlide - 1 + slides.length) % slides.length;
            showSlide(prevIndex);
            slideInterval = setInterval(nextSlide, 5000);
        });
    }
    
    if (nextBtn) {
        nextBtn.addEventListener('click', () => {
            clearInterval(slideInterval);
            nextSlide();
            slideInterval = setInterval(nextSlide, 5000);
        });
    }

    // Touch Swipe Gestures for Mobile
    const container = document.querySelector('.hero-slideshow-container');
    if (container) {
        let startX = 0;
        let endX = 0;
        
        container.addEventListener('touchstart', (e) => {
            startX = e.touches[0].clientX;
        }, { passive: true });
        
        container.addEventListener('touchend', (e) => {
            endX = e.changedTouches[0].clientX;
            handleSwipe();
        }, { passive: true });
        
        function handleSwipe() {
            const threshold = 50;
            if (startX - endX > threshold) {
                clearInterval(slideInterval);
                nextSlide();
                slideInterval = setInterval(nextSlide, 5000);
            } else if (endX - startX > threshold) {
                clearInterval(slideInterval);
                let prevIndex = (currentSlide - 1 + slides.length) % slides.length;
                showSlide(prevIndex);
                slideInterval = setInterval(nextSlide, 5000);
            }
        }
    }
}
document.addEventListener('DOMContentLoaded', initHeroSlideshow);


// ─── INTERACTIVE COST CALCULATOR ENGINE ───
const calcData = {
    implant: {
        label: "Dòng trụ sử dụng:",
        qtyLabel: "Số lượng răng cần cấy ghép Implant:",
        maxQty: 14,
        types: [
            { id: "osstem", name: "Osstem / Dentium (Hàn Quốc) - 16.000.000đ/răng", price: 16000000 },
            { id: "biotem", name: "Biotem (Hàn Quốc) - 19.000.000đ/răng", price: 19000000 },
            { id: "dentium-super", name: "Dentium Superline (Mỹ) - 22.000.000đ/răng", price: 22000000 },
            { id: "neodent", name: "Neodent (Thụy Sĩ) - 26.000.000đ/răng", price: 26000000 },
            { id: "implantswiss", name: "Implantswiss (Thụy Sĩ) - 29.000.000đ/răng", price: 29000000 },
            { id: "straumann-sla", name: "Straumann SLA (Thụy Sĩ) - 32.000.000đ/răng", price: 32000000 },
            { id: "nobel-biocare", name: "Nobel Biocare (Mỹ/Thụy Điển) - 35.000.000đ/răng", price: 35000000 },
            { id: "straumann-slactive", name: "Straumann SLActive (Thụy Sĩ) - 39.000.000đ/răng", price: 39000000 }
        ],
        extra: true,
        timeline: [
            "<strong>Ngày 1:</strong> Thăm khám tổng quát, chụp phim CT Cone Beam 3D & chế tác máng hướng dẫn cấy ghép.",
            "<strong>Ngày 7:</strong> Phẫu thuật cấy ghép trụ Implant chính hãng nhẹ nhàng trong 15 phút (có hỗ trợ tiền mê).",
            "<strong>Sau 2-3 tháng:</strong> Gắn Abutment và lấy dấu răng sứ.",
            "<strong>Sau 3 ngày tiếp theo:</strong> Gắn mão răng sứ cố định trên trụ Implant, hoàn tất phục hình răng ăn nhai."
        ]
    },
    invisalign: {
        label: "Mức độ thưa lệch răng:",
        qtyLabel: "Chọn mức độ răng hiện tại:",
        maxQty: 4,
        qtyNames: ["Mức 1 (Lite) - 59M", "Mức 2 (Moderate) - 79M", "Mức 3 - 99M", "Mức 4 (Full) - 120M"],
        types: [
            { id: "lite", name: "Invisalign Mức 1 (Lite) - 59.000.000đ", price: 59000000 },
            { id: "moderate", name: "Invisalign Mức 2 (Moderate) - 79.000.000đ", price: 79000000 },
            { id: "m3", name: "Invisalign Mức 3 - 99.000.000đ", price: 99000000 },
            { id: "full", name: "Invisalign Mức 4 (Full) - 120.000.000đ", price: 120000000 }
        ],
        extra: false,
        timeline: [
            "<strong>Ngày 1:</strong> Chụp phim, scan răng kỹ thuật số TRIOS 3D xem trước kết quả dịch chuyển ClinCheck.",
            "<strong>Ngày 10:</strong> Bác sĩ chuyên khoa bàn giao kế hoạch ClinCheck chi tiết và tiến hành đặt khay niềng.",
            "<strong>Ngày 20:</strong> Bàn giao bộ khay Invisalign đầu tiên và hướng dẫn tháo lắp tại nhà.",
            "<strong>Mỗi 6-8 tuần:</strong> Tái khám định kỳ theo dõi tiến trình cho đến khi răng đều đẹp hoàn hảo."
        ]
    },
    veneer: {
        label: "Dòng mặt dán sứ:",
        qtyLabel: "Số lượng răng dán sứ Veneer:",
        maxQty: 20,
        types: [
            { id: "veneer-sarah", name: "Sứ Sarah Thẩm Mỹ - 6.000.000đ/răng", price: 6000000 },
            { id: "veneer-cercon-ht", name: "Sứ Cercon HT - 7.000.000đ/răng", price: 7000000 },
            { id: "veneer-emax", name: "Sứ E.max Thụy Sĩ - 7.500.000đ/răng", price: 7500000 },
            { id: "veneer-lava", name: "Sứ Lava Plus (Mỹ) - 8.000.000đ/răng", price: 8000000 }
        ],
        extra: false,
        timeline: [
            "<strong>Ngày 1:</strong> Khám khớp cắn, chụp hình thiết kế nụ cười Smile Design & sửa soạn cùi răng siêu mỏng 0.3mm.",
            "<strong>Ngày 3:</strong> Lấy dấu kỹ thuật số và tiến hành chế tác đúc mặt dán sứ tại Labo nội bộ.",
            "<strong>Ngày 5:</strong> Ướm thử dáng răng, tinh chỉnh khớp cắn và gắn xi măng nha khoa cố định vĩnh viễn."
        ]
    },
    "ho-loi": {
        label: "Phương pháp tạo hình nướu:",
        qtyLabel: "Mức độ phẫu thuật cắt nướu:",
        maxQty: 1,
        types: [
            { id: "laser-only", name: "Cắt nướu Laser thẩm mỹ - 12.000.000đ", price: 12000000 },
            { id: "laser-bone", name: "Cắt nướu Laser kết hợp mài xương ổ răng - 18.000.000đ", price: 18000000 }
        ],
        extra: false,
        timeline: [
            "<strong>Ngày 1:</strong> Đo khoảng sinh học nướu, chụp phim đo độ gồ xương và thiết kế cung cười bằng thước tỷ lệ vàng.",
            "<strong>Ngày 2:</strong> Phẫu thuật cắt nướu gọt xương ổ bằng máy Laser không sưng, không đau chỉ trong 45 phút.",
            "<strong>Sau 7 ngày:</strong> Tái khám cắt chỉ nha khoa, nướu hồi phục trắng hồng rạng rỡ vĩnh viễn."
        ]
    },
    "tong-quat": {
        label: "Bệnh lý cần điều trị:",
        qtyLabel: "Số lượng răng điều trị:",
        maxQty: 4,
        types: [
            { id: "wisdom-teeth-straight", name: "Nhổ răng khôn mọc thẳng - 1.500.000đ/răng", price: 1500000 },
            { id: "wisdom-teeth-tilt", name: "Nhổ răng khôn mọc lệch / ngầm - 3.500.000đ/răng", price: 3500000 },
            { id: "root-canal", name: "Điều trị tủy răng (nhiều chân) - 1.500.000đ/răng", price: 1500000 },
            { id: "cavity-filling", name: "Trám răng Composite 3M - 400.000đ/răng", price: 400000 },
            { id: "teeth-whitening", name: "Tẩy trắng răng tại phòng khám - 2.500.000đ/ca", price: 2500000 }
        ],
        extra: false,
        timeline: [
            "<strong>Ngày 1:</strong> Thăm khám ổ viêm, chụp x-quang răng cận chóp chẩn đoán tổn thương.",
            "<strong>Tiến hành:</strong> Xử lý bệnh lý êm ái bằng máy siêu âm Piezotome hoặc thiết bị lấy tủy vi phẫu.",
            "<strong>Sau 3 ngày:</strong> Tái khám kiểm tra vết thương, đảm bảo răng ăn uống nhai cắn bình thường hết đau nhức."
        ]
    }
};

function initCostCalculator() {
    const serviceSel = document.getElementById('calc-service');
    const subTypeSel = document.getElementById('calc-sub-type');
    const subLabel = document.getElementById('calc-sub-label');
    const qtyLabel = document.getElementById('calc-qty-label');
    const qtyInput = document.getElementById('calc-qty');
    const qtyVal = document.getElementById('calc-qty-val');
    const extraContainer = document.getElementById('calc-extra-container');
    
    const totalPriceEl = document.getElementById('calc-total-price');
    const installmentPriceEl = document.getElementById('calc-installment-price');
    const timelineList = document.getElementById('calc-timeline');
    const bookBtn = document.getElementById('calc-book-btn');
    
    if (!serviceSel || !subTypeSel) return;

    function updateSliderFill() {
        if (!qtyInput) return;
        const min = parseFloat(qtyInput.min) || 1;
        const max = parseFloat(qtyInput.max) || 14;
        const val = parseFloat(qtyInput.value) || 1;
        const percent = ((val - min) / (max - min)) * 100;
        qtyInput.style.background = `linear-gradient(to right, var(--clr-primary) 0%, var(--clr-primary) ${percent}%, #e2e8f0 ${percent}%, #e2e8f0 100%)`;
    }

    function updateOptions() {
        const service = serviceSel.value;
        const data = calcData[service];
        if (!data) return;

        subLabel.textContent = data.label;
        qtyLabel.textContent = data.qtyLabel;
        
        // Update sub types options
        subTypeSel.innerHTML = '';
        data.types.forEach(t => {
            const opt = document.createElement('option');
            opt.value = t.id;
            opt.textContent = t.name;
            opt.setAttribute('data-price', t.price);
            subTypeSel.appendChild(opt);
        });

        // Update range inputs
        qtyInput.min = 1;
        qtyInput.max = data.maxQty;
        qtyInput.value = 1;
        qtyVal.textContent = 1;
        updateSliderFill();

        // Toggle extras visibility
        if (data.extra) {
            extraContainer.style.display = 'block';
        } else {
            extraContainer.style.display = 'none';
        }

        calculateCost();
    }

    function calculateCost() {
        const service = serviceSel.value;
        const data = calcData[service];
        if (!data) return;

        const selectedOpt = subTypeSel.options[subTypeSel.selectedIndex];
        if (!selectedOpt) return;
        
        const basePrice = parseFloat(selectedOpt.getAttribute('data-price')) || 0;
        const qty = parseInt(qtyInput.value) || 1;
        
        // Show text label instead of raw number if qtyNames exists (Invisalign)
        if (data.qtyNames && data.qtyNames[qty - 1]) {
            qtyVal.textContent = data.qtyNames[qty - 1];
        } else {
            qtyVal.textContent = qty;
        }

        let total = basePrice * qty;

        // Add extras if visible and checked
        if (data.extra) {
            const boneGraft = document.getElementById('calc-bone-graft');
            const sedation = document.getElementById('calc-sedation');
            
            if (boneGraft && boneGraft.checked) {
                total += 5000000 * qty; // 5M per tooth
            }
            if (sedation && sedation.checked) {
                total += 3000000; // 3M flat rate
            }
        }

        // Render values with smooth counting animation
        const startVal = parseFloat(totalPriceEl.getAttribute('data-price')) || 0;
        totalPriceEl.setAttribute('data-price', total);
        
        const oldAnimId = totalPriceEl.getAttribute('data-anim-id');
        if (oldAnimId) {
            cancelAnimationFrame(parseInt(oldAnimId));
        }
        
        const duration = 450; // ms
        const startTime = performance.now();
        
        function updateNumber(now) {
            const elapsed = now - startTime;
            const progress = Math.min(elapsed / duration, 1);
            const easeProgress = progress * (2 - progress); // Ease out quad
            const current = Math.floor(startVal + (total - startVal) * easeProgress);
            
            totalPriceEl.textContent = current.toLocaleString('vi-VN') + 'đ';
            const installment = Math.round(current / 12);
            installmentPriceEl.textContent = `Trả góp 0%: chỉ từ ${installment.toLocaleString('vi-VN')}đ / tháng`;
            
            if (progress < 1) {
                const animId = requestAnimationFrame(updateNumber);
                totalPriceEl.setAttribute('data-anim-id', animId);
            } else {
                totalPriceEl.textContent = total.toLocaleString('vi-VN') + 'đ';
                const finalInstallment = Math.round(total / 12);
                installmentPriceEl.textContent = `Trả góp 0%: chỉ từ ${finalInstallment.toLocaleString('vi-VN')}đ / tháng`;
                totalPriceEl.removeAttribute('data-anim-id');
            }
        }
        
        const initialAnimId = requestAnimationFrame(updateNumber);
        totalPriceEl.setAttribute('data-anim-id', initialAnimId);

        // Render timeline with staggered entry animations
        timelineList.innerHTML = '';
        data.timeline.forEach((step, idx) => {
            const li = document.createElement('li');
            li.innerHTML = `<span>${step}</span>`;
            li.style.opacity = '0';
            li.style.transform = 'translateX(-15px)';
            li.style.transition = 'all 0.35s cubic-bezier(0.165, 0.84, 0.44, 1)';
            timelineList.appendChild(li);
            
            setTimeout(() => {
                li.style.opacity = '1';
                li.style.transform = 'translateX(0)';
            }, idx * 60);
        });
    }

    serviceSel.addEventListener('change', updateOptions);
    subTypeSel.addEventListener('change', calculateCost);
    qtyInput.addEventListener('input', () => {
        qtyVal.textContent = qtyInput.value;
        updateSliderFill();
        calculateCost();
    });

    const boneGraft = document.getElementById('calc-bone-graft');
    const sedation = document.getElementById('calc-sedation');
    if (boneGraft) boneGraft.addEventListener('change', calculateCost);
    if (sedation) sedation.addEventListener('change', calculateCost);

    // Initial load
    updateOptions();

    // Link booking trigger to register and auto-fill notes
    if (bookBtn) {
        bookBtn.addEventListener('click', () => {
            const serviceName = serviceSel.options[serviceSel.selectedIndex].text;
            const subName = subTypeSel.options[subTypeSel.selectedIndex].text.split(' - ')[0];
            const qtyText = qtyVal.textContent;
            
            let note = `Đăng ký phác đồ: ${serviceName} - dòng ${subName}, số lượng: ${qtyText}. Tổng dự toán: ${totalPriceEl.textContent}`;
            
            // Append extras if active
            const service = serviceSel.value;
            if (calcData[service].extra) {
                const boneGraftVal = boneGraft && boneGraft.checked ? "Có ghép xương" : "";
                const sedationVal = sedation && sedation.checked ? "Có tiền mê" : "";
                const extras = [boneGraftVal, sedationVal].filter(Boolean).join(', ');
                if (extras) {
                    note += ` (${extras})`;
                }
            }

            // Auto-select corresponding service in registration form
            const formServiceSel = document.querySelector('#dang-ky select[name="service"]');
            if (formServiceSel) {
                let formServiceVal = '';
                if (service === 'implant') formServiceVal = 'Trồng răng Implant';
                else if (service === 'invisalign') formServiceVal = 'Niềng răng chỉnh nha';
                else if (service === 'ho-loi') formServiceVal = 'Điều trị cười hở lợi';
                else if (service === 'veneer') formServiceVal = 'Dán sứ thẩm mỹ Veneer';
                
                if (formServiceVal) {
                    formServiceSel.value = formServiceVal;
                }
            }

            // Find booking form textarea/hidden note and auto-fill
            const noteTextarea = document.querySelector('#dang-ky textarea, #dang-ky [name="note"], #dang-ky [name="noi_dung"], #note');
            if (noteTextarea) {
                noteTextarea.value = note;
            } else {
                // If no specific textarea exists, find the first textarea in the form
                const genericTextarea = document.querySelector('#dang-ky textarea');
                if (genericTextarea) {
                    genericTextarea.value = note;
                }
            }

            // Scroll down smoothly
            const bookSec = document.getElementById('dang-ky');
            if (bookSec) {
                bookSec.scrollIntoView({ behavior: 'smooth' });
            }
        });
    }
}
document.addEventListener('DOMContentLoaded', initCostCalculator);


// ─── CUSTOM APP-STYLE DROPDOWN INITIALIZER ───
function initCustomDropdowns() {
    const selects = document.querySelectorAll('select.form-select, select#calc-service, select#calc-sub-type');
    
    selects.forEach(select => {
        if (select.parentNode.classList.contains('custom-select-wrapper')) return;

        select.style.display = 'none';

        const wrapper = document.createElement('div');
        wrapper.className = 'custom-select-wrapper';
        select.parentNode.insertBefore(wrapper, select);
        wrapper.appendChild(select);

        const trigger = document.createElement('div');
        trigger.className = 'custom-select-trigger';
        
        const triggerText = document.createElement('span');
        const selectedOpt = select.options[select.selectedIndex];
        triggerText.textContent = selectedOpt ? selectedOpt.textContent : 'Lựa chọn';
        trigger.appendChild(triggerText);

        const icon = document.createElement('i');
        icon.className = 'fa-solid fa-chevron-down';
        trigger.appendChild(icon);
        wrapper.appendChild(trigger);

        const optionsContainer = document.createElement('div');
        optionsContainer.className = 'custom-options';
        
        const rebuildOptions = () => {
            optionsContainer.innerHTML = '';
            Array.from(select.options).forEach(opt => {
                if (opt.disabled && !opt.value) return;
                const optDiv = document.createElement('div');
                optDiv.className = 'custom-option' + (opt.selected ? ' selected' : '');
                optDiv.textContent = opt.textContent;
                optDiv.setAttribute('data-value', opt.value);

                optDiv.addEventListener('click', (e) => {
                    e.stopPropagation();
                    triggerText.textContent = opt.textContent;
                    select.value = opt.value;
                    
                    const event = new Event('change', { bubbles: true });
                    select.dispatchEvent(event);
                    
                    wrapper.classList.remove('open');
                });
                optionsContainer.appendChild(optDiv);
            });
        };
        
        rebuildOptions();
        wrapper.appendChild(optionsContainer);

        select.addEventListener('change', () => {
            const currentSelected = select.options[select.selectedIndex];
            triggerText.textContent = currentSelected ? currentSelected.textContent : '';
            
            const optDivs = optionsContainer.querySelectorAll('.custom-option');
            optDivs.forEach(div => {
                if (div.getAttribute('data-value') === select.value) {
                    div.classList.add('selected');
                } else {
                    div.classList.remove('selected');
                }
            });
        });

        trigger.addEventListener('click', (e) => {
            e.stopPropagation();
            if (select.id === 'calc-sub-type') {
                rebuildOptions();
            }
            document.querySelectorAll('.custom-select-wrapper').forEach(w => {
                if (w !== wrapper) w.classList.remove('open');
            });
            wrapper.classList.toggle('open');
        });
    });

    document.addEventListener('click', () => {
        document.querySelectorAll('.custom-select-wrapper').forEach(wrapper => {
            wrapper.classList.remove('open');
        });
    });
}
document.addEventListener('DOMContentLoaded', initCustomDropdowns);

// ─── AI ASSISTANT FOR ESTIMATOR ───
function initAICostProposal() {
    const aiInput = document.getElementById('calc-ai-input');
    const aiBtn = document.getElementById('calc-ai-btn');
    const aiFeedback = document.getElementById('calc-ai-feedback');
    const serviceSel = document.getElementById('calc-service');
    
    if (!aiInput || !aiBtn || !serviceSel) return;

    const analyzeText = () => {
        const text = aiInput.value.toLowerCase().trim();
        if (!text) return;

        let detectedService = '';
        let detectedQty = 1;
        let feedbackText = '';

        // 1. Detect service
        if (text.includes('implant') || text.includes('mất răng') || text.includes('trồng răng') || text.includes('cắm trụ') || text.includes('răng giả') || text.includes('mất 1 răng') || text.includes('mất nhiều răng')) {
            detectedService = 'implant';
            feedbackText = 'Cấy ghép Implant Thụy Sĩ';
        } else if (text.includes('niềng') || text.includes('invisalign') || text.includes('chỉnh nha') || text.includes('khay trong') || text.includes('mắc cài') || text.includes('khấp khểnh') || text.includes('hô vẩu') || text.includes('móm') || text.includes('lệch')) {
            detectedService = 'invisalign';
            feedbackText = 'Niềng răng Invisalign';
        } else if (text.includes('hở lợi') || text.includes('cười hở lợi') || text.includes('cắt nướu') || text.includes('thân răng') || text.includes('nướu')) {
            detectedService = 'ho-loi';
            feedbackText = 'Điều trị Cười hở lợi';
        } else if (text.includes('sứ') || text.includes('veneer') || text.includes('bọc răng') || text.includes('dán sứ') || text.includes('mặt dán')) {
            detectedService = 'veneer';
            feedbackText = 'Dán sứ Veneer';
        } else if (text.includes('khôn') || text.includes('nhổ') || text.includes('tủy') || text.includes('sâu') || text.includes('nhổ răng') || text.includes('trám') || text.includes('cạo vôi')) {
            detectedService = 'tong-quat';
            feedbackText = 'Nha khoa tổng quát';
        }

        // 2. Parse quantity
        const numberMatches = text.match(/\d+/);
        if (numberMatches) {
            detectedQty = parseInt(numberMatches[0]);
        } else {
            if (text.includes(' một ') || text.includes(' 1 ') || text.startsWith('1 ') || text.startsWith('một ')) detectedQty = 1;
            else if (text.includes(' hai ') || text.includes(' 2 ') || text.startsWith('2 ') || text.startsWith('hai ')) detectedQty = 2;
            else if (text.includes(' ba ') || text.includes(' 3 ') || text.startsWith('3 ') || text.startsWith('ba ')) detectedQty = 3;
            else if (text.includes(' bốn ') || text.includes(' 4 ') || text.startsWith('4 ') || text.startsWith('bốn ')) detectedQty = 4;
            else if (text.includes(' năm ') || text.includes(' 5 ') || text.startsWith('5 ') || text.startsWith('năm ')) detectedQty = 5;
            else if (text.includes(' sáu ') || text.includes(' 6 ') || text.startsWith('6 ') || text.startsWith('sáu ')) detectedQty = 6;
            else if (text.includes(' toàn hàm ') || text.includes('all-on') || text.includes('all on')) {
                detectedQty = 4;
            }
        }

        if (detectedService) {
            serviceSel.value = detectedService;
            // Trigger change event to populate subtypes
            const changeEvent = new Event('change', { bubbles: true });
            serviceSel.dispatchEvent(changeEvent);

            // Fetch active service range settings
            const qtyInput = document.getElementById('calc-qty');
            if (qtyInput) {
                const maxVal = parseInt(qtyInput.max) || 14;
                const minVal = parseInt(qtyInput.min) || 1;
                if (detectedQty > maxVal) detectedQty = maxVal;
                if (detectedQty < minVal) detectedQty = minVal;
                qtyInput.value = detectedQty;
                // trigger input event
                const inputEvent = new Event('input', { bubbles: true });
                qtyInput.dispatchEvent(inputEvent);
            }

            // Try to match type/brand
            const subTypeSel = document.getElementById('calc-sub-type');
            if (subTypeSel) {
                if (detectedService === 'implant') {
                    if (text.includes('swiss') || text.includes('thụy sĩ') || text.includes('thụy sỹ') || text.includes('tốt nhất')) {
                        for (let i = 0; i < subTypeSel.options.length; i++) {
                            if (subTypeSel.options[i].text.toLowerCase().includes('swiss') || subTypeSel.options[i].text.toLowerCase().includes('thụy')) {
                                subTypeSel.selectedIndex = i;
                                break;
                            }
                        }
                    } else if (text.includes('nhật') || text.includes('hàn quốc') || text.includes('osstem') || text.includes('dentium')) {
                        for (let i = 0; i < subTypeSel.options.length; i++) {
                            if (subTypeSel.options[i].text.toLowerCase().includes('osstem') || subTypeSel.options[i].text.toLowerCase().includes('hàn')) {
                                subTypeSel.selectedIndex = i;
                                break;
                            }
                        }
                    }
                }
                const subChangeEvent = new Event('change', { bubbles: true });
                subTypeSel.dispatchEvent(subChangeEvent);
            }

            // Check extras
            const boneGraft = document.getElementById('calc-bone-graft');
            const sedation = document.getElementById('calc-sedation');
            if (boneGraft) {
                boneGraft.checked = text.includes('ghép xương') || text.includes('nâng xoang') || text.includes('thiếu xương');
                const boneEvent = new Event('change', { bubbles: true });
                boneGraft.dispatchEvent(boneEvent);
            }
            if (sedation) {
                sedation.checked = text.includes('tiền mê') || text.includes('gây mê') || text.includes('sợ đau') || text.includes('êm ái');
                const sedationEvent = new Event('change', { bubbles: true });
                sedation.dispatchEvent(sedationEvent);
            }

            aiFeedback.style.display = 'block';
            aiFeedback.style.color = 'var(--clr-secondary)';
            aiFeedback.innerHTML = `🤖 AI Đề xuất: <strong>${feedbackText}</strong> (Số lượng: ${detectedQty}). Bộ dự toán chi phí đã được cập nhật tự động!`;
        } else {
            aiFeedback.style.display = 'block';
            aiFeedback.style.color = '#ef4444';
            aiFeedback.innerHTML = `🤖 AI chưa nhận diện rõ nhu cầu. Bạn hãy mô tả rõ hơn (ví dụ: 'mất 2 răng', 'niềng răng hô vẩu', 'bị cười hở lợi').`;
        }
    };

    const runAnalysis = () => {
        const text = aiInput.value.toLowerCase().trim();
        if (!text) return;
        
        aiBtn.disabled = true;
        aiBtn.textContent = 'Đang phân tích...';
        aiFeedback.style.display = 'none';
        
        setTimeout(() => {
            analyzeText();
            aiBtn.disabled = false;
            aiBtn.textContent = 'Phân tích';
        }, 250);
    };

    aiBtn.addEventListener('click', (e) => {
        e.preventDefault();
        runAnalysis();
    });
    aiInput.addEventListener('keydown', (e) => {
        if (e.key === 'Enter') {
            e.preventDefault();
            runAnalysis();
        }
    });
}
document.addEventListener('DOMContentLoaded', initAICostProposal);

// Lenis Smooth Scroll Initialization
function initLenis() {
    if (typeof Lenis === 'undefined') return;
    
    const lenis = new Lenis({
        duration: 1.2,
        easing: (t) => Math.min(1, 1.001 - Math.pow(2, -10 * t)),
        direction: 'vertical',
        gestureDirection: 'vertical',
        smooth: true,
        mouseMultiplier: 1,
        smoothTouch: false,
        touchMultiplier: 2,
        infinite: false,
    });

    function raf(time) {
        lenis.raf(time);
        requestAnimationFrame(raf);
    }

    requestAnimationFrame(raf);
    
    // Connect anchor links to Lenis scroll
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const targetId = this.getAttribute('href');
            if (targetId === '#') return;
            const targetEl = document.querySelector(targetId);
            if (targetEl) {
                e.preventDefault();
                lenis.scrollTo(targetEl, {
                    offset: -70 // offset header
                });
            }
        });
    });
}
document.addEventListener('DOMContentLoaded', initLenis);


// Clinical Cases Gallery Tab Switcher
function initCasesTabs() {
    const tabs = document.querySelectorAll('.cases-tab-btn');
    const panels = document.querySelectorAll('.cases-tab-panel');
    if (tabs.length === 0 || panels.length === 0) return;

    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            const targetTab = tab.getAttribute('data-tab');

            // Set active class on buttons
            tabs.forEach(t => t.classList.remove('active'));
            tab.classList.add('active');

            // Set active class on panels
            panels.forEach(panel => {
                panel.classList.remove('active');
                if (panel.id === `cases-${targetTab}`) {
                    panel.classList.add('active');
                }
            });
        });
    });
}
document.addEventListener('DOMContentLoaded', initCasesTabs);


// ─── IMPLANT MATERIALS SLIDER & TABS SYNCHRONIZATION ───
function initMaterialsSlider() {
    const track = document.querySelector('.materials-slider-track');
    const tabs = document.querySelectorAll('.materials-tab-btn');
    const dotsContainer = document.querySelector('.materials-dots');
    const prevBtn = document.querySelector('.materials-nav-btn.prev');
    const nextBtn = document.querySelector('.materials-nav-btn.next');

    if (!track) return;

    const slides = Array.from(track.children);
    let currentIdx = 0;

    // Create dots if container is present and empty
    if (dotsContainer && dotsContainer.children.length === 0) {
        slides.forEach((_, idx) => {
            const dot = document.createElement('button');
            dot.className = 'materials-dot' + (idx === 0 ? ' active' : '');
            dot.setAttribute('aria-label', `Slide ${idx + 1}`);
            dot.addEventListener('click', () => {
                moveToSlide(idx);
            });
            dotsContainer.appendChild(dot);
        });
    }

    const dots = dotsContainer ? Array.from(dotsContainer.children) : [];

    const moveToSlide = (idx) => {
        if (idx < 0 || idx >= slides.length) return;
        
        // Shift track container
        track.style.transform = `translateX(-${idx * 33.3333}%)`;
        
        // Update top tabs active status
        tabs.forEach((tab, tIdx) => {
            if (tIdx === idx) {
                tab.classList.add('active');
            } else {
                tab.classList.remove('active');
            }
        });

        // Update bottom slide dots
        if (dots.length > 0) {
            dots.forEach((dot, dIdx) => {
                if (dIdx === idx) {
                    dot.classList.add('active');
                } else {
                    dot.classList.remove('active');
                }
            });
        }

        currentIdx = idx;
    };

    // Bind tab clicks
    tabs.forEach((tab, idx) => {
        tab.addEventListener('click', () => {
            moveToSlide(idx);
        });
    });

    // Bind arrow navigators
    if (prevBtn) {
        prevBtn.addEventListener('click', () => {
            let prevIndex = (currentIdx - 1 + slides.length) % slides.length;
            moveToSlide(prevIndex);
        });
    }

    if (nextBtn) {
        nextBtn.addEventListener('click', () => {
            let nextIndex = (currentIdx + 1) % slides.length;
            moveToSlide(nextIndex);
        });
    }

    // Bind touch gestures for mobile swiping
    let startX = 0;
    let endX = 0;
    const wrapper = document.querySelector('.materials-slider-track-wrapper');

    if (wrapper) {
        wrapper.addEventListener('touchstart', (e) => {
            startX = e.touches[0].clientX;
        }, { passive: true });

        wrapper.addEventListener('touchend', (e) => {
            endX = e.changedTouches[0].clientX;
            const threshold = 50;
            if (startX - endX > threshold) {
                let nextIndex = (currentIdx + 1) % slides.length;
                moveToSlide(nextIndex);
            } else if (endX - startX > threshold) {
                let prevIndex = (currentIdx - 1 + slides.length) % slides.length;
                moveToSlide(prevIndex);
            }
        }, { passive: true });
    }
}
document.addEventListener('DOMContentLoaded', initMaterialsSlider);


// ─── FINANCE ACCORDIONS TAB TOGGLE ───
function initFinanceTabs() {
    const tabs = document.querySelectorAll('.finance-tab-btn');
    const boxes = document.querySelectorAll('.finance-content-box');
    if (tabs.length === 0 || boxes.length === 0) return;

    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            const target = tab.getAttribute('data-target');

            // Select active tab
            tabs.forEach(t => t.classList.remove('active'));
            tab.classList.add('active');

            // Select active content body
            boxes.forEach(box => {
                box.classList.remove('active');
                if (box.id === target) {
                    box.classList.add('active');
                }
            });
        });
    });
}
document.addEventListener('DOMContentLoaded', initFinanceTabs);


