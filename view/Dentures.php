<?php
session_start();
$username_from_session = isset($_SESSION['username']) ? $_SESSION['username'] : '';
?>

<!DOCTYPE html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dentures</title>
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
    <section class="service-header dentures-header">
        <div class="serviceHeader-container">
            <h1>Premium Denture Care for a Natural You</h1>
            <p>Restore your smile and confidence with our custom-crafted dentures designed for comfort and natural appearance.</p>
            <div class="btn-container">
                <a href=".//book_appointment.php?service_category=Denture&book_type=appointment&username=<?php echo urlencode($username_from_session); ?>" 
                   class="bookBtn apptBtn"> Make Appointment</a>
                <a href=".//consultation.php?service_category=Denture&book_type=consultation&username=<?php echo urlencode($username_from_session); ?>" 
                   class="bookBtn consultBtn">Book Consultation</a>
            </div>
        </div>
    </section>

    <div class="container">
        <!-- What's Included Section -->
        <section class="content-section">
            <h2 class="section-title">Understanding Dentures</h2>

            <div class="twocolContainer">
                <div>
                 <p>Custom-crafted, comfortable dentures designed to restore your smile, your comfort, and your confidence.</p>   
                    <p>Tooth loss affects more than just your smile—it can change the shape of your face, making you look older. Our premium dentures do more than replace teeth; they support your facial muscles to restore a more youthful and natural appearance.</p>

                <p>We believe your dentures should feel as good as they look. Using the latest techniques and high-quality materials, we create custom-fitted dentures for unparalleled comfort and a seamless, natural look.</p>

                <h3>Why choose our denture services?</h3>
                <ul class="benefits-list">
                    <li><strong>Natural Beauty:</strong> Designed to mimic the look of natural teeth and gums.</li>
                    <li><strong>Personalized Fit:</strong> Custom-crafted for comfort and secure fit.</li>
                    <li><strong>Restored Function:</strong> Enjoy speaking clearly and eating your favorite foods with confidence.</li>
                    <li><strong>Expert Care:</strong> Your journey is guided by experienced dental professionals.</li>
                </ul>
                </div>
                <div class="content-image">
                    <img src="image/dentures.jpg" alt="Dental Check-Up">
                </div>
            </div>
        </section>
        
        <section class="content-section">
            <h2 class="section-title">Our Denture Services</h2>

            <div class="optionsContainer">
                <!-- Metal Braces -->
                <div class="optionsCard">
                    <div class="options-img">
                        <img src="image/complete-dentures.jpg" alt="Complete Dentures">
                    </div>
                    <div class="options-content">
                        <h3>Complete Dentures</h3>
                        <p>Complete dentures are used when all the teeth are missing. They can be either "conventional" or "immediate."</p><br/>
                        <p>Conventional dentures are made after the teeth have been removed and the gum tissue has begun to heal. Immediate dentures are made in advance and can be positioned as soon as the teeth are removed.</p>
                    </div>
                </div>
                
                <!-- Ceramic Braces -->
                <div class="optionsCard">
                    <div class="options-img">
                        <img src="image/partial-dentures.jpg" alt="Partial Dentures">
                    </div>
                    <div class="options-content">
                        <h3>Partial Dentures</h3>
                        <p>A partial denture is used when one or more natural teeth remain in the upper or lower jaw.</p><br/>
                        <p>Partial dentures not only fill in the spaces created by missing teeth, but they also prevent other teeth from changing position. They are custom-made to match your natural teeth.</p>
                    </div>
                </div>
                
                <!-- Lingual Braces -->
                <div class="optionsCard">
                    <div class="options-img">
                        <img src="image/implant-dentures.jpg" alt="Implant Dentures">
                    </div>
                    <div class="options-content">
                        <h3>Implant-Supported Dentures</h3>
                        <p>Implant-supported dentures are a modern solution that combines dental implants with dentures for superior stability and function.</p><br/>
                        <p>These dentures are anchored by dental implants, providing a secure fit that allows you to eat and speak with confidence. They also help preserve jaw bone health.</p>
                    </div>
                </div>
                
                <div class="optionsCard">
                    <div class="options-img">
                        <img src="image/Broken-Denture.jpg" alt="Dentures Repair">
                    </div>
                    <div class="options-content">
                        <h3>Denture Relines & Repairs</h3>
                        <p>Over time, dentures may need adjustments, relines, or repairs due to normal wear or changes in your mouth.</p><br/>
                        <p>We offer professional denture maintenance services to ensure your dentures continue to fit properly and function optimally. We can often repair broken dentures quickly.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Why Important Section -->
        <section class="content-section">
            <h2 class="section-title">Benefits of Choosing Denture</h2>

            <div class="twocolContainer">
                <div class="content-image">
                    <img src="image/dentures-benefit.jpg" alt="Dental Examination">
                </div>
                <div>
                    <p>Professional teeth whitening offers significant advantages over store-bought products:</p>
                    <ul class="benefits-list">
                        <li><i class="fas fa-bolt"></i> <strong>Restored Smile</strong> - A complete, natural-looking smile.</li>
                        <li><i class="fas fa-star"></i> <strong>Improved Eating</strong> - Enjoy a wider variety of foods comfortably.</li>
                        <li><i class="fas fa-teeth"></i> <strong>Clearer Speech</strong> - Speak more clearly without impediments.</li>
                        <li><i class="fas fa-check-circle"></i> <strong>Facial Support</strong> - Prevent a sunken appearance from tooth loss.</li>
                    </ul>
                    <p>During your consultation, we'll evaluate your oral health and recommend the best whitening approach for your specific needs.</p>
                </div>
            </div>
        </section>

        <!-- Process Section -->
        <section class="content-section">
            <h2 class="section-title">The Denture Process</h2>

            <div class="process-steps">
                <div class="step-card">
                    <h1 class="numbers">&#49;</h1>
                     <h3>Initial Consultation</h3>
                    <p>We examine your oral health, discuss options, and create a treatment plan.</p>
                </div>

                <div class="step-card">
                    <h1 class="numbers">&#50;</h1>
                    <h3>Impressions & Measurements</h3>
                    <p>We take precise impressions and measurements of your mouth.</p>
                </div>

                <div class="step-card">
                    <h1 class="numbers">&#51;</h1>
                    <h3>Try-In</h3>
                    <p>You try on a model to ensure proper color, shape, and fit before final production.</p>
                </div>

                <div class="step-card">
                    <h1 class="numbers">&#52;</h1>
                    <h3>Final Fitting</h3>
                    <p>We place the final dentures and make any necessary adjustments for optimal comfort.</p>
                </div>
                
                <div class="step-card">
                    <h1 class="numbers">&#53;</h1>
                    <h3>Follow-Up Care</h3>
                    <p>We schedule follow-up appointments to ensure your dentures continue to fit properly.</p>
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

