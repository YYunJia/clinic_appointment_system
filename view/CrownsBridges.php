<?php
session_start();
$username_from_session = isset($_SESSION['username']) ? $_SESSION['username'] : '';
?>

<!DOCTYPE html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crown & Bridge</title>
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
    <section class="service-header bridge-header">
        <div class="serviceHeader-container">
            <h1>Crown & Bridge Services</h1>
            <p>Restore your smile with our premium dental crown and bridge treatments. Custom-crafted to match your natural teeth for both function and aesthetics.</p>
            <div class="btn-container">
                <a href=".//book_appointment.php?service_category=Crown and Bridge&book_type=appointment&username=<?php echo urlencode($username_from_session); ?>" 
                   class="bookBtn apptBtn"> Make Appointment</a>
                <a href=".//consultation.php?service_category=Crown and Bridge&book_type=consultation&username=<?php echo urlencode($username_from_session); ?>" 
                   class="bookBtn consultBtn">Book Consultation</a>
            </div>
        </div>
    </section>

    <div class="container">
        <!-- What's Included Section -->
        <section class="content-section">
            <h2 class="section-title">What are Dental Crowns & Bridges?</h2>

            <div class="twocolContainer">
                <div>
                    <p>A dental crown is a tooth-shaped "cap" that is placed over a tooth to restore its shape, size, strength, and improve its appearance. Crowns are needed when a tooth is damaged, decayed, or discolored.</p>
                    <p>A dental bridge is used to replace one or more missing teeth. It consists of artificial teeth anchored to adjacent natural teeth or implants. Bridges restore your smile, maintain facial shape, and prevent remaining teeth from shifting.</p>
                    <p>At SmileMaker Dental, we use high-quality materials and advanced technology to create crowns and bridges that look, feel, and function like natural teeth.</p>
                </div>
                <div class="content-image">
                    <img src="image/bridge-benefit.jpg" alt="Dental Check-Up">
                </div>
            </div>
        </section>

        <!-- Why Important Section -->
        <section class="content-section">
            <h2 class="section-title">Why choose these restorative treatments</h2>

            <div class="twocolContainer">
                <div class="content-image">
                    <img src="image/bridge.jpg" alt="Dental Examination">
                </div>
                <div>
                    <ul class="benefits-list">
                        <li><i class="fas fa-hand-holding-medical"></i> <strong>Restore Dental Function</strong> - Allows for normal chewing, speaking, and biting force distribution.</li>
                        <li><i class="fas fa-grip-lines"></i>    <strong>Prevent Teeth Shifting</strong> – Bridges prevent adjacent teeth from shifting into the gap left by missing teeth.</li>
                        <li><i class="fas fa-grin"></i> <strong>Natural Appearance</strong> – Modern materials closely match the color and translucency of natural teeth.</li>
                        <li><i class="fas fa-first-aid"></i> <strong>Protect Weakened Teeth</strong> – Crowns protect and strengthen teeth that have been weakened by decay or trauma.</li>
                    </ul>
                </div>
            </div>
        </section>

        <section class="content-section">
            <h2 class="section-title">Crown vs Bridge: Which is Right for You?</h2>
            <p>Understanding the differences between these restorative option</p>

            <table class="option-table">
                <thead>
                    <tr>
                        <th>Feature</th>
                        <th>Dental Crown</th>
                        <th>Dental Bridge</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Purpose</td>
                        <td>Restores a damaged tooth</td>
                        <td>Replaces one or more missing teeth</td>
                    </tr>
                    <tr>
                        <td>Procedure Complexity</td>
                        <td>Moderate</td>
                        <td>Moderate to Complex</td>
                    </tr>
                    <tr>
                        <td>Treatment Time</td>
                        <td>2 visits over 1-2 weeks</td>
                        <td>2-3 visits over 2-3 weeks</td>
                    </tr>
                    <tr>
                        <td>Durability</td>
                        <td>5-15 years (depending on material)</td>
                        <td>5-15 years (depending on material)</td>
                    </tr>
                    <tr>
                        <td>Cost in Malaysia</td>
                        <td>RM800 - RM2,000 per crown</td>
                        <td>RM2,500 - RM6,000 per bridge</td>
                    </tr>
                </tbody>
            </table>
        </section>

        <!-- Process Section -->
        <section class="content-section">
            <h2 class="section-title">The Crown & Bridge Process</h2>

            <div class="bridge-process-steps">
                <div class="step-card">
                    <h1 class="numbers">&#49;</h1>
                    <h3>Consultation</h3>
                    <p>We examine your teeth, discuss options, and create a treatment plan.</p>
                </div>

                <div class="step-card">
                    <h1 class="numbers">&#50;</h1>
                    <h3>Tooth Preparation</h3>
                    <p>The tooth is reshaped to make room for the crown or bridge.</p>
                </div>

                <div class="step-card">
                    <h1 class="numbers">&#51;</h1>
                    <h3>Impressions</h3>
                    <p>Precise impressions are taken to create your custom restoration.</p>
                </div>

                <div class="step-card">
                    <h1 class="numbers">&#52;</h1>
                    <h3>Temporary Restoration</h3>
                    <p>A temporary crown or bridge is placed while yours is being crafted.</p>
                </div>

                <div class="step-card">
                    <h1 class="numbers">&#53;</h1>
                    <h3>Fitting & Adjustment</h3>
                    <p>Your permanent crown or bridge is checked for fit and comfort.</p>
                </div>

                <div class="step-card">
                    <h1 class="numbers">&#54;</h1>
                    <h3>Final Placement</h3>
                    <p>The restoration is permanently cemented into place.</p>
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



