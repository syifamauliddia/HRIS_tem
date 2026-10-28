<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Dashboard Admin</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- FontAwesome Icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gray-100">

<div class="min-h-screen bg-gradient-to-r from-black to-blue-900 py-6 flex flex-col justify-center sm:py-12">
    <div class="relative py-3 sm:max-w-xl sm:mx-auto">
        <div class="absolute inset-0 bg-gradient-to-r from-cyan-400 via-sky-500 to-purple-500 shadow-lg transform -skew-y-6 sm:skew-y-0 sm:-rotate-6 sm:rounded-3xl"></div>
        
        <form id="loginForm" class="relative px-4 py-10 bg-white shadow-lg sm:rounded-3xl sm:p-12">
            <div class="max-w-md mx-auto">
                <div>
                    <h1 class="text-[25pt] font-bold text-center">Login</h1>
                </div>
                <h1 class="text-md text-gray-400 text-center mb-4">Masuk untuk melanjutkan</h1>
                
                <div class="divide-y divide-gray-200">
                    <div class="text-base leading-6 space-y-4 text-gray-700 sm:text-lg sm:leading-7">
                        
                        <!-- Input Username -->
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                                <i class="fas fa-user"></i>
                            </span>
                            <input type="text" id="username" required
                                class="w-full pl-10 pr-4 py-3 border rounded-md text-sm focus:outline-none focus:border-cyan-500" 
                                placeholder="Username">
                        </div>
                        
                        <!-- Input Password -->
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                                <i class="fas fa-lock"></i>
                            </span>
                            <input type="password" id="password" required
                                class="w-full pl-10 pr-4 py-3 border rounded-md text-sm focus:outline-none focus:border-cyan-500" 
                                placeholder="Password">
                        </div>
                        
                        <!-- Submit Button -->
                        <div class="relative pt-2">
                            <button type="submit" id="btnSubmit" 
                                class="bg-cyan-500 w-full text-white rounded-md px-2 py-2 hover:bg-cyan-600 font-semibold transition duration-200">
                                Masuk
                            </button>
                        </div>

                    </div>
                </div>
            </div>
        </form>
    </div>

    <div class="flex justify-end absolute bottom-0 right-0 p-4">
        <p class="text-white text-sm">©2024 Copyright <a class="underline" href="https://qqltech.com" target="_blank">qqltech.com</a>. All Rights Reserved</p>
    </div>
</div>

<script>
    const URL_BACKEND = 'http://localhost:3000/api';

    // Cek Session jika user sudah login
    document.addEventListener('DOMContentLoaded', () => {
        if (localStorage.getItem('user')) {
            window.location.href = '/master-penilaian';
        }
    });

    // Handle Form Submit Login
    document.getElementById('loginForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const btnSubmit = document.getElementById('btnSubmit');
        const usernameInput = document.getElementById('username').value;
        const passwordInput = document.getElementById('password').value;
        const ini_fcm = localStorage.getItem('fcmToken') || '';

        try {
            btnSubmit.disabled = true;
            btnSubmit.innerHTML = 'Memproses...';

            const response = await fetch(`${URL_BACKEND}/login`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    username: usernameInput,
                    password: passwordInput,
                    token_fcm: ini_fcm
                })
            });

            if (!response.ok) {
                throw new Error('Authentication failed');
            }

            const userValue = await response.json();

            // Simpan Session User ke localStorage
            localStorage.setItem('user', JSON.stringify(userValue));

            // Redirect ke Dashboard / Master Penilaian
            window.location.href = '/master-penilaian';

        } catch (err) {
            btnSubmit.disabled = false;
            btnSubmit.innerHTML = 'Masuk';
            
            Swal.fire({
                icon: 'warning',
                text: 'Username atau Password Salah!'
            });
        }
    });
</script>

</body>
</html>