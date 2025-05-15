document.addEventListener('DOMContentLoaded', function() {
    console.log('Document is ready!');
    
    // Mobile Menu Toggle
    const menuToggle = document.querySelector('.menu-toggle');
    const navLinks = document.querySelector('.nav-links');
    
    if (menuToggle) {
        menuToggle.addEventListener('click', function() {
            navLinks.classList.toggle('active');
            menuToggle.classList.toggle('active');
        });
    }
    
    // Search Functionality
    const searchInput = document.getElementById('search-input');
    const searchButton = document.querySelector('.search-container button');
    
    if (searchButton) {
        searchButton.addEventListener('click', function() {
            searchFunction();
        });
    }
    
    if (searchInput) {
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                searchFunction();
            }
        });
    }
    
    // Smooth Scrolling for Anchor Links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            
            const targetId = this.getAttribute('href').substring(1);
            const targetElement = document.getElementById(targetId);
            
            if (targetElement) {
                window.scrollTo({
                    top: targetElement.offsetTop - 70,
                    behavior: 'smooth'
                });
                
                // Close mobile menu if open
                if (navLinks.classList.contains('active')) {
                    navLinks.classList.remove('active');
                    menuToggle.classList.remove('active');
                }
            }
        });
    });
    
    // Image Gallery Enhancement
    const galleryItems = document.querySelectorAll('.gallery-item');
    
    galleryItems.forEach(item => {
        item.addEventListener('click', function() {
            // You could implement a lightbox here
            console.log('Gallery item clicked:', this);
        });
    });
    
    // Add Animation Classes on Scroll
    const animatedElements = document.querySelectorAll('.animate-on-scroll');
    
    function checkScroll() {
        const triggerBottom = window.innerHeight * 0.8;
        
        animatedElements.forEach(element => {
            const elementTop = element.getBoundingClientRect().top;
            
            if (elementTop < triggerBottom) {
                element.classList.add('visible');
            }
        });
    }
    
    window.addEventListener('scroll', checkScroll);
    checkScroll(); // Check on initial load
    
    // Form Validation
    const forms = document.querySelectorAll('form');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    highlightInvalidField(field);
                } else {
                    removeInvalidHighlight(field);
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                showValidationMessage(form, 'Please fill in all required fields');
            }
        });
    });
    
    function highlightInvalidField(field) {
        field.classList.add('invalid');
        field.addEventListener('input', function() {
            if (field.value.trim()) {
                removeInvalidHighlight(field);
            }
        });
    }
    
    function removeInvalidHighlight(field) {
        field.classList.remove('invalid');
    }
    
    function showValidationMessage(form, message) {
        let validationMessage = form.querySelector('.validation-message');
        
        if (!validationMessage) {
            validationMessage = document.createElement('div');
            validationMessage.className = 'validation-message';
            form.appendChild(validationMessage);
        }
        
        validationMessage.textContent = message;
        validationMessage.style.display = 'block';
        
        setTimeout(() => {
            validationMessage.style.opacity = '0';
            setTimeout(() => {
                validationMessage.style.display = 'none';
                validationMessage.style.opacity = '1';
            }, 300);
        }, 3000);
    }
});

// Search Function
function searchFunction() {
    const searchInput = document.getElementById('search-input');
    const searchTerm = searchInput.value.trim().toLowerCase();
    
    if (!searchTerm) {
        alert('Please enter a search term');
        return;
    }
    
    console.log('Searching for:', searchTerm);
    
    // Get all gallery items and their captions
    const galleryItems = document.querySelectorAll('.gallery-item');
    let foundItems = 0;
    
    galleryItems.forEach(item => {
        const caption = item.querySelector('.caption');
        const itemText = caption ? caption.textContent.toLowerCase() : '';
        
        if (itemText.includes(searchTerm)) {
            item.style.display = 'block';
            // Add highlight animation
            item.classList.add('search-highlight');
            setTimeout(() => {
                item.classList.remove('search-highlight');
            }, 2000);
            foundItems++;
        } else {
            item.style.display = 'none';
        }
    });
    
    // Show search results message
    let resultsMessage = document.querySelector('.search-results-message');
    
    if (!resultsMessage) {
        resultsMessage = document.createElement('div');
        resultsMessage.className = 'search-results-message';
        document.querySelector('.search-container').appendChild(resultsMessage);
    }
    
    if (foundItems > 0) {
        resultsMessage.textContent = `Found ${foundItems} results for "${searchTerm}"`;
    } else {
        resultsMessage.textContent = `No results found for "${searchTerm}". Try different keywords.`;
    }
    
    resultsMessage.style.display = 'block';
    
    // Scroll to results
    window.scrollTo({
        top: document.querySelector('.gallery-grid').offsetTop - 100,
        behavior: 'smooth'
    });
}

