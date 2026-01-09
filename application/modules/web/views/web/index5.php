<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Login</title>

    <!-- Bootstrap 4 -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

    <style>
        html, body {
            height: 100%;
            margin: 0;
            font-family: 'Poppins', sans-serif;
            overflow: hidden;
        }

        /* ===== Background ===== */
        .bg-animated {
            position: fixed;
            width: 100%;
            height: 100%;
            background: linear-gradient(120deg, #0f2027, #203a43, #2c5364);
            z-index: -2;
        }

        /* SVG Wave */
        .wave-wrapper {
            position: fixed;
            bottom: 0;
            width: 100%;
            height: 45%;
            overflow: hidden;
            z-index: -1;
        }

        .wave-wrapper svg {
            width: 200%;
            height: 100%;
        }

        /* ===== Login Card ===== */
        .login-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 15px;
        }

        .login-card {
            width: 100%;
            max-width: 380px;
            background: rgba(255,255,255,0.95);
            border-radius: 12px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.25);
            padding: 30px;
            animation: fadeInUp .8s ease;
        }

        @keyframes fadeInUp {
            from {opacity: 0; transform: translateY(30px);}
            to {opacity: 1; transform: translateY(0);}
        }

        .logo {
            text-align: center;
            margin-bottom: 20px;
        }

        .logo img {
            max-height: 70px;
        }

        .form-control {
            border-radius: 30px;
            padding-left: 20px;
        }

        .btn-login {
            border-radius: 30px;
            font-weight: 600;
        }

        /* Captcha */
        .captcha-box {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .captcha-text {
            background: #222;
            color: #fff;
            padding: 6px 12px;
            border-radius: 6px;
            font-weight: bold;
            letter-spacing: 2px;
        }

        /* Controls */
        .motion-control {
            position: fixed;
            top: 15px;
            right: 15px;
            z-index: 10;
        }

        .motion-control button {
            border-radius: 20px;
            font-size: 12px;
        }
        .vanta-bg {
            position: fixed;
            width: 100%;
            height: 100%;
            z-index: -3;
        }
    </style>
</head>

<body id="#vantaFog">


<div class="bg-animated"></div>

<!-- SVG WAVE -->
<div class="wave-wrapper">
    <svg viewBox="0 0 1440 320" preserveAspectRatio="none">
        <path id="wavePath" fill="#ffffff33" d="
    M0,160
    C120,180 240,220 360,200
    C480,180 600,120 720,130
    C840,140 960,200 1080,190
    C1200,180 1320,130 1440,140
    L1440,320 L0,320 Z">
        </path>
    </svg>
</div>

<!-- Play / Pause -->
<div class="motion-control">
    <button id="toggleAnim" class="btn btn-light btn-sm">Pause</button>
</div>

<!-- LOGIN -->
<div class="login-wrapper">
    <div class="login-card">

        <div class="logo">
            <img src="logo.png" alt="Company Logo">
        </div>

        <form>
            <div class="form-group">
                <input type="text" class="form-control" placeholder="Username">
            </div>

            <div class="form-group">
                <input type="password" class="form-control" placeholder="Password">
            </div>

            <div class="form-group">
                <div class="captcha-box">
                    <div class="captcha-text" id="captchaCode"></div>
                    <button type="button" id="resetCaptcha" class="btn btn-sm btn-outline-secondary">↻</button>
                </div>
            </div>

            <div class="form-group">
                <input type="text" class="form-control" placeholder="Enter Captcha">
            </div>

            <button type="submit" class="btn btn-primary btn-block btn-login">Login</button>
        </form>

    </div>
</div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r121/three.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/vanta@latest/dist/vanta.fog.min.js"></script>

<script>
    /* ===== Captcha ===== */
    function generateCaptcha() {
        let chars = "ABCDEFGHJKLMNPQRSTUVWXYZ23456789";
        let captcha = "";
        for (let i = 0; i < 5; i++) {
            captcha += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        $("#captchaCode").text(captcha);
    }
    generateCaptcha();
    $("#resetCaptcha").click(generateCaptcha);

    /* ===== SVG Wave Morph ===== */
    let running = true;
    let t = 0;

    const paths = [
        `M0,160 C120,180 240,220 360,200 C480,180 600,120 720,130 C840,140 960,200 1080,190 C1200,180 1320,130 1440,140 L1440,320 L0,320 Z`,
        `M0,170 C150,140 300,180 420,170 C540,160 660,220 780,210 C900,200 1020,150 1140,160 C1260,170 1380,210 1440,200 L1440,320 L0,320 Z`
    ];

    function animateWave() {
        if (!running) return;
        t += 0.01;
        let index = Math.floor((Math.sin(t) + 1) / 2);
        $("#wavePath").attr("d", paths[index]);
        requestAnimationFrame(animateWave);
    }
    animateWave();

    /* ===== Play / Pause ===== */
    $("#toggleAnim").click(function () {
        running = !running;
        $(this).text(running ? "Pause" : "Play");
        if (running) animateWave();
    });
</script>

<script>
    let vantaEffect = null;

    function initVanta() {
        if (vantaEffect) vantaEffect.destroy();

        vantaEffect = VANTA.FOG({
            el: "#vantaFog",
            mouseControls: true,
            touchControls: true,
            gyroControls: false,
            minHeight: 200.00,
            minWidth: 200.00,
            highlightColor: 0xdcfc28,
            midtoneColor: 0x00c2ff,
            lowlightColor: 0xef00ff,
            blurFactor: 0.5,
            speed: 1.2,
            zoom: 0.8
        });
    }

    $(document).ready(function () {
        initVanta();
    });
</script>

</body>
</html>
