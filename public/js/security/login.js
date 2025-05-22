function togglePassword() {
    var passwordField = document.getElementById('password');
    var toggleIcon = document.querySelector('.password-toggle i');
    if (passwordField.type === 'password') {
        passwordField.type = 'text';
        toggleIcon.classList.remove('fa-eye');
        toggleIcon.classList.add('fa-eye-slash');
    } else {
        passwordField.type = 'password';
        toggleIcon.classList.remove('fa-eye-slash');
        toggleIcon.classList.add('fa-eye');
    }
}

// Afficher l'icône quand l'utilisateur tape dans le champ mot de passe
document.getElementById('password').addEventListener('input', function() {
    var passwordField = document.getElementById('password');
    var toggleIcon = document.querySelector('.password-toggle');
    if (passwordField.value.length > 0) {
        toggleIcon.style.visibility = 'visible';
    } else {
        toggleIcon.style.visibility = 'hidden';
    }
});