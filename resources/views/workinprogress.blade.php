<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Developers Working Hard</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Arial', sans-serif;
        }
        
        body {
            background-color: #1e1e2e;
            color: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            overflow: hidden;
        }
        
        .container {
            text-align: center;
            position: relative;
            width: 100%;
            max-width: 900px;
        }
        
        h1 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            color: #89b4fa;
            text-shadow: 0 0 10px rgba(137, 180, 250, 0.5);
        }
        
        p {
            font-size: 1.2rem;
            margin-bottom: 2rem;
            color: #cdd6f4;
        }
        
        .progress-container {
            width: 100%;
            height: 20px;
            background-color: #313244;
            border-radius: 10px;
            overflow: hidden;
            position: relative;
            margin-bottom: 2rem;
        }
        
        .progress-bar {
            height: 100%;
            width: 0%;
            background: linear-gradient(90deg, #f38ba8, #89b4fa);
            border-radius: 10px;
            transition: width 0.5s ease;
        }
        
        .percentage {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-weight: bold;
            color: #cdd6f4;
            text-shadow: 0 0 5px rgba(0, 0, 0, 0.5);
        }
        
        .developers-container {
            display: flex;
            justify-content: space-around;
            margin: 30px 0;
            height: 300px;
            position: relative;
        }
        
        .developer {
            width: 180px;
            height: 240px;
            position: relative;
        }
        
        .desk {
            position: absolute;
            bottom: 0;
            width: 180px;
            height: 80px;
            background-color: #45475a;
            border-radius: 5px 5px 0 0;
        }
        
        .laptop {
            position: absolute;
            bottom: 80px;
            left: 40px;
            width: 100px;
            height: 60px;
            background-color: #313244;
            border-radius: 5px;
            transform-origin: bottom center;
            animation: laptop-movement 3s infinite;
        }
        
        .laptop-screen {
            position: absolute;
            bottom: 60px;
            width: 100px;
            height: 70px;
            background-color: #585b70;
            border-radius: 5px 5px 0 0;
            overflow: hidden;
        }
        
        .laptop-screen::before {
            content: '';
            position: absolute;
            width: 100%;
            height: 100%;
            background: linear-gradient(transparent, #89b4fa, transparent);
            transform: translateY(-100%);
            animation: code-animation 3s infinite;
        }
        
        .developer-icon {
            position: absolute;
            bottom: 140px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 100px;
            animation: working 2s infinite ease-in-out;
        }
        
        .head {
            position: absolute;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 40px;
            height: 40px;
            background-color: #f9e2af;
            border-radius: 50%;
        }
        
        .body {
            position: absolute;
            top: 40px;
            left: 50%;
            transform: translateX(-50%);
            width: 50px;
            height: 60px;
            background-color: #74c7ec;
            border-radius: 10px;
        }
        
        .arm {
            position: absolute;
            width: 12px;
            height: 40px;
            background-color: #f9e2af;
            border-radius: 6px;
        }
        
        .arm-left {
            top: 45px;
            left: 12px;
            transform-origin: top center;
            animation: typing-left 1s infinite;
        }
        
        .arm-right {
            top: 45px;
            right: 12px;
            transform-origin: top center;
            animation: typing-right 1.2s infinite;
        }
        
        .coffee {
            position: absolute;
            bottom: 80px;
            right: 20px;
            width: 20px;
            height: 25px;
            background-color: #a6e3a1;
            border-radius: 0 0 5px 5px;
        }
        
        .coffee::before {
            content: '';
            position: absolute;
            top: 5px;
            left: 50%;
            transform: translateX(-50%);
            width: 10px;
            height: 10px;
            background-color: #1e1e2e;
            border-radius: 50%;
        }
        
        .coffee-steam {
            position: absolute;
            bottom: 105px;
            right: 25px;
            width: 10px;
            height: 15px;
            border-radius: 10px;
            background-color: rgba(166, 227, 161, 0.3);
            animation: steam 2s infinite;
        }
        
        .code-block {
            position: absolute;
            bottom: 220px;
            width: 60px;
            height: 40px;
            background-color: #313244;
            border-radius: 5px;
            padding: 5px;
            font-family: monospace;
            font-size: 6px;
            color: #a6e3a1;
            text-align: left;
            opacity: 0;
            animation: code-popup 5s infinite;
        }
        
        .dev-1 .code-block {
            left: 30px;
            animation-delay: 1s;
        }
        
        .dev-2 .code-block {
            left: 30px;
            animation-delay: 2s;
        }
        
        .dev-3 .code-block {
            left: 30px;
            animation-delay: 3s;
        }
        
        .status-update {
            position: absolute;
            bottom: -30px;
            left: 0;
            width: 100%;
            text-align: center;
            font-size: 0.8rem;
            color: #cdd6f4;
        }
        
        @keyframes typing-left {
            0%, 100% {
                transform: rotate(-20deg);
            }
            50% {
                transform: rotate(-30deg);
            }
        }
        
        @keyframes typing-right {
            0%, 100% {
                transform: rotate(20deg);
            }
            50% {
                transform: rotate(30deg);
            }
        }
        
        @keyframes working {
            0%, 100% {
                transform: translateX(-50%) translateY(0);
            }
            50% {
                transform: translateX(-50%) translateY(-5px);
            }
        }
        
        @keyframes laptop-movement {
            0%, 100% {
                transform: rotateX(0deg);
            }
            50% {
                transform: rotateX(5deg);
            }
        }
        
        @keyframes code-animation {
            0% {
                transform: translateY(-100%);
            }
            100% {
                transform: translateY(100%);
            }
        }
        
        @keyframes steam {
            0%, 100% {
                opacity: 0;
                transform: translateY(0);
            }
            50% {
                opacity: 0.7;
                transform: translateY(-10px);
            }
        }
        
        @keyframes code-popup {
            0%, 70%, 100% {
                opacity: 0;
                transform: translateY(0) scale(0.8);
            }
            10%, 60% {
                opacity: 1;
                transform: translateY(-10px) scale(1);
            }
        }
        
        .particles {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
        }
        
        .particle {
            position: absolute;
            background-color: rgba(137, 180, 250, 0.2);
            border-radius: 50%;
            pointer-events: none;
        }
    </style>
</head>
<body>
    <div class="particles"></div>
    <div class="container">
        <h1>We Are Working Hard</h1>
        <p>Our developer team is building an amazing system for you</p>
        
        <div class="progress-container">
            <div class="progress-bar"></div>
            <div class="percentage">0%</div>
        </div>
        
        <div class="developers-container">
            <div class="developer dev-1">
                <div class="desk"></div>
                <div class="laptop"></div>
                <div class="laptop-screen"></div>
                <div class="developer-icon">
                    <div class="head"></div>
                    <div class="body"></div>
                    <div class="arm arm-left"></div>
                    <div class="arm arm-right"></div>
                </div>
                <div class="coffee"></div>
                <div class="coffee-steam"></div>
                <div class="code-block">
                    function init() {<br>
                    &nbsp;&nbsp;setup();<br>
                    &nbsp;&nbsp;render();<br>
                    }
                </div>
                <div class="status-update">Working on frontend</div>
            </div>
            
            <div class="developer dev-2">
                <div class="desk"></div>
                <div class="laptop"></div>
                <div class="laptop-screen"></div>
                <div class="developer-icon">
                    <div class="head"></div>
                    <div class="body" style="background-color: #f38ba8;"></div>
                    <div class="arm arm-left"></div>
                    <div class="arm arm-right"></div>
                </div>
                <div class="coffee"></div>
                <div class="coffee-steam"></div>
                <div class="code-block">
                    class System {<br>
                    &nbsp;&nbsp;constructor() {<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;this.init();<br>
                    &nbsp;&nbsp;}<br>
                    }
                </div>
                <div class="status-update">Building database</div>
            </div>
            
            <div class="developer dev-3">
                <div class="desk"></div>
                <div class="laptop"></div>
                <div class="laptop-screen"></div>
                <div class="developer-icon">
                    <div class="head"></div>
                    <div class="body" style="background-color: #a6e3a1;"></div>
                    <div class="arm arm-left"></div>
                    <div class="arm arm-right"></div>
                </div>
                <div class="coffee"></div>
                <div class="coffee-steam"></div>
                <div class="code-block">
                    async function api() {<br>
                    &nbsp;&nbsp;const res = await<br>
                    &nbsp;&nbsp;fetch('/data');<br>
                    }
                </div>
                <div class="status-update">Creating API</div>
            </div>
        </div>
    </div>

    <script>
        // Progress bar animation
        const progressBar = document.querySelector('.progress-bar');
        const percentage = document.querySelector('.percentage');
        let progress = 0;
        
        function updateProgress() {
            if (progress < 98) {
                progress += Math.random() * 2;
                if (progress > 98) progress = 98;
                progressBar.style.width = progress + '%';
                percentage.textContent = Math.floor(progress) + '%';
                setTimeout(updateProgress, Math.random() * 2000 + 500);
            }
        }
        
        // Start progress animation after a delay
        setTimeout(updateProgress, 1000);
        
        // Create background particles
        const particlesContainer = document.querySelector('.particles');
        const particleCount = 30;
        
        for (let i = 0; i < particleCount; i++) {
            createParticle();
        }
        
        function createParticle() {
            const particle = document.createElement('div');
            particle.classList.add('particle');
            
            // Random size between 2 and 5 pixels
            const size = Math.random() * 3 + 2;
            particle.style.width = size + 'px';
            particle.style.height = size + 'px';
            
            // Random position
            const posX = Math.random() * window.innerWidth;
            const posY = Math.random() * window.innerHeight;
            particle.style.left = posX + 'px';
            particle.style.top = posY + 'px';
            
            // Random color
            const colors = ['rgba(137, 180, 250, 0.3)', 'rgba(243, 139, 168, 0.3)', 'rgba(166, 227, 161, 0.3)'];
            particle.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
            
            // Add to DOM
            particlesContainer.appendChild(particle);
            
            // Animate particle
            animateParticle(particle);
        }
        
        function animateParticle(particle) {
            // Get current position
            const style = window.getComputedStyle(particle);
            let x = parseFloat(style.left);
            let y = parseFloat(style.top);
            
            // Random direction and speed
            const speedX = Math.random() * 0.5 - 0.25;
            const speedY = Math.random() * 0.5 - 0.25;
            
            function move() {
                x += speedX;
                y += speedY;
                
                // Wrap around screen
                if (x < 0) x = window.innerWidth;
                if (x > window.innerWidth) x = 0;
                if (y < 0) y = window.innerHeight;
                if (y > window.innerHeight) y = 0;
                
                particle.style.left = x + 'px';
                particle.style.top = y + 'px';
                
                requestAnimationFrame(move);
            }
            
            move();
        }
        
        // Update status text periodically
        const statusTexts = [
            ['Working on frontend', 'Building components', 'Optimizing UI', 'Testing interface'],
            ['Building database', 'Creating schema', 'Optimizing queries', 'Setting up migrations'],
            ['Creating API', 'Setting up routes', 'Adding security', 'Writing documentation']
        ];
        
        const statusElements = document.querySelectorAll('.status-update');
        
        function updateStatus() {
            statusElements.forEach((element, index) => {
                const texts = statusTexts[index];
                const randomText = texts[Math.floor(Math.random() * texts.length)];
                element.textContent = randomText;
            });
            
            setTimeout(updateStatus, 3000);
        }
        
        // Start updating status after a delay
        setTimeout(updateStatus, 3000);
    </script>
</body>
</html>