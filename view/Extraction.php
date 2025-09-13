<?php
session_start();
$username_from_session = isset($_SESSION['username']) ? $_SESSION['username'] : '';
?>

<!DOCTYPE html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tooth Extraction</title>
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
    <section class="service-header extraction-header">
        <div class="serviceHeader-container">
            <h1>Tooth Extraction Services</h1>
            <p>Safe and comfortable tooth extraction procedures performed by our experienced dental surgeons. We prioritize your comfort and ensure minimal discomfort during the process.</p>
            <div class="btn-container">
                <a href=".//book_appointment.php?service_category=Surgical&book_type=appointment&username=<?php echo urlencode($username_from_session); ?>" 
                   class="bookBtn apptBtn"> Make Appointment</a>
                <a href=".//consultation.php?service_category=Surgical&book_type=consultation&username=<?php echo urlencode($username_from_session); ?>" 
                   class="bookBtn consultBtn">Book Consultation</a>
            </div>
        </div>
    </section>

    <div class="container">
        <!-- What's Included Section -->
        <section class="content-section">
            <h2 class="section-title">What is Tooth Extraction?</h2>

            <div class="twocolContainer">
                <div>
                    <p>Tooth extraction is the professional removal of a tooth from its socket in the bone. While preserving your natural teeth is always our priority, there are circumstances when extraction becomes necessary for your oral health.</p>
                    
                    <p>At SmileMaker Dental, we use advanced techniques to ensure the procedure is as comfortable and pain-free as possible. Our dentists will thoroughly explain the process and answer all your questions before proceeding.</p>
                </div>
                <div class="content-image">
                    <img src="image/extraction.jpg" alt="Dental Check-Up">
                </div>
            </div>
        </section>

       <!-- Why Important Section -->
        <section class="content-section">
            <h2 class="section-title">When Extraction is Necessary?</h2>

            <div class="twocolContainer">
                <div class="content-image">
                    <img src="image/extraction-reason.jpg" alt="Dental Examination">
                </div>
                <div>
                    <p>Our dentists may recommend tooth extraction in the following situations:</p>
                    <ul class="benefits-list">
                        <li><i class="fas fa-fire"></i><strong>Severe tooth decay</strong> that cannot be treated with a filling or root canal</li>
                        <li><i class="fas fa-band-aid"></i><strong>Advanced periodontal disease</strong> that has loosened the tooth</li>
                        <li><i class="fas fa-teeth"></i><strong>Impacted wisdom teeth</strong> that are causing pain or potential problems</li>
                        <li><i class="fas fa-layer-group"></i><strong>Crowded mouth</strong> when teeth need to be removed for orthodontic treatment</li>
                        <li><i class="fas fa-tooth"></i><strong>Broken or fractured teeth</strong> that cannot be repaired</li>
                        <li><i class="fas fa-bacteria"></i><strong>Risk of infection</strong> for patients with compromised immune systems</li>
                    </ul>
                     <p>If you're experiencing dental pain or have been advised that you might need an extraction, our team at SmileMaker Dental will provide a thorough examination and discuss all available options with you.</p>
                </div>
            </div>
        </section>

        <!-- Process Section -->
        <section class="content-section">
            <h2 class="section-title">Our Extraction Process</h2>

            <div class="process-steps">
                <div class="step-card">
                    <h1 class="numbers">&#49;</h1>
                    <h3>Examination</h3>
                    <p>We begin with a thorough examination and X-rays to assess the tooth and surrounding bone structure.</p>
                </div>

                <div class="step-card">
                    <h1 class="numbers">&#50;</h1>
                    <h3>Anesthesia</h3>
                     <p>Local anesthesia is administered to ensure a completely pain-free experience during the procedure.</p>
                </div>

                <div class="step-card">
                    <h1 class="numbers">&#51;</h1>
                    <h3>Extraction</h3>
                    <p>The dentist carefully loosens and removes the tooth using specialized instruments.</p>
                </div>

                <div class="step-card">
                    <h1 class="numbers">&#52;</h1>
                    <h3>Aftercare</h3>
                    <p>We provide detailed aftercare instructions to promote healing and prevent complications.</p>
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

