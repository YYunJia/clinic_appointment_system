<?php
session_start();
$username_from_session = isset($_SESSION['username']) ? $_SESSION['username'] : '';
?>

<!DOCTYPE html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teeth Whitening</title>
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
    <section class="service-header whitening-header">
        <div class="serviceHeader-container">
            <h1>Professional Teeth Whitening</h1>
            <p>Brighten your smile with our safe and effective professional whitening treatments for a more confident you.</p>
            <div class="btn-container">
                <a href=".//book_appointment.php?service_category=Whitening&book_type=appointment&username=<?php echo urlencode($username_from_session); ?>" 
                   class="bookBtn apptBtn"> Make Appointment</a>
                <a href=".//consultation.php?service_category=Whitening&book_type=consultation&username=<?php echo urlencode($username_from_session); ?>" 
                   class="bookBtn consultBtn">Book Consultation</a>
            </div>
        </div>
    </section>

    <div class="container">
        <!-- What's Included Section -->
        <section class="content-section">
            <h2 class="section-title">Our Whitening Options</h2>

            <div class="twocolContainer">
                <div>
                    <p>We offer professional teeth whitening solutions that are safe, effective, and customized to your needs. Our options include:</p>
                    <ul class="benefits-list">
                         <li><i class="fas fa-wand-magic-sparkles"></i> <strong>In-Office Whitening</strong> - Dramatic results in just one visit</li>
                        <li><i class="fas fa-home"></i> <strong>Take-Home Kits</strong> - Professional-grade whitening at your convenience</li>
                        <li><i class="fas fa-shield-alt"></i> <strong>Enamel-Safe Formulas</strong> - Advanced formulas that protect your teeth</li>
                        <li><i class="fas fa-user-md"></i> <strong>Professional Supervision</strong> - Expert guidance for optimal results</li>
                        <li><i class="fas fa-clipboard-list"></i> <strong>Customized Trays</strong> - Perfect fit for even whitening and comfort</li>
                    </ul>
                </div>
                <div class="content-image">
                    <img src="image/whitening.jpg" alt="Dental Check-Up">
                </div>
            </div>
        </section>

        <!-- Why Important Section -->
        <section class="content-section">
            <h2 class="section-title">Benefits of Professional Whitening</h2>

            <div class="twocolContainer">
                <div class="content-image">
                    <img src="image/whitening-benefit.jpg" alt="Dental Examination">
                </div>
                <div>
                    <p>Professional teeth whitening offers significant advantages over store-bought products:</p>
                    <ul class="benefits-list">
                        <li><i class="fas fa-bolt"></i> <strong>Faster Results</strong> - Noticeable improvement in just one session</li>
                        <li><i class="fas fa-star"></i> <strong>Brighter Outcome</strong> - Professional formulas provide superior whitening</li>
                        <li><i class="fas fa-teeth"></i> <strong>Customized Approach</strong> - Treatment tailored to your tooth sensitivity and goals</li>
                        <li><i class="fas fa-check-circle"></i> <strong>Safe Procedure</strong> - Performed by dental professionals ensuring enamel protection</li>
                    </ul>
                    <p>During your consultation, we'll evaluate your oral health and recommend the best whitening approach for your specific needs.</p>
                </div>
            </div>
        </section>

        <!-- Process Section -->
        <section class="content-section">
            <h2 class="section-title">The Check-Up Process</h2>

            <div class="process-steps">
                <div class="step-card">
                    <h1 class="numbers">&#49;</h1>
                     <h3>Initial Consultation</h3>
                    <p>We examine your teeth and discuss your whitening goals to create a personalized treatment plan.</p>
                </div>

                <div class="step-card">
                    <h1 class="numbers">&#50;</h1>
                    <h3>Preparation</h3>
                    <p>We protect your gums and lips before applying the professional-grade whitening gel.</p>
                </div>

                <div class="step-card">
                    <h1 class="numbers">&#51;</h1>
                    <h3>Whitening Application</h3>
                    <p>The whitening gel is applied and activated with a special light for enhanced results.</p>
                </div>

                <div class="step-card">
                    <h1 class="numbers">&#52;</h1>
                    <h3>Aftercare Guidance</h3>
                    <p>We provide instructions on maintaining your brighter smile and minimizing sensitivity.</p>
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
    </script>
</body>
</html>



