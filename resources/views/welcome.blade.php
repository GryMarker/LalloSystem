<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LalloCare Login</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- AOS Animation Library -->
    <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">

    <style>
        html,
        body {
            height: 100%;
            overflow-x: hidden;
        }


        body {
            background: linear-gradient(135deg, #e8f5e9, #ffffff);
            font-family: 'Segoe UI', sans-serif;
            overflow-x: hidden;
            position: relative;
        }

        .floating-icon {
            position: absolute;
            opacity: 0.07;
            animation: float-up 20s linear infinite;
            pointer-events: none;
            z-index: 0;
        }

        @keyframes float-up {
            0% {
                transform: translateY(100vh);
                opacity: 0;
            }

            10% {
                opacity: 0.1;
            }

            50% {
                opacity: 0.12;
            }

            90% {
                opacity: 0.1;
            }

            100% {
                transform: translateY(-150px);
                opacity: 0;
            }
        }

        .login-card {
            border: none;
            border-radius: 15px;
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.6s ease;
            z-index: 10;
            position: relative;
        }

        .login-card.show {
            opacity: 1;
            transform: translateY(0);
        }

        .login-header {
            font-weight: bold;
            color: #2e7d32;
            font-size: 1.8rem;
            min-height: 50px;
        }

        .btn-primary {
            background-color: #43a047;
            border-color: #43a047;
        }

        .btn-primary:hover {
            background-color: #388e3c;
            border-color: #388e3c;
            box-shadow: 0 0 10px rgba(56, 142, 60, 0.4);
        }

        .loader {
            border: 3px solid #c8e6c9;
            border-top: 3px solid #2e7d32;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            animation: spin 0.8s linear infinite;
            display: inline-block;
            vertical-align: middle;
            margin-right: 8px;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .backdrop-blur {
            backdrop-filter: blur(10px);
            background-color: rgba(255, 255, 255, 0.6) !important;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
            border-radius: 20px;
        }

        .form-floating>.form-control {
            border-radius: 10px;
            background-color: rgba(255, 255, 255, 0.8);
        }

        .form-floating>label {
            color: #4caf50;
            font-weight: 500;
        }

        .login-header {
            font-weight: bold;
            color: #2e7d32;
            font-size: 1.8rem;
            min-height: 50px;
        }

        .btn-primary {
            background-color: #43a047;
            border-color: #43a047;
            border-radius: 10px;
            font-weight: 500;
        }

        .btn-primary:hover {
            background-color: #388e3c;
            border-color: #388e3c;
            box-shadow: 0 0 10px rgba(56, 142, 60, 0.4);
        }
    </style>
</head>

<body class="d-flex align-items-center justify-content-center vh-100 position-relative">

    <div id="floatingContainer"
        style="position: fixed; top: 0; left: 0; width: 100%; height: 100vh; pointer-events: none; z-index: 0;"></div>

    <div class="card shadow-lg border-0 login-card backdrop-blur" id="loginCard" data-aos="zoom-in"
        data-aos-duration="800" style="width: 100%; max-width: 420px; background: rgba(255, 255, 255, 0.65);">

        <div class="text-center px-4 pt-4">

            <div id="typingHeader" class="login-header"></div>
            <p class="text-muted mb-4">Sign in to manage your health and appointments.</p>
        </div>

        <div class="px-4 pb-4">
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <form method="POST" action="{{ route('login') }}" onsubmit="showLoader(event)">
                @csrf

                <div class="form-floating mb-3">
                    <input type="email" class="form-control" id="email" name="email" required autofocus
                        placeholder="Email" aria-label="Email address">
                    <label for="email">Email Address</label>
                </div>

                <div class="form-floating mb-3">
                    <input type="password" class="form-control" id="password" name="password" required
                        placeholder="Password" aria-label="Password">
                    <label for="password">Password</label>
                </div>

                <button id="loginBtn" type="submit" class="btn btn-primary w-100 py-2">
                    Log In
                </button>
            </form>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
    <script>
        AOS.init();
    </script>


    <script>
        const text = "Welcome to LalloCare 🩺";
        const header = document.getElementById("typingHeader");
        let index = 0;

        function type() {
            if (index < text.length) {
                header.innerHTML += text.charAt(index);
                index++;
                setTimeout(type, 50);
            }
        }

        // Start typing and show the login card with delay
        window.onload = () => {
            setTimeout(() => {
                document.getElementById('loginCard').classList.add('show');
                type();
            }, 300);
        };
    </script>

    <!-- Button loader -->
    <script>
        function showLoader(e) {
            const btn = document.getElementById('loginBtn');
            btn.disabled = true;
            btn.innerHTML = '<span class="loader"></span> Logging in...';
        }
    </script>
    <script>
        const container = document.getElementById('floatingContainer');
        const icons = [
            "{{ asset('images/medicine.png') }}",
            "{{ asset('images/shield.png') }}",
            "{{ asset('images/sword.png') }}"
        ];

        const numberOfIcons = 30; // Change this number for more/less

        for (let i = 0; i < numberOfIcons; i++) {
            const img = document.createElement('img');
            img.src = icons[Math.floor(Math.random() * icons.length)];
            img.className = 'floating-icon';
            img.style.top = '100vh';
            img.style.left = `${Math.random() * 100}%`;
            img.style.animationDelay = `${Math.random() * 5}s`;
            img.style.animationDuration = `${6 + Math.random() * 4}s`;
            img.style.width = `${40 + Math.random() * 30}px`;
            container.appendChild(img);
        }
    </script>


</body>

</html>