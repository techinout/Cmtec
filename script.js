const container = document.getElementById('container');
const registerBtn = document.getElementById('register');
const loginBtn = document.getElementById('login');

// Alternar entre as telas de login e registro
registerBtn.addEventListener('click', () => {
    container.classList.add("active");
});

loginBtn.addEventListener('click', () => {
    container.classList.remove("active");
});

// Validar email com domínio específico
function isValidEmail(email) {
    const regex = /^[a-zA-Z0-9._%+-]+@etec\.sp\.gov\.br$/;
    return regex.test(email);
}

// Validar formulário de registro
document.getElementById('signup-form').addEventListener('submit', (e) => {
    const email = document.getElementById('signup-email').value;
    if (!isValidEmail(email)) {
        e.preventDefault(); // Impede o envio do formulário
        alert('O e-mail deve ser do domínio @etec.sp.gov.br');
    }
});

// Validar formulário de login
document.getElementById('login-form').addEventListener('submit', (e) => {
    const email = document.getElementById('login-email').value;
    if (!isValidEmail(email)) {
        e.preventDefault(); // Impede o envio do formulário
        alert('O e-mail deve ser do domínio @etec.sp.gov.br');
    }
});

