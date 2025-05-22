        // Toggle visibility of the FAQ answers
        document.querySelectorAll('.faq-item h3').forEach(item => {
            item.addEventListener('click', () => {
                item.parentElement.classList.toggle('active');
            });
        });