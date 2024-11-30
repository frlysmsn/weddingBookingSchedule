document.addEventListener('DOMContentLoaded', function() {
    const tabBtns = document.querySelectorAll('.auth-tab-btn');
    const tabContents = document.querySelectorAll('.auth-tab-content');

    tabBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            // Remove active class from all buttons
            tabBtns.forEach(b => b.classList.remove('active'));
            // Add active class to clicked button
            btn.classList.add('active');
            
            // Hide all tab contents
            tabContents.forEach(content => content.classList.add('hidden'));
            // Show selected tab content
            document.getElementById(`${btn.dataset.tab}-tab`).classList.remove('hidden');
        });
    });
}); 