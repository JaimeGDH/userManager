## Instrucciones

Importar proyecto
```
git clone git@github.com:JaimeGDH/userManager.git userManager
```

Instalar repositorios
```
composer install
```

Generar llave secreta para JWT
```
php artisan jwt:secret
```

Revisar si en el .env se agregó JWT_SECRET=81S... y JWT_ALGO=HS256


Compilar proyecto
```
npm run dev
```

Ejecutar proyecto
```
npm run serve
```

## MockApi

Se utilizó el siguiente proyecto mockApi
```
https://mockapi.io/clone/64d25c51f8d60b174361f0a7
```

# Definiciones de rutas de API
userManager/routes/api.php

# Controlador para API
userManager/app/Http/Controllers/API/AuthController.php

# Vista que utiliza la API
userManager/resources/views/welcome.blade.php

## Tecnologías

PHP 8.1.2

Laravel 10

Para generación de JWT se utilizó: php-open-source-saver/jwt-auth": "^2.1

