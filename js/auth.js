async function login(email, password) {
    try {
        const response = await fetch('/api/auth.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ email, password })
        });
        const result = await response.json();
        if (result.success) {
            window.location.href = 'dashboard.php';
        } else {
            alert(result.message || 'Login failed');
        }
    } catch (error) {
        console.error('Login Error:', error);
    }
}

// Attach to button
document.querySelector('button[onclick*="login"]').onclick = (e) => {
    e.preventDefault();
    const email = document.querySelector('input[type="text"], input[type="email"]').value;
    const pass = document.querySelector('input[type="password"]').value;
    login(email, pass);
};
