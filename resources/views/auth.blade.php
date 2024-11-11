<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
</head>
<body>
    <h1>Loginee</h1>
    <form id="loginForm">
        <label for="email">Emailee</label>
        <input type="email" id="email" name="email" required>
        <br>
        <label for="password">Password</label>
        <input type="password" id="password" name="password" required>
        <br>
        <button type="submit">Login</button>
    </form>

    <script>
    document.getElementById('loginForm').addEventListener('submit', async function(event) {
        event.preventDefault();
        
        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;

        try {
            // On envoie la requête POST vers /login
            const response = await axios.post('/login', {  // Changer /api/login par /login
                email,
                password
            });
            localStorage.setItem('token', response.data.token);  // Stocker le token dans le stockage local
            alert('Login successful!');
            window.location.href = '/home';  // Rediriger vers la page principale
        } catch (error) {
            alert('Login failed');
        }
    });
</script>

</body>
</html>
