<!DOCTYPE html>
<html lang="en">
<!-- coding by @_.codedevotee -->

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login Register form</title>

    <!--Boxicons CDN-->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css'
        rel='stylesheet'>

    <!--Custom CSS-->
    <link rel="stylesheet" href="{{asset('backend/css/login.css')}}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-toast-plugin/1.3.2/jquery.toast.min.css">

</head>

<body>
    <!-- FOR SOURCE CODE COMMENT "CODE" -->

    <div class="wrapper">
        <span class="rotate-bg"></span>
        <span class="rotate-bg2"></span>

        <div class="form-box login">
            <h2 class="title animation" style="--i:0; --j:21">Login</h2>
            <form id="adminLoginForm">
                <div class="input-box animation" style="--i:1; --j:22">
                    <input type="email" required name="username">
                    <label for="">Username</label>
                    <i class='bx bxs-user'></i>
                </div>

                <div class="input-box animation" style="--i:2; --j:23">
                    <input type="password" required name="password">
                    <label for="">Password</label>
                    <i class='bx bxs-lock-alt'></i>
                </div>
                <button type="submit" id="adminBtn" class="btn animation" style="--i:3; --j:24">Login</button>
                <!-- <div class="linkTxt animation" style="--i:5; --j:25">
                    <p>Don't have an account? <a href="#" class="register-link">Sign Up</a></p>
                </div> -->
            </form>
        </div>

        <div class="info-text login">
            <h2 class="animation" style="--i:0; --j:20">Welcome Back!</h2>
            <p class="animation" style="--i:1; --j:21">Glad to see you again! Access your dashboard and manage easily.</p>
        </div>
    </div>

    <!--Script.js-->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="{{asset('backend/js/login.js')}}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-toast-plugin/1.3.2/jquery.toast.min.js"></script>
</body>

</html>