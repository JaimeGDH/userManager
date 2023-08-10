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

    <style>
        /* Estilos específicos definidos en línea */
        .logged-in-content {
            display: none;
        }
    </style>
</head>
<body>
    <div id="app"> 
        <button id="logout-button" class="btn btn-secondary btn-logged" style="position: absolute; top: 10px; right: 10px;" style="display: none;">Logout</button>
        <div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="createModalLabel">Crear Usuario</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="create-form">
                            <div class="mb-3">
                                <label for="create-name" class="form-label">Nombre</label>
                                <input type="text" class="form-control" id="create-name" name="create-name" required>
                            </div>
                            <div class="mb-3">
                                <label for="create-email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="create-email" name="create-email" required>
                            </div>
                            <div class="mb-3">
                                <label for="create-password" class="form-label">Contraseña</label>
                                <input type="password" class="form-control" id="create-password" name="create-password" required>
                            </div>
                            <div class="mb-3">
                                <label for="create-password-confirmation" class="form-label">Confirmar Contraseña</label>
                                <input type="password" class="form-control" id="create-password-confirmation" name="create-password-confirmation" required>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-primary" id="create-save-btn">Crear Usuario</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel">Editar Usuario</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="edit-form">
                            <div class="mb-3">
                                <label for="edit-name" class="form-label">Nombre</label>
                                <input type="text" class="form-control" id="edit-name" name="edit-name" required>
                                <div class="text-danger" id="edit-name-error"></div>
                            </div>                            
                            <div class="mb-3">
                                <label for="edit-email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="edit-email" name="edit-email" required>
                                <div class="text-danger" id="edit-email-error"></div>
                            </div>
                            <div class="mb-3">
                                <label for="edit-password" class="form-label">Contraseña</label>
                                <input type="password" class="form-control" id="edit-password" name="edit-password" required>
                                <div class="text-danger" id="edit-password-error"></div>
                            </div>
                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label">Confirmar Contraseña</label>
                                <input type="password" class="form-control" id="password-confirmation" name="password-confirmation" required>
                                <div class="text-danger" id="password-confirmation-error"></div>
                            </div>
                            <input type="hidden" id="edit-user-id" name="edit-user-id">
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-primary" id="edit-save-btn">Guardar Cambios</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="confirmDeleteModalLabel">Confirmar Eliminación</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        ¿Estás seguro de que deseas eliminar este usuario?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-danger" id="confirm-delete-btn">Eliminar</button>
                    </div>
                </div>
            </div>
        </div>

        <main class="container">
            <div class="container mt-4">
                <h1 class="display-4">User Manager</h1>
            </div>
            <div class="container mt-5">
                <div class="row justify-content-center">
                    <div class="col-md-6">
                        <button id="create-user-button" class="btn btn-primary btn-create" style="display: none;">Crear Usuario</button>
                    </div>
                </div>
            </div>
            <div class="container mt-5">
                <div class="row justify-content-center">
                    <div class="col-md-6">
                        <div class="card">
                            <div id="login-container" class="card-body" style="display: none;">
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
                                <table class="user-table table">
                                    <thead>
                                        <tr>
                                            <th scope="col">Name</th>
                                            <th scope="col">Email</th>
                                            <th scope="col">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody class="user-table">
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
<script>
   

    // Función para cargar la lista de usuarios y verificar el token
    function checkTokenAndLoadUserList() {
        const loginContainer = document.querySelector('#login-container');
        const loggedInContent = document.querySelector('.logged-in-content');
        const btnLogout = document.querySelector('.btn-logged');
        const btnCreate = document.querySelector('.btn-create');
        
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
                    btnLogout.style.display = 'block';
                    btnCreate.style.display = 'block';
                    return response.json();
                } else {
                    // Token no válido, borrar token y mostrar formulario de inicio de sesión                        
                    localStorage.removeItem('access_token');
                    loggedInContent.style.display = 'none';
                    loginContainer.style.display = 'block';
                    btnLogout.style.display = 'none';
                    btnCreate.style.display = 'none';
                }
            })
            .then(data => {
                // Agregar usuarios a la tabla
                userTable.innerHTML = '';
                const users = data;

                data.forEach(user => {
                    const row = userTable.insertRow();
                    const nameCell = row.insertCell(0);
                    const emailCell = row.insertCell(1);

                    nameCell.textContent = user.name;
                    emailCell.textContent = user.email;
                    const actionCell = row.insertCell(2);

                    // Crear botones de editar y borrar
                    const editButton = document.createElement('button');
                    editButton.textContent = 'Edit';
                    editButton.classList.add('btn', 'btn-primary', 'btn-sm', 'mx-1', 'edit-button');
                    editButton.onclick = function() {
                        openEditModal(user.id, users); // Llamar a la función con el ID del usuario
                    };

                    const deleteButton = document.createElement('button');
                    deleteButton.textContent = 'Delete';
                    deleteButton.classList.add('btn', 'btn-danger', 'btn-sm');                        
                    deleteButton.dataset.userId = user.id;
                    deleteButton.onclick = function() {
                        openDeleteModal(user.id, users); // Llamar a la función con el ID del usuario
                    };
                    actionCell.appendChild(editButton);
                    actionCell.appendChild(deleteButton);
                });
            })
            .catch(error => {
                console.error('An error occurred while fetching user list', error);
            });
        } else {
            // No hay token, mostrar formulario de inicio de sesión
            loggedInContent.style.display = 'none';
            btnLogout.style.display = 'none';
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

    // Agregar listeners para los botones de editar en cada fila de la tabla
    const editButtons = document.querySelectorAll('.edit-button');
    editButtons.forEach(editButton => {
        editButton.addEventListener('click', () => {
        
            // Lógica para abrir el modal de edición y cargar datos actuales
            const editModal = new bootstrap.Modal(document.getElementById('editModal'));
            const row = editButton.closest('tr');
            const nameCell = row.querySelector('.name-cell');
            const emailCell = row.querySelector('.email-cell');
            const editNameInput = document.getElementById('edit-name');
            const editEmailInput = document.getElementById('edit-email');
            const editPasswordInput = document.getElementById('edit-password');
            const editUserIdInput = document.getElementById('edit-user-id');
            
            // Cargar datos actuales en el formulario
            editNameInput.value = nameCell.textContent.trim();
            editEmailInput.value = emailCell.textContent.trim();
            editPasswordInput.value = '';
            editUserIdInput.value = row.dataset.userId;
            
            // Mostrar el modal
            editModal.show();               
        });
    });

    // Lógica para guardar los cambios al hacer clic en "Guardar Cambios"
    const editSaveBtn = document.getElementById('edit-save-btn');
    editSaveBtn.addEventListener('click', async () => {
        const editForm = document.getElementById('edit-form');
        if (!editForm.checkValidity()) {
            // Mostrar mensajes de validación en campos inválidos
            const invalidFields = editForm.querySelectorAll(':invalid');
            invalidFields.forEach(field => {
                const fieldName = field.getAttribute('name');
                const errorDiv = document.getElementById(`${fieldName}-error`);
                if (errorDiv) {
                    errorDiv.textContent = field.validationMessage;
                }
            });
            
            return;
        }
        
        const editNameInput = document.getElementById('edit-name');
        const editEmailInput = document.getElementById('edit-email');
        const editPasswordInput = document.getElementById('edit-password');
        const editIdInput = document.getElementById('edit-user-id');

        const editedName = editNameInput.value;
        const editedEmail = editEmailInput.value;
        const editedPassword = editPasswordInput.value;
        const userId = editIdInput.value;

        // Realizar la solicitud PATCH al servidor para actualizar los datos del usuario
        try {
            const accessToken = localStorage.getItem('access_token');

            if (accessToken) {
                const editUserIdInput = document.getElementById('edit-user-id');

                const response = await fetch(`/api/me/${userId}`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': `Bearer ${accessToken}`,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        name: editedName,
                        email: editedEmail,
                        password: editedPassword,
                    })
                });
                
                if (response.ok) {
                    // Cerrar el modal después de guardar
                    const editModal = new bootstrap.Modal(document.getElementById('editModal'));                 
                    editModal.hide(); // No se está cerrando

                    checkTokenAndLoadUserList();
                } else {
                    console.error('Error updating user');
                }
            }            
        } catch (error) {
            console.error('An error occurred while updating user', error);
        }
    });
    
    function openEditModal(userId, users) {
        const editModal = new bootstrap.Modal(document.getElementById('editModal'));

        // Buscar el usuario por su ID en la lista completa
        const user = users.find(user => user.id === userId);

        if (user) {
            // Obtener elementos del formulario de edición
            const editNameInput = document.getElementById('edit-name');
            const editEmailInput = document.getElementById('edit-email');
            const editPasswordInput = document.getElementById('edit-password');
            const editUserIdInput = document.getElementById('edit-user-id'); 

            // Cargar datos del usuario en el formulario
            editNameInput.value = user.name;
            editEmailInput.value = user.email;
            editPasswordInput.value = '';
            editUserIdInput.value = user.id;
            // Mostrar el modal
            editModal.show();
        } else {
            console.error('User not found');
        }
    }

    function getUserById(userId) {
        return users.find(user => user.id === userId);
    }

    function openDeleteModal(userId) {
        const confirmDeleteModal = new bootstrap.Modal(document.getElementById('confirmDeleteModal'));
        
        // Mostrar el modal de confirmación
        confirmDeleteModal.show();
        
        const confirmDeleteBtn = document.getElementById('confirm-delete-btn');
        confirmDeleteBtn.addEventListener('click', async () => {
            // Realizar la solicitud DELETE al servidor para eliminar el usuario
            try {
                const accessToken = localStorage.getItem('access_token');
                
                if (accessToken) {
                    const response = await fetch(`/api/delete/${userId}`, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'Authorization': `Bearer ${accessToken}`,
                            'Accept': 'application/json'
                        }
                    });
                    
                    if (response.ok) {
                        // Cerrar el modal de confirmación
                        confirmDeleteModal.hide();
                        
                        // Actualizar la lista de usuarios
                        checkTokenAndLoadUserList();
                    } else {
                        console.error('Error deleting user');
                    }
                }
            } catch (error) {
                console.error('An error occurred while deleting user', error);
            }
        });
    }

    // Agregar listeners para los botones de eliminar en cada fila de la tabla
    const deleteButtons = document.querySelectorAll('.delete-button');
    deleteButtons.forEach(deleteButton => {
        deleteButton.addEventListener('click', () => {
            const userId = deleteButton.dataset.userId;
            openDeleteModal(userId);
        });
    });

    document.addEventListener('DOMContentLoaded', () => {
        // Agregar evento de clic al botón "Logout"
        const logoutButton = document.getElementById('logout-button');
        logoutButton.addEventListener('click', () => {
            // Eliminar el token de acceso almacenado en localStorage
            localStorage.removeItem('access_token');

            window.location.href = '/'; 
        });
    });

    const createUserButton = document.getElementById('create-user-button');
    createUserButton.addEventListener('click', () => {
        const createModal = new bootstrap.Modal(document.getElementById('createModal'));
        createModal.show();
    });

    const createSaveBtn = document.getElementById('create-save-btn');
    createSaveBtn.addEventListener('click', async () => {        
        const createForm = document.getElementById('create-form');
        if (!createForm.checkValidity()) {
            // Mostrar mensajes de validación en campos inválidos
            const invalidFields = createForm.querySelectorAll(':invalid');
            invalidFields.forEach(field => {
                const fieldName = field.getAttribute('name');
                const errorDiv = document.getElementById(`create-${fieldName}-error`);
                if (errorDiv) {
                    errorDiv.textContent = field.validationMessage;
                }
            });

            return;
        }

        const createNameInput = document.getElementById('create-name');
        const createEmailInput = document.getElementById('create-email');
        const createPasswordInput = document.getElementById('create-password');
        const createPasswordConfirmationInput = document.getElementById('create-password-confirmation');

        const newName = createNameInput.value;
        const newEmail = createEmailInput.value;
        const newPassword = createPasswordInput.value;
        const newPasswordConfirmation = createPasswordConfirmationInput.value;

        if (newPassword !== newPasswordConfirmation) {
            // Mostrar mensaje de error si las contraseñas no coinciden
            const errorDiv = document.getElementById('create-password-confirmation-error');
            if (errorDiv) {
                errorDiv.textContent = 'Las contraseñas no coinciden.';
            }
            return;
        }

        // Realizar la solicitud POST al servidor para crear un nuevo usuario
        try {
            const accessToken = localStorage.getItem('access_token');

            const response = await fetch('/api/new', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${accessToken}`,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    name: newName,
                    email: newEmail,
                    password: newPassword,
                })
            });

            if (response.ok) {
                // Cerrar el modal después de crear el usuario
                const createModal = new bootstrap.Modal(document.getElementById('createModal'));
                createModal.hide(); // no se cierra

                checkTokenAndLoadUserList();
            } else {
                console.error('Error creating user');
            }
        } catch (error) {
            console.error('An error occurred while creating user', error);
        }
    });
    </script>
</body>
</html>
