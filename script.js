// ================== MOBILE MENU TOGGLE ==================
document.addEventListener('DOMContentLoaded', function() {
    const navToggle = document.querySelector('.nav-toggle');
    const navMenu = document.querySelector('.nav-menu');

    if (navToggle) {
        navToggle.addEventListener('click', function() {
            navMenu.classList.toggle('show');
        });
    }

    // Sembunyikan tombol Login di navbar jika bukan jaringan lokal (mis. GitHub Pages)
    function isLocalNetwork() {
        const h = location.hostname;
        return h === 'localhost'
            || h === '127.0.0.1'
            || h === '::1'
            || /^10\./.test(h)
            || /^192\.168\./.test(h)
            || /^172\.(1[6-9]|2[0-9]|3[01])\./.test(h);
    }
    if (!isLocalNetwork()) {
        const loginNavLink = document.querySelector('.nav-menu a[href*="login.html"]');
        if (loginNavLink) {
            loginNavLink.parentElement.style.display = 'none';
        }
    }

    // Close menu when clicking on a link
    const navLinks = document.querySelectorAll('.nav-menu a');
    navLinks.forEach(link => {
        link.addEventListener('click', function() {
            navMenu.classList.remove('show');
        });
    });

    // Close menu when clicking outside
    document.addEventListener('click', function(event) {
        if (!event.target.closest('.navbar')) {
            navMenu.classList.remove('show');
        }
    });

    // ================== ACTIVE PAGE LINK ==================
    setActiveLink();

    // ================== DATE DISPLAY ==================
    displayDate();

    // ================== CALENDAR GENERATION ==================
    generateCalendar();

    // ================== CONTACT FORM HANDLER ==================
    handleContactForm();
});

// ================== SET ACTIVE PAGE LINK ==================
function setActiveLink() {
    const currentPage = window.location.pathname.split('/').pop() || 'index.html';
    const navLinks = document.querySelectorAll('.nav-menu a');

    navLinks.forEach(link => {
        const href = link.getAttribute('href');
        if (href === currentPage || (currentPage === '' && href === 'index.html')) {
            link.classList.add('active');
        } else {
            link.classList.remove('active');
        }
    });
}

// ================== DISPLAY DATE WITH INDONESIAN FORMAT ==================
function displayDate() {
    const dateElements = document.querySelectorAll('#info-date');
    
    if (dateElements.length === 0) return;

    const now = new Date();
    const daysInIndonesian = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
    const monthsInIndonesian = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

    const dayName = daysInIndonesian[now.getDay()];
    const date = now.getDate();
    const monthName = monthsInIndonesian[now.getMonth()];
    const year = now.getFullYear();

    const formattedDate = `${dayName}, ${date} ${monthName} ${year}`;

    dateElements.forEach(element => {
        element.textContent = formattedDate;
    });
}

// ================== GENERATE CALENDAR ==================
function generateCalendar() {
    const calendarElement = document.getElementById('calendar');
    
    if (!calendarElement) return;

    const now = new Date();
    const year = now.getFullYear();
    const month = now.getMonth();

    const monthsInIndonesian = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    const daysInIndonesian = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];

    // Create calendar table
    let calendarHTML = `<table class="calendar-table">`;
    calendarHTML += `<caption>${monthsInIndonesian[month]} ${year}</caption>`;
    calendarHTML += `<tr>`;

    // Add day headers
    daysInIndonesian.forEach(day => {
        calendarHTML += `<th>${day}</th>`;
    });
    calendarHTML += `</tr><tr>`;

    // Get first day of month
    const firstDay = new Date(year, month, 1).getDay();
    
    // Add empty cells for days before month starts
    for (let i = 0; i < firstDay; i++) {
        calendarHTML += `<td></td>`;
    }

    // Get number of days in month
    const daysInMonth = new Date(year, month + 1, 0).getDate();
    let dayCell = firstDay;

    // Add days
    for (let day = 1; day <= daysInMonth; day++) {
        if (dayCell % 7 === 0 && dayCell !== 0) {
            calendarHTML += `</tr><tr>`;
        }
        
        if (day === now.getDate()) {
            calendarHTML += `<td class="today">${day}</td>`;
        } else {
            calendarHTML += `<td>${day}</td>`;
        }
        
        dayCell++;
    }

    // Close table
    calendarHTML += `</tr></table>`;

    calendarElement.innerHTML = calendarHTML;

    // Add calendar CSS
    addCalendarStyles();
}

// ================== ADD CALENDAR STYLES ==================
function addCalendarStyles() {
    const style = document.createElement('style');
    style.textContent = `
        .calendar-table {
            width: 100%;
            border-collapse: collapse;
            text-align: center;
            font-size: 0.9rem;
        }

        .calendar-table caption {
            font-weight: bold;
            padding: 10px 0;
            font-size: 1rem;
        }

        .calendar-table th {
            background: #f5f5f5;
            padding: 8px;
            font-weight: 600;
            color: #333;
        }

        .calendar-table td {
            padding: 8px;
            border: 1px solid #eee;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .calendar-table td:hover {
            background: #e6f2e9;
        }

        .calendar-table td.today {
            background: #1a5a3d;
            color: white;
            font-weight: bold;
        }
    `;
    
    document.head.appendChild(style);
}

// ================== HANDLE CONTACT FORM ==================
function handleContactForm() {
    const contactForm = document.getElementById('contactForm');

    if (!contactForm) return;

    contactForm.addEventListener('submit', function(e) {
        e.preventDefault();

        // Get form values
        const name = document.getElementById('name').value.trim();
        const email = document.getElementById('email').value.trim();
        const subject = document.getElementById('subject').value.trim();
        const message = document.getElementById('message').value.trim();

        // Basic validation
        if (!name || !email || !subject || !message) {
            alert('Mohon lengkapi semua field yang diperlukan (*)');
            return;
        }

        // Email validation
        if (!isValidEmail(email)) {
            alert('Mohon masukkan email yang valid');
            return;
        }

        // Show success message
        alert('Terima kasih! Pesan Anda telah berhasil dikirim. Kami akan segera menghubungi Anda.');

        // Reset form
        contactForm.reset();
    });
}

// ================== EMAIL VALIDATION ==================
function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

// ================== SMOOTH SCROLL ==================
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth'
            });
        }
    });
});

// ================== LAZY LOAD IMAGES ==================
if ('IntersectionObserver' in window) {
    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.src = img.dataset.src;
                img.classList.add('loaded');
                observer.unobserve(img);
            }
        });
    });

    document.querySelectorAll('img[data-src]').forEach(img => {
        imageObserver.observe(img);
    });
}

// ================== ADD ANIMATION ON SCROLL ==================
function observeElements() {
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('fade-in');
                observer.unobserve(entry.target);
            }
        });
    }, {
        threshold: 0.1
    });

    document.querySelectorAll('.section, .facility-card, .value-card, .extracurricular-card').forEach(element => {
        observer.observe(element);
    });
}

// Run animations after page load
window.addEventListener('load', observeElements);

// ================== PRINT FRIENDLY ==================
function makePrintFriendly() {
    const style = document.createElement('style');
    style.textContent = `
        @media print {
            .navbar, .footer, .whatsapp-btn, .ticker-info {
                display: none;
            }
            body {
                background: white;
            }
        }
    `;
    document.head.appendChild(style);
}

makePrintFriendly();