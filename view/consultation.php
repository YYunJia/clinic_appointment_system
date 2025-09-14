<?php
session_start();
$username_from_session = isset($_SESSION['username']) ? $_SESSION['username'] : '';

$username = isset($_GET['username']) ? $_GET['username'] : '';
$serviceCategory = isset($_GET['service_category']) ? $_GET['service_category'] : '';
$user = [];
$services = [];

if ($username) {
    $url = 'http://localhost/clinic_appointment_system/auth/get_user.php?username=' . urlencode($username);
    $response = file_get_contents($url);

    if ($response !== false) {
        $result = json_decode($response, true);
        if ($result && $result['success']) {
            $user = $result['user'];
        }
    }
}

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
    <title>Book Consultation</title>
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

        <div class="action-button">
            <button class="btn outlineBtn">Login</button>
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
                <h2>Book Your Consultation</h2>
                <p>Please select your preferred date and time</p>
            </div>

            <!-- User Information Section -->
            <div class="user-info">
                <h3>Your Information</h3>
                <div class="user-info-grid">
                    <div class="info-item">
                        <span class="info-label">Full Name</span>
                        <span class="info-value"><?php echo htmlspecialchars($user['name'] ?? '') ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Email Address</span>
                        <span class="info-value"><?php echo htmlspecialchars($user['email'] ?? '') ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Phone Number</span>
                        <span class="info-value"><?php echo htmlspecialchars($user['phone_number'] ?? '') ?></span>
                    </div>
                </div>
            </div>


            <form id="consultationForm" action="process_appointment.php" method="POST">
                <input type="hidden" name="userId" value="12345">

                <div class="form-grid">
                     <div class="form-group">
                        <label for="service">Service</label>
                        <select class="serviceType" id="service" name="service_id" required>
                            <option value="">-- Select Service --</option>
                            <?php foreach ($services as $serviceOption): ?>
                                <option value="<?php echo ($serviceOption['service_id']) ?>">
                                    <?php echo ($serviceOption['service_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
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
                    
                </div>

                <div class="btn-container">
                    <a href="#" class="btn outlineBtn">Back to Services</a>
                    <button type="submit" class="btn payment-btn">Confirm Booking</button>
                </div>
            </form>

        </div>

        <!-- Summary Section -->
        <div class="summary">
            <h3 class="summary-title">Consultation Summary</h3>

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
        // Simple JavaScript for interactive elements
        document.addEventListener('DOMContentLoaded', function () {
            // Doctor selection
            const doctorCards = document.querySelectorAll('.doctor-card');
            doctorCards.forEach(card => {
                card.addEventListener('click', function () {
                    doctorCards.forEach(c => c.classList.remove('selected'));
                    this.classList.add('selected');

                    // Update the hidden radio input
                    const radioInput = this.querySelector('input[type="radio"]');
                    radioInput.checked = true;

                    // Update summary
                    document.querySelector('.summary-item:nth-child(2) .summary-value').textContent = this.querySelector('.doctor-name').textContent;
                });
            });

            // Date selection
            const calendarDays = document.querySelectorAll('.calendar-day:not(.disabled):not(:nth-child(-n+7))');
            calendarDays.forEach(day => {
                day.addEventListener('click', function () {
                    if (this.classList.contains('disabled'))
                        return;

                    calendarDays.forEach(d => d.classList.remove('selected'));
                    this.classList.add('selected');

                    // Update the hidden date input (simplified for demo)
                    const selectedDate = this.textContent;
                    const monthYear = document.querySelector('.calendar-header span').textContent;
                    document.getElementById('selectedDate').value = `2023-06-${selectedDate.padStart(2, '0')}`;

                    // Update summary
                    document.querySelector('.summary-item:nth-child(3) .summary-value').textContent = `${selectedDate} ${monthYear}`;
                });
            });

            // Time selection
            const timeSlots = document.querySelectorAll('.time-slot:not(.disabled)');
            timeSlots.forEach(slot => {
                slot.addEventListener('click', function () {
                    timeSlots.forEach(s => s.classList.remove('selected'));
                    this.classList.add('selected');

                    // Update the hidden time input (simplified for demo)
                    const timeText = this.textContent;
                    let timeValue = '';

                    if (timeText.includes('AM')) {
                        timeValue = timeText.replace(' AM', ':00');
                    } else {
                        const hour = parseInt(timeText) + 12;
                        timeValue = `${hour}${timeText.replace(/[0-9]+:/, ':')}`.replace(' PM', ':00');
                    }

                    document.getElementById('selectedTime').value = timeValue;

                    // Update summary with both date and time
                    const selectedDate = document.querySelector('.calendar-day.selected');
                    if (selectedDate) {
                        const dateText = selectedDate.textContent;
                        const monthYear = document.querySelector('.calendar-header span').textContent;
                        document.querySelector('.summary-item:nth-child(3) .summary-value').textContent = `${dateText} ${monthYear}, ${timeText}`;
                    }
                });
            });

            // Form submission
            document.getElementById('appointmentForm').addEventListener('submit', function (e) {
                e.preventDefault();

                // Basic validation
                const doctorSelected = document.querySelector('input[name="doctor"]:checked');
                if (!doctorSelected) {
                    alert('Please select a dentist');
                    return;
                }

                // If validation passes, submit the form
                this.submit();
            });
        });
        
        
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


