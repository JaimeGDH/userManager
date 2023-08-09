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
                            <div class="card-header">
                                @auth
                                    Users
                                @else
                                    Login
                                @endauth
                            </div>
                            <div class="card-body">
                                @auth
                                    <!-- Aquí muestra la tabla con los datos de los usuarios -->
                                @else
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
                                @endauth
                            </div>
                            <div class="card-footer">
                                @auth
                                    <!-- No se necesita el enlace de registro cuando el usuario está autenticado -->
                                @else
                                    <p class="mb-0">Don't have an account? <a href="#">Register</a></p>
                                @endauth
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const loginForm = document.querySelector('#login-form');

            loginForm.addEventListener('submit', async function (event) {
                event.preventDefault();

                const formData = new FormData(loginForm);

                try {
                    const response = await fetch('/api/login', {
                        method: 'POST',
                        body: formData,
                    });

                    const data = await response.json();

                    if (response.ok) {
                        localStorage.setItem('access_token', data.access_token);
                        console.log('Login successful', data);
                    } else {
                        console.error('Login failed', data);
                    }
                } catch (error) {
                    console.error('An error occurred', error);
                }
            });
        });
    </script>
</body>
</html>
