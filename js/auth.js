async function handleAuth(e, action) {
    e.preventDefault();
    const formData = action === 'login' ? 
        { action, username: loginUsername.value, password: loginPassword.value } :
        { action, username: regUsername.value, email: regEmail.value, password: regPassword.value };

    const res = await fetch('api/auth.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(formData)
    });

    const result = await res.json();
    if (result.success) {
        location.reload();
    } else {
        alert(result.error);
    }
}

document.getElementById('loginForm').onsubmit = (e) => handleAuth(e, 'login');
document.getElementById('registerForm').onsubmit = (e) => handleAuth(e, 'register');

function openModal(id) { document.getElementById(id).classList.add('active'); }
function closeModal(id) { document.getElementById(id).classList.remove('active'); }