// Theme Toggle
function toggleDarkMode() {
    document.body.classList.toggle('dark-mode');
    
    const isDarkMode = document.body.classList.contains('dark-mode');
    localStorage.setItem('darkMode', isDarkMode);
    
    const themeToggle = document.getElementById('theme-toggle');
    if (themeToggle) {
        themeToggle.textContent = isDarkMode ? '☀️' : '🌙';
    }
}

// Check for saved theme preference
const savedDarkMode = localStorage.getItem('darkMode') === 'true';
if (savedDarkMode) {
    document.body.classList.add('dark-mode');
    const themeToggle = document.getElementById('theme-toggle');
    if (themeToggle) {
        themeToggle.textContent = '☀️';
    }
}

// Add lazy loading to images
document.addEventListener('DOMContentLoaded', function() {
    const lazyImages = document.querySelectorAll('img[data-src]');
    
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    img.removeAttribute('data-src');
                    imageObserver.unobserve(img);
                }
            });
        });
        
        lazyImages.forEach(img => {
            imageObserver.observe(img);
        });
    } else {
        // Fallback for browsers that don't support IntersectionObserver
        lazyImages.forEach(img => {
            img.src = img.dataset.src;
            img.removeAttribute('data-src');
        });
    }
});

// Scroll to top function
function scrollToTop() {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
}

// Scroll to bottom function
function scrollToBottom() {
    window.scrollTo({
        top: document.body.scrollHeight,
        behavior: 'smooth'
    });
}

// Show/hide buttons based on scroll position
window.addEventListener('scroll', function() {
    const scrollTopBtn = document.getElementById('scrollTopBtn');
    const scrollBottomBtn = document.getElementById('scrollBottomBtn');
    
    // For top button: show when scrolled down a bit
    if (window.scrollY > 300) {
        scrollTopBtn.classList.remove('hidden');
    } else {
        scrollTopBtn.classList.add('hidden');
    }
    
    // For bottom button: hide when near the bottom
    const nearBottom = window.scrollY + window.innerHeight > document.body.scrollHeight - 300;
    if (nearBottom) {
        scrollBottomBtn.classList.add('hidden');
    } else {
        scrollBottomBtn.classList.remove('hidden');
    }
});

// Initial check to set correct button visibility on page load
document.addEventListener('DOMContentLoaded', function() {
    // Hide top button initially (we're at the top)
    document.getElementById('scrollTopBtn').classList.add('hidden');
    
    // Check if page is tall enough to need a bottom button
    const pageIsTall = document.body.scrollHeight > window.innerHeight + 300;
    if (!pageIsTall) {
        document.getElementById('scrollBottomBtn').classList.add('hidden');
    }
});

// Image Slider Functionality
document.addEventListener('DOMContentLoaded', function() {
    const track = document.querySelector('.slider-track');
    const items = document.querySelectorAll('.slider-item');
    const prevArrow = document.querySelector('.prev-arrow');
    const nextArrow = document.querySelector('.next-arrow');
    
    let currentIndex = 0;
    const itemWidth = 100 / 3; // 3 items visible at once
    const maxIndex = items.length - 3; // Number of items minus visible items
    
    // Set initial width based on total number of items
    track.style.width = `${(items.length / 3) * 100}%`;
    
    // Handle previous button click
    prevArrow.addEventListener('click', function() {
        if (currentIndex > 0) {
            currentIndex--;
            updateSliderPosition();
        }
    });
    
    // Handle next button click
    nextArrow.addEventListener('click', function() {
        if (currentIndex < maxIndex) {
            currentIndex++;
            updateSliderPosition();
        }
    });
    
    // Touch swipe functionality
    let touchStartX = 0;
    let touchEndX = 0;
    
    track.addEventListener('touchstart', function(e) {
        touchStartX = e.changedTouches[0].screenX;
    });
    
    track.addEventListener('touchend', function(e) {
        touchEndX = e.changedTouches[0].screenX;
        handleSwipe();
    });
    
    function handleSwipe() {
        if (touchStartX - touchEndX > 50) {
            // Swipe left
            if (currentIndex < maxIndex) {
                currentIndex++;
                updateSliderPosition();
            }
        }
        
        if (touchEndX - touchStartX > 50) {
            // Swipe right
            if (currentIndex > 0) {
                currentIndex--;
                updateSliderPosition();
            }
        }
    }
    
    function updateSliderPosition() {
        const translateValue = -currentIndex * itemWidth;
        track.style.transform = `translateX(${translateValue}%)`;
        
        // Update button states
        prevArrow.style.opacity = currentIndex === 0 ? '0.5' : '1';
        nextArrow.style.opacity = currentIndex === maxIndex ? '0.5' : '1';
    }
    
    // Initialize button states
    updateSliderPosition();
});