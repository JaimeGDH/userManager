<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
  
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
  
    <title>{{ config('app.name', 'userManager') }}</title>
  
    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
  
    <!-- Scripts -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-HwwvtgBNo3bZJJLYd8oVXjrBZt8cqVSpeBNS5n7C8IVInixGAoxmnlMuBnhbgrkm" crossorigin="anonymous"></script>
</head>
<body>
    <div id="app"> 
        
        <main class="container">
            <div class="container mt-4">
                <h1 class="display-4">User Manager</h1>
            </div>
            <div class="container mt-5">
                <div class="row justify-content-center">
                    <div class="col-md-6">
                        <div class="card">
                            <div id="login-container" class="card-body">                                
                                <form id="login-form">
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="email" name="email" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="password" class="form-label">Password</label>
                                        <input type="password" class="form-control" id="password" name="password" required>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Login</button>
                                </form>                                
                            </div>
                            <div class="logged-in-content" style="display: none;">
                                <table class="user-table">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Email</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Las filas de usuarios se agregarán aquí dinámicamente -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <!-- ... (encabezado y contenido) ... -->

<script>
        // Función para cargar la lista de usuarios y verificar el token
        function checkTokenAndLoadUserList() {
            const loginContainer = document.querySelector('#login-container');
            const loggedInContent = document.querySelector('.logged-in-content');
            const userTable = document.querySelector('.user-table tbody');
            
            // Obtener el token de acceso almacenado en localStorage
            const accessToken = localStorage.getItem('access_token');
            
            if (accessToken) {
                // Intentar cargar la lista de usuarios
                fetch('/api/list', {
                    headers: {
                        'Authorization': `Bearer ${accessToken}`,
                        'Accept': 'application/json'
                    }
                })
                .then(response => {
                    if (response.ok) {                        
                        // Token válido, mostrar contenido
                        loginContainer.style.display = 'none';
                        loggedInContent.style.display = 'block';

                        return response.json();
                    } else {
                        // Token no válido, borrar token y mostrar formulario de inicio de sesión                        
                        localStorage.removeItem('access_token');
                        loggedInContent.style.display = 'none';
                        loginContainer.style.display = 'block';
                    }
                })
                .then(data => {
                    // Agregar usuarios a la tabla
                    data.forEach(user => {
                        const row = userTable.insertRow();
                        const nameCell = row.insertCell(0);
                        const emailCell = row.insertCell(1);

                        nameCell.textContent = user.name;
                        emailCell.textContent = user.email;
                    });
                })
                .catch(error => {
                    console.error('An error occurred while fetching user list', error);
                });
            } else {
                // No hay token, mostrar formulario de inicio de sesión
                loggedInContent.style.display = 'none';
                loginContainer.style.display = 'block';
            }
        }

        // Función para manejar el envío del formulario de inicio de sesión
        document.querySelector('#login-form').addEventListener('submit', async function (event) {
            event.preventDefault();
            
            const email = document.querySelector('#email').value;
            const password = document.querySelector('#password').value;

            // Realizar solicitud POST para iniciar sesión
            const response = await fetch('/api/login', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    email: email,
                    password: password
                })
            });

            if (response.ok) {
                // Inicio de sesión exitoso, guardar token en localStorage y cargar lista de usuarios
                const data = await response.json();
                localStorage.setItem('access_token', data.access_token);
                checkTokenAndLoadUserList();
            } else {
                // Inicio de sesión fallido, mostrar mensaje de error
                console.error('Login failed');
            }
        });

        document.addEventListener('DOMContentLoaded', checkTokenAndLoadUserList);
    </script>


</body>
</html>
