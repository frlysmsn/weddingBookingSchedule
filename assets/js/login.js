document.getElementById('loginForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;
    
    // Add form validation here
    if (!email || !password) {
        alert('Please fill in all fields');
        return;
    }
    
    // Submit the form
    this.submit();
}); 