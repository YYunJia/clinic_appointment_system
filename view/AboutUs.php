<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>About Us - SmileMaker Dental</title>
        <base href="/clinic_appointment_system/view/">
        <link rel="stylesheet" href="homepageStyle.css">
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
                        <img src="image/logowithoutname.png" alt="Logo" style="width:40px;" class="rounded-pill">
                    </a>
                </div>
                <div class="name">SMILEMAKER</div>
            </div>

            <div class="navigation-bar">
                <ul>
                    <li><a href=".//HomePage_patient.html">Home</a></li>
                    <li><a href=".//AboutUs.php">About Us</a></li>
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
                    <button class="btn btn-outline" onclick="window.location.href = 'login.html'">Login</button>
                    <button class="btn btn-primary" onclick="window.location.href = 'register_patient.html'">Register</button>
                </div>

                <!-- Logged In State -->
                <div id="logged-in-state" class="auth-buttons" style="display: none;">
                    <div class="user-menu">
                        <button class="btn btn-outline" id="username-btn" onclick="window.location.href = 'profile.html'">
                            <i class="fas fa-user"></i> <span id="username-text">Username</span>
                        </button>
                        <button class="btn btn-danger" onclick="logout()">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <section class="service-header main-header">
            <div class="serviceHeader-container">
                <h1>About SmileMaker Dental</h1>
                <p>Your trusted partner in dental care for over 15 years. We are committed to providing exceptional dental services with compassion and expertise.</p>

            </div>
        </section>

        <section class="content-section">
            <h2 class="section-title">Our Story</h2>
            <p>Experience the difference in dental care</p>

            <div class="twocolContainer">
                <div>
                    <p>Established in 2008, SmileMaker Dental has been serving the Malaysian community with comprehensive dental care for over 15 years. Our journey began with a simple mission: to make quality dental care accessible and comfortable for everyone.</p>
                    <p>Founded by Dr. Lim Wei Jian, our clinic started as a small practice with just two dental chairs. Today, we have grown into a modern dental facility with state-of-the-art equipment and a team of highly skilled dental professionals.</p>
                    <p>Over the years, we've helped thousands of patients achieve healthier, more beautiful smiles while maintaining our commitment to personalized care and clinical excellence.</p>
                </div>
                <div class="content-image">
                    <img src="image/professional.jpg" alt="Professional Dentist Team">
                </div>
            </div>
        </section>

        <section class="content-section">
                    <h2 class="section-title">Our Mission & Values</h2>
                    <p>Guiding principles that define who we are and how we care for our patients</p>
                    
                <div class="mission-container">
                    <div class="mission-grid">
                        <div class="mission-item">
                            <div class="mission-icon">
                                <i class="fas fa-heart"></i>
                            </div>
                            <h4>Patient-Centered Care</h4>
                            <p>We prioritize your comfort and needs, ensuring you receive personalized treatment in a warm, welcoming environment.</p>
                        </div>

                        <div class="mission-item">
                            <div class="mission-icon">
                                <i class="fas fa-award"></i>
                            </div>
                            <h4>Clinical Excellence</h4>
                            <p>We maintain the highest standards of dental practice through continuous education and adoption of advanced technologies.</p>
                        </div>

                        <div class="mission-item">
                            <div class="mission-icon">
                                <i class="fas fa-hand-holding-heart"></i>
                            </div>
                            <h4>Compassionate Service</h4>
                            <p>We understand dental visits can be stressful, so we provide gentle care with empathy and understanding.</p>
                        </div>

                        <div class="mission-item">
                            <div class="mission-icon">
                                <i class="fas fa-shield-alt"></i>
                            </div>
                            <h4>Integrity & Trust</h4>
                            <p>We build lasting relationships with our patients through honest communication and ethical practices.</p>
                        </div>
                    </div>
                </div>
           
        </section>




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