document.addEventListener('DOMContentLoaded', function() {
    const card = document.getElementById('card');
    const container = document.getElementById('card-container');
    let isFlipped = false;

    container.addEventListener('click', () => {
        isFlipped = !isFlipped;
        if (isFlipped) {
            card.style.transform = 'rotateY(180deg)';
        } else {
            card.style.transform = 'rotateY(0)';
        }
    });

    container.addEventListener('mousemove', (e) => {
        const rect = card.getBoundingClientRect();
        const x = e.clientX - rect.left;
        const y = e.clientY - rect.top;
        
        const centerX = rect.width / 2;
        const centerY = rect.height / 2;
        
        const rotateX = (y - centerY) / 10;
        const rotateY = (centerX - x) / 10;

        card.style.transform = `rotateX(${rotateX}deg) rotateY(${rotateY}deg) ${isFlipped ? 'rotateY(180deg)' : ''}`;
    });

    container.addEventListener('mouseleave', () => {
        card.style.transform = isFlipped ? 'rotateY(180deg)' : 'rotateY(0)';
    });
});