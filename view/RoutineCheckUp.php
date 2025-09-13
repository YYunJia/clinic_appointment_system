<?php
session_start();
$username_from_session = isset($_SESSION['username']) ? $_SESSION['username'] : '';
?>

<!DOCTYPE html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Routine Checkup</title>
    <link rel="stylesheet" href=".//homepageStyle.css">
    <link rel="stylesheet" href="fontawesome/css/fontawesome.min.css">
    <link rel="stylesheet" href="fontawesome/css/all.min.css">
     <style>
        .auth-buttons {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        .user-menu {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        .btn-outline {
            background: transparent;
            border: 2px solid #667eea;
            color: #667eea;
        }
        .btn-outline:hover {
            background: #667eea;
            color: white;
        }
        .btn-primary {
            background: #667eea;
            color: white;
        }
        .btn-primary:hover {
            background: #5a6fd8;
        }
        .btn-danger {
            background: #dc3545;
            color: white;
        }
        .btn-danger:hover {
            background: #c82333;
        }
        .btn i {
            margin-right: 5px;
        }
    </style>

</head>
<body>
    <div class="navigation-container">
        <div class="logo-section">
            <div class="logo">
                <a class="navbar-logo" href="#">
                    <img src="../image/logowithoutname.png" alt="Logo" style="width:40px;" class="rounded-pill">
                </a>
            </div>
            <div class="name">SMILEMAKER</div>
        </div>

        <div class="navigation-bar">
            <ul>
                <li><a href=".//HomePage_patient.html">Home</a></li>
                <li><a href="#about">About Us</a></li>
                <li class="dropdown">
                    <a href="#" class="dropbtn active">Service</a>
                    <div class="dropdown-content">
                        <a href=".//RoutineCheckUp.php">Routine Check Up/Consultant</a>
                        <a href=".//Whitening.php">Whitening</a>
                        <a href=".//braces.php">Braces</a>
                        <a href=".//Dentures.php">Dentures</a>
                        <a href=".//Filling.php">Tooth Filling</a>
                        <a href=".//Cleaning.php">Scaling and Polishing</a>
                        <a href=".//CanalTreatment.php">Root Canal Treatment</a>
                        <a href=".//CrownsBridges.php">Crowns and Bridges</a>
                        <a href=".//Extraction.php">Tooth Extraction</a>
                    </div>
                </li>
                <li><a href="#contact">Contact Us</a></li>
            </ul>
        </div>

         <div class="action-buttons">
            <!-- Login State -->
            <div id="login-state" class="auth-buttons">
                <button class="btn outlineBtn" onclick="window.location.href = 'login.html'">Login</button>
                <button class="btn btn-primary" onclick="window.location.href = 'register_patient.html'">Register</button>
            </div>

            <!-- Logged In State -->
            <div id="logged-in-state" class="auth-buttons" style="display: none;">
                <div class="user-menu">
                    <button class="btn outlineBtn" id="username-btn" onclick="window.location.href = 'profile.html'">
                        <i class="fas fa-user"></i> <span id="username-text">Username</span>
                    </button>
                    <button class="btn btn-danger" onclick="logout()">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Hero Section -->
    <section class="service-header checkup-header">
        <div class="serviceHeader-container">
            <h1>Routine Dental Check-Up & Consultation</h1>
            <p>Professional dental examinations to maintain optimal oral health and prevent problems before they start.</p>
            <div class="btn-container">
                <a href=".//book_appointment.php?service_category=Checkup&book_type=appointment&username=<?php echo urlencode($username_from_session); ?>" 
                   class="bookBtn apptBtn"> Make Appointment</a>
                <a href=".//consultation.php?service_category=Checkup&book_type=consultation&username=<?php echo urlencode($username_from_session); ?>" 
                   class="bookBtn consultBtn">Book Consultation</a>
            </div>
        </div>
    </section>

    <div class="container">
        <!-- What's Included Section -->
        <section class="content-section">
            <h2 class="section-title">What's Included in Your Check-Up</h2>

            <div class="twocolContainer">
                <div>
                    <p>A routine dental check-up is your first line of defense against oral health problems. Our comprehensive examination includes:</p>
                    <ul class="benefits-list">
                        <li><i class="fas fa-teeth"></i> <strong>Comprehensive Oral Examination</strong> - Thorough assessment of teeth, gums, and mouth</li>
                        <li><i class="fas fa-tooth"></i> <strong>Professional Cleaning</strong> - Removal of plaque and tartar buildup</li>
                        <li><i class="fas fa-x-ray"></i> <strong>Oral Cancer Screening</strong> - Early detection of abnormalities</li>
                        <li><i class="fas fa-camera"></i> <strong>Digital X-Rays</strong> - When needed for proper diagnosis</li>
                        <li><i class="fas fa-comment-medical"></i> <strong>Personalized Advice</strong> - Tailored recommendations for your oral health</li>
                    </ul>
                </div>
                <div class="content-image">
                    <img src="image/dental-checkup.jpg" alt="Dental Check-Up">
                </div>
            </div>
        </section>

        <!-- Why Important Section -->
        <section class="content-section">
            <h2 class="section-title">Why Regular Check-Ups Matter</h2>

            <div class="twocolContainer">
                <div class="content-image">
                    <img src="image/Dental-examination.jpg" alt="Dental Examination">
                </div>
                <div>
                    <p>Regular dental check-ups are essential for maintaining optimal oral health and preventing serious issues:</p>
                    <ul class="benefits-list">
                        <li><i class="fas fa-shield-alt"></i> <strong>Preventative Care</strong> - Catch problems early before they become serious</li>
                        <li><i class="fas fa-money-bill-wave"></i> <strong>Cost Savings</strong> - Prevent expensive treatments with early detection</li>
                        <li><i class="fas fa-smile"></i> <strong>Confidence</strong> - Maintain a healthy, beautiful smile</li>
                        <li><i class="fas fa-stethoscope"></i> <strong>Overall Health</strong> - Oral health is connected to your general wellbeing</li>
                    </ul>
                    <p>We recommend routine check-ups every six months, though your dentist may suggest a different schedule based on your individual needs.</p>
                </div>
            </div>
        </section>

        <!-- Process Section -->
        <section class="content-section">
            <h2 class="section-title">The Check-Up Process</h2>

            <div class="process-steps">
                <div class="step-card">
                    <h1 class="numbers">&#49;</h1>
                    <h3>Medical History Review</h3>
                    <p>We'll discuss any changes to your health or medications that might affect your oral health.</p>
                </div>

                <div class="step-card">
                    <h1 class="numbers">&#50;</h1>
                    <h3>Examination</h3>
                    <p>Thorough inspection of your teeth, gums, mouth, and jaw for any signs of issues.</p>
                </div>

                <div class="step-card">
                    <h1 class="numbers">&#51;</h1>
                    <h3>Cleaning</h3>
                    <p>Professional removal of plaque and tartar, followed by polishing your teeth.</p>
                </div>

                <div class="step-card">
                    <h1 class="numbers">&#52;</h1>
                    <h3>Consultation</h3>
                    <p>Discussion of findings and personalized recommendations for your oral care.</p>
                </div>
            </div>
        </section>
    </div>

    <footer class="footer">
        <div class="footer-container">
            
             <div class="footer-clinic">
                    <div class="footer-logo">
                        <img src="image/logowithnamewhite.png" alt="Logo" style="width:50px;">
                        <div class="footer-name">SmileMaker Dental</div>
                    </div>
                    <p class="footer-intro">
                        Providing comprehensive healthcare services with compassion and excellence since 2005. Our team of board-certified physicians is dedicated to your wellbeing.
                    </p>
                </div>

            <!-- Contact Info -->
            <div class="footer-contact">
                <p><strong>Contact Us</strong></p>
                <p>123 Dental Street, City</p>
                <p>03-19204819</p>
                <p>SmileMakerDental@gmail.com</p>
            </div>

            <div class="footer-operation">
                <h3>Opening Hours</h3>
                <p>Monday - Friday: 8:00 AM - 6:00 PM</p>
                <p>Saturday - Sunday: 9:00 AM - 8:00 PM</p>
            </div>

            <!-- Quick Links -->
            <div class="footer-links">
                <p><strong>Quick Links</strong></p>
                <a href=".//HomePage_patient.html">Home</a>
                <a href="#about">About Us</a>
                <a href="#services">Services</a>
                <a href="#contact">Contact Us</a>
            </div>

        </div>

        <div class="footer-bottom">
            <p>&copy; 2025 SmileMaker Dental Clinic. All rights reserved.</p>
        </div>
    </footer>
    <script>
        function checkLoginStatus() {
            const isLoggedIn = sessionStorage.getItem('isLoggedIn') === 'true';
            const username = sessionStorage.getItem('username');
            const userType = sessionStorage.getItem('userType'); 

            if (isLoggedIn && username) {
                document.getElementById('login-state').style.display = 'none';
                document.getElementById('logged-in-state').style.display = 'block';
                document.getElementById('username-text').textContent = username;

                const profileBtn = document.getElementById('username-btn');
                if (userType === 'admin') {
                    profileBtn.onclick = () => window.location.href = '../view/admin_dashboard.html';
                } else if (userType === 'doctor') {
                    profileBtn.onclick = () => window.location.href = '../view/doctor_dashboard.html';
                } else {
                    profileBtn.onclick = () => window.location.href = '../view/profile.html?username=' + encodeURIComponent(username);
                }
            } else {
                document.getElementById('login-state').style.display = 'block';
                document.getElementById('logged-in-state').style.display = 'none';
            }
        }

        function logout() {
            sessionStorage.clear();
            window.location.href = '../auth/logout.php';
        }

        document.addEventListener('DOMContentLoaded', function () {
            const urlParams = new URLSearchParams(window.location.search);
            const loginSuccess = urlParams.get('login');
            const logoutSuccess = urlParams.get('logout');
            const username = urlParams.get('username');
            const userType = urlParams.get('role');

            if (loginSuccess === 'success' && username && userType) {
                sessionStorage.setItem('isLoggedIn', 'true');
                sessionStorage.setItem('username', username);
                sessionStorage.setItem('userType', userType);
                window.history.replaceState({}, document.title, window.location.pathname);
            } else if (logoutSuccess === 'success') {
                sessionStorage.clear();
                window.history.replaceState({}, document.title, window.location.pathname);
            }

            checkLoginStatus();
        });

        window.addEventListener('storage', function (e) {
            if (e.key === 'isLoggedIn') {
                checkLoginStatus();
            }
        });
    </script><script>
        function checkLoginStatus() {
            const isLoggedIn = sessionStorage.getItem('isLoggedIn') === 'true';
            const username = sessionStorage.getItem('username');
            const userType = sessionStorage.getItem('userType'); 

            if (isLoggedIn && username) {
                document.getElementById('login-state').style.display = 'none';
                document.getElementById('logged-in-state').style.display = 'block';
                document.getElementById('username-text').textContent = username;

                const profileBtn = document.getElementById('username-btn');
                if (userType === 'admin') {
                    profileBtn.onclick = () => window.location.href = '../view/admin_dashboard.html';
                } else if (userType === 'doctor') {
                    profileBtn.onclick = () => window.location.href = '../view/doctor_dashboard.html';
                } else {
                    profileBtn.onclick = () => window.location.href = '../view/profile.html?username=' + encodeURIComponent(username);
                }
            } else {
                document.getElementById('login-state').style.display = 'block';
                document.getElementById('logged-in-state').style.display = 'none';
            }
        }

        function logout() {
            sessionStorage.clear();
            window.location.href = '../auth/logout.php';
        }

        document.addEventListener('DOMContentLoaded', function () {
            const urlParams = new URLSearchParams(window.location.search);
            const loginSuccess = urlParams.get('login');
            const logoutSuccess = urlParams.get('logout');
            const username = urlParams.get('username');
            const userType = urlParams.get('role');

            if (loginSuccess === 'success' && username && userType) {
                sessionStorage.setItem('isLoggedIn', 'true');
                sessionStorage.setItem('username', username);
                sessionStorage.setItem('userType', userType);
                window.history.replaceState({}, document.title, window.location.pathname);
            } else if (logoutSuccess === 'success') {
                sessionStorage.clear();
                window.history.replaceState({}, document.title, window.location.pathname);
            }

            checkLoginStatus();
        });

        window.addEventListener('storage', function (e) {
            if (e.key === 'isLoggedIn') {
                checkLoginStatus();
            }
        });
    </script>
</body>
</html>

