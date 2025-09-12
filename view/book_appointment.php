<?php
$serviceCategory = isset($_GET['service_category']) ? $_GET['service_category'] : '';
$services = [];
if ($serviceCategory) {
    $url = 'http://localhost/clinic_appointment_system/auth/getServicesByCategoryService.php?service_category=' . urlencode($serviceCategory);


    $response = file_get_contents($url);

    if ($response === false) {
        return 0;
    } else {
        $services = json_decode($response, true);
    }
}
?>

<!DOCTYPE html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Appointment</title>
    <link rel="stylesheet" href=".//homepageStyle.css">
    <link rel="stylesheet" href="fontawesome/css/fontawesome.min.css">
    <link rel="stylesheet" href="fontawesome/css/all.min.css">

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
                        <a href=".//RoutineCheckUp.html">Routine Check Up/Consultant</a>
                        <a href=".//Whitening.html">Whitening</a>
                        <a href=".//braces.html">Braces</a>
                        <a href=".//Dentures.html">Dentures</a>
                        <a href=".//Filling.html">Tooth Filling</a>
                        <a href=".//Cleaning.html">Scaling and Polishing</a>
                        <a href=".//CanalTreatment.html">Root Canal Treatment</a>
                        <a href=".//CrownsBridges.html">Crowns and Bridges</a>
                        <a href=".//Extraction.html">Tooth Extraction</a>
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

    <div class="container">
        <div class="progress-bar">
            <div class="progress-step completed">
                <span>&#49;</span>
                <div class="progress-label">Appointment Details</div>
            </div>
            <div class="progress-step">
                <span>&#50;</span>
                <div class="progress-label">Payment</div>
            </div>
            <div class="progress-step">
                <span>&#51;</span>
                <div class="progress-label">Confirmation</div>
            </div>
        </div>
    </div>

    <!-- Appointment Form -->
    <div class="container">
        <div class="apt-form">
            <div class="form-title">
                <h2>Book Your Appointment</h2>
                <p>Please select your preferred date and time</p>
            </div>

            <!-- User Information Section -->
            <div class="user-info">
                <h3>Your Information</h3>
                <div class="user-info-grid">
                    <div class="info-item">
                        <span class="info-label">Full Name</span>
                        <span class="info-value">John Smith</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Email Address</span>
                        <span class="info-value">john.smith@example.com</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Phone Number</span>
                        <span class="info-value">+60 12 345 6789</span>
                    </div>
                </div>
            </div>


            <form id="appointmentForm" action="process_appointment.php" method="POST">
                <input type="hidden" name="userId" value="12345">

                <div class="form-grid">
                    <div class="form-group">
                        <label for="service">Service</label>
                        <select id="service" name="service_id" required>
                            <option value="">-- Select Service --</option>
                            <?php foreach ($services as $serviceOption): ?>
                                <option value="<?php echo ($serviceOption['service_id']) ?>">
                                    <?php echo ($serviceOption['service_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group full-width">
                        <label>Select a Dentist</label>
                        <div class="doctors-grid">
                            <div class="doctor-card" data-id="1">
                                <div class="doctor-name">Dr. Ahmad Rizal</div>
                                <div class="doctor-specialty">Prosthodontist</div>
                                <input type="radio" name="doctor" value="1" style="display: none;">
                            </div>

                            <div class="doctor-card" data-id="2">
                                <div class="doctor-name">Dr. Sarah Lim</div>
                                <div class="doctor-specialty">Prosthodontist</div>
                                <input type="radio" name="doctor" value="2" style="display: none;">
                            </div>

                            <div class="doctor-card" data-id="3">
                                <div class="doctor-name">Dr. Wong Chen</div>
                                <div class="doctor-specialty">General Dentist</div>
                                <input type="radio" name="doctor" value="3" style="display: none;">
                            </div>
                        </div>
                    </div>

                    <div class="form-group full-width">
                        <label for="appointmentDate">Select Date</label>
                        <div class="calendar">
                            <div class="calendar-header">
                                <button type="button">&lt; Prev</button>
                                <span>June 2023</span>
                                <button type="button">Next &gt;</button>
                            </div>
                            <div class="calendar-day">Sun</div>
                            <div class="calendar-day">Mon</div>
                            <div class="calendar-day">Tue</div>
                            <div class="calendar-day">Wed</div>
                            <div class="calendar-day">Thu</div>
                            <div class="calendar-day">Fri</div>
                            <div class="calendar-day">Sat</div>

                            <!-- Calendar days will be populated with JavaScript -->
                            <div class="calendar-day disabled">28</div>
                            <div class="calendar-day disabled">29</div>
                            <div class="calendar-day disabled">30</div>
                            <div class="calendar-day disabled">31</div>
                            <div class="calendar-day">1</div>
                            <div class="calendar-day">2</div>
                            <div class="calendar-day">3</div>
                            <div class="calendar-day">4</div>
                            <div class="calendar-day">5</div>
                            <div class="calendar-day">6</div>
                            <div class="calendar-day">7</div>
                            <div class="calendar-day">8</div>
                            <div class="calendar-day">9</div>
                            <div class="calendar-day">10</div>
                            <div class="calendar-day">11</div>
                            <div class="calendar-day">12</div>
                            <div class="calendar-day">13</div>
                            <div class="calendar-day">14</div>
                            <div class="calendar-day">15</div>
                            <div class="calendar-day selected">16</div>
                            <div class="calendar-day">17</div>
                            <div class="calendar-day">18</div>
                            <div class="calendar-day">19</div>
                            <div class="calendar-day">20</div>
                            <div class="calendar-day">21</div>
                            <div class="calendar-day">22</div>
                            <div class="calendar-day">23</div>
                            <div class="calendar-day">24</div>
                            <div class="calendar-day">25</div>
                            <div class="calendar-day">26</div>
                            <div class="calendar-day">27</div>
                            <div class="calendar-day">28</div>
                            <div class="calendar-day">29</div>
                            <div class="calendar-day">30</div>
                            <div class="calendar-day disabled">1</div>
                        </div>
                        <input type="hidden" id="selectedDate" name="appointmentDate" value="2023-06-16">
                    </div>

                    <div class="form-group full-width">
                        <label for="appointmentTime">Select Time</label>
                        <div class="time-slots">
                            <div class="time-slot disabled">9:00 AM</div>
                            <div class="time-slot disabled">9:30 AM</div>
                            <div class="time-slot">10:00 AM</div>
                            <div class="time-slot">10:30 AM</div>
                            <div class="time-slot">11:00 AM</div>
                            <div class="time-slot">11:30 AM</div>
                            <div class="time-slot disabled">12:00 PM</div>
                            <div class="time-slot disabled">12:30 PM</div>
                            <div class="time-slot">2:00 PM</div>
                            <div class="time-slot">2:30 PM</div>
                            <div class="time-slot">3:00 PM</div>
                            <div class="time-slot selected">3:30 PM</div>
                            <div class="time-slot">4:00 PM</div>
                            <div class="time-slot">4:30 PM</div>
                            <div class="time-slot">5:00 PM</div>
                        </div>
                        <input type="hidden" id="selectedTime" name="appointmentTime" value="15:30">
                    </div>

                    <div class="form-group full-width">
                        <label for="notes">Additional Notes (Optional)</label>
                        <textarea id="notes" name="notes" class="form-control" rows="4" placeholder="Any special requests or information we should know?"></textarea>
                    </div>
                </div>

                <div class="btn-container">
                    <a href="#" class="btn outlineBtn">Back to Services</a>
                    <button type="submit" class="btn payment-btn">Proceed to Payment</button>
                </div>
            </form>

        </div>

        <!-- Summary Section -->
        <div class="summary">
            <h3 class="summary-title">Appointment Summary</h3>

            <div class="summary-item">
                <span class="summary-label">Service:</span>
                <span class="summary-value">Complete Dentures</span>
            </div>

            <div class="summary-item">
                <span class="summary-label">Dentist:</span>
                <span class="summary-value">Not selected</span>
            </div>

            <div class="summary-item">
                <span class="summary-label">Date & Time:</span>
                <span class="summary-value">Not selected</span>
            </div>

            <div class="summary-item">
                <span class="summary-label">Duration:</span>
                <span class="summary-value">Approx. 60 minutes</span>
            </div>

            <div class="summary-total">
                <span class="summary-label">Total:</span>
                <span class="summary-value">RM 350.00</span>
            </div>
        </div>
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

                // Update appointment and consultation links with username
                document.getElementById('appointment-link').href = `./book_appointment.php?service_category=Checkup&book_type=appointment&username=${encodeURIComponent(username)}`;
                document.getElementById('consultation-link').href = `./consultation.php?service_category=Checkup&book_type=consultation&username=${encodeURIComponent(username)}`;
            } else {
                document.getElementById('login-state').style.display = 'block';
                document.getElementById('logged-in-state').style.display = 'none';
                
                // Set links to redirect to login if not logged in
                document.getElementById('appointment-link').href = 'login.html';
                document.getElementById('consultation-link').href = 'login.html';
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


