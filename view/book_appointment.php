<?php
session_start();
$username_from_session = isset($_SESSION['username']) ? $_SESSION['username'] : '';
$username = isset($_GET['username']) ? $_GET['username'] : '';
$serviceCategory = isset($_GET['service_category']) ? $_GET['service_category'] : '';
$selected_service_id = $_POST['service_id'] ?? '';
$selected_dentist = $_POST['doctor_id'] ?? '';
$selected_date = $_POST['appointmentDate'] ?? '';
$selected_time = $_POST['appointmentTime'] ?? '';

$user = [];
$services = [];
$dentists = [];
$available_slots = [];

// Get user info
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

// Get services by category
if ($serviceCategory) {
    $url = 'http://localhost/clinic_appointment_system/auth/getServicebyCateService.php?service_category=' . urlencode($serviceCategory);
    $response = file_get_contents($url);
    if ($response !== false) {
        $services = json_decode($response, true);
    }
}

// Get all dentists
$url = 'http://localhost/clinic_appointment_system/auth/getDentistService.php';
$response = file_get_contents($url);
if ($response !== false) {
    $dentists = json_decode($response, true);
}

// Get available slots for selected dentist & date
if ($selected_dentist && $selected_date) {
    $url = "http://localhost/clinic_appointment_system/auth/getAvailableSlotService.php?doctor_id=" . urlencode($selected_dentist) . "&date=" . urlencode($selected_date);
    $response = file_get_contents($url);
    if ($response !== false) {
        $available_slots = json_decode($response, true);
    }
}

// Get the selected service details for summary (including price)
$selected_service_detail = null;
if ($selected_service_id) {
    $url = "http://localhost/clinic_appointment_system/auth/getServiceByIdService.php?service_id=" . urlencode($selected_service_id);
    $response = file_get_contents($url);
    if ($response !== false) {
        $service_result = json_decode($response, true);
        // getServiceById usually returns ['success'=>true,'data'=>{...}]
        if ($service_result && isset($service_result['data'])) {
            $selected_service_detail = $service_result['data'];
        } elseif (isset($service_result[0])) { // sometimes just returns array of one
            $selected_service_detail = $service_result[0];
        }
    }
}

// Get the selected dentist details for summary
$selected_dentist_detail = null;
if ($selected_dentist && !empty($dentists)) {
    foreach ($dentists as $dentistOption) {
        if ($dentistOption['doctor_id'] == $selected_dentist) {
            $selected_dentist_detail = $dentistOption;
            break;
        }
    }
}

// Format selected time for summary
$selected_slot = null;
if ($selected_time && !empty($available_slots)) {
    foreach ($available_slots as $slot) {
        if ($slot['start_time'] == $selected_time) {
            $selected_slot = $slot;
            break;
        }
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


            <form id="appointmentForm" action="book_appointment.php?service_category=<?php echo urlencode($serviceCategory); ?>&username=<?php echo urlencode($username); ?>" method="POST">
                <input type="hidden" name="userId" value="<?php echo htmlspecialchars($user['user_id'] ?? '') ?>">

                <div class="form-grid">
                    <div class="form-group">
                        <label for="service">Service</label>
                        <select class="serviceType" id="service" name="service_id" required>
                            <option value="">-- Select Service --</option>
                            <?php foreach ($services as $serviceOption): ?>
                                <option value="<?php echo htmlspecialchars($serviceOption['service_id']); ?>"
                                        <?php if ($selected_service_id == $serviceOption['service_id']) echo 'selected'; ?>>
                                            <?php echo htmlspecialchars($serviceOption['service_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="doctor_id">Select a Dentist</label>
                        <select class="serviceType" id="doctor_id" name="doctor_id" required onchange="this.form.submit()">
                            <option value="">-- Select Dentist --</option>
                            <?php foreach ($dentists as $dentistOption): ?>
                                <option value="<?php echo htmlspecialchars($dentistOption['doctor_id']) ?>"
                                        <?php if ($selected_dentist == $dentistOption['doctor_id']) echo "selected"; ?>>
                                            <?php echo htmlspecialchars($dentistOption['full_name'] . " (" . $dentistOption['specialization'] . ")"); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <?php if ($selected_dentist): ?>
                        <div class="form-group">
                            <label for="appointmentDate">Select Date</label>
                            <input class="serviceType" type="date" id="appointmentDate" name="appointmentDate"
                                   value="<?php echo htmlspecialchars($selected_date); ?>" required onchange="this.form.submit()">
                            <input type="hidden" name="doctor_id" value="<?php echo htmlspecialchars($selected_dentist); ?>">
                        </div>
                    <?php endif; ?>

                    <?php if ($selected_dentist && $selected_date): ?>
                        <div class="form-group">
                            <label for="appointmentTime">Select Time</label>
                            <?php if (!empty($available_slots) && !isset($available_slots['error'])): ?>
                                <select class="serviceType" name="appointmentTime" id="appointmentTime" required>
                                    <option value="">-- Select Time Slot --</option>
                                    <?php foreach ($available_slots as $slot): ?>
                                        <option value="<?php echo htmlspecialchars($slot['start_time']); ?>"
                                                <?php if ($selected_time == $slot['start_time']) echo "selected"; ?>>
                                                    <?php
                                                    echo htmlspecialchars(date('h:i A', strtotime($slot['start_time']))) . " - " .
                                                    htmlspecialchars(date('h:i A', strtotime($slot['end_time'])));
                                                    ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            <?php elseif (isset($available_slots['error'])): ?>
                                <p><?php echo htmlspecialchars($available_slots['error']); ?></p>
                            <?php else: ?>
                                <p>No available slots for this date.</p>
                            <?php endif; ?>
                            <input type="hidden" name="doctor_id" value="<?php echo htmlspecialchars($selected_dentist); ?>">
                            <input type="hidden" name="appointmentDate" value="<?php echo htmlspecialchars($selected_date); ?>">
                        </div>
                    <?php endif; ?>

                    <div class="form-group full-width">
                        <label for="notes">Additional Notes (Optional)</label>
                        <textarea id="notes" name="notes" class="form-control" rows="4" placeholder="Any special requests or information we should know?"></textarea>
                    </div>
                </div>

                <div class="btn-container">
                    <a href="#" class="btn outlineBtn">Back to Services</a>
                    <?php if ($selected_dentist && $selected_date): ?>
                        <button type="submit" class="btn payment-btn">Proceed to Payment</button>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <!-- Summary Section -->
        <div class="summary">
            <h3 class="summary-title">Appointment Summary</h3>

            <div class="summary-item">
                <span class="summary-label">Service:</span>
                <span class="summary-value">
                    <?php
                    if ($selected_service_detail) {
                        echo htmlspecialchars($selected_service_detail['service_name']);
                    } elseif ($selected_service_id) {
                        // fallback: show selected id
                        echo "Service #" . htmlspecialchars($selected_service_id);
                    } else {
                        echo "Not selected";
                    }
                    ?>
                </span>
            </div>

            <div class="summary-item">
                <span class="summary-label">Dentist:</span>
                <span class="summary-value">
                    <?php
                    if ($selected_dentist_detail) {
                        echo htmlspecialchars($selected_dentist_detail['full_name']);
                        if (!empty($selected_dentist_detail['specialization'])) {
                            echo " (" . htmlspecialchars($selected_dentist_detail['specialization']) . ")";
                        }
                    } elseif ($selected_dentist) {
                        echo "Dentist #" . htmlspecialchars($selected_dentist);
                    } else {
                        echo "Not selected";
                    }
                    ?>
                </span>
            </div>

            <div class="summary-item">
                <span class="summary-label">Date & Time:</span>
                <span class="summary-value">
                    <?php
                    if ($selected_date && $selected_time) {
                        echo htmlspecialchars(date("d M Y", strtotime($selected_date))) . " ";
                        if ($selected_slot) {
                            echo htmlspecialchars(date('h:i A', strtotime($selected_slot['start_time']))) . " - " .
                            htmlspecialchars(date('h:i A', strtotime($selected_slot['end_time'])));
                        } else {
                            echo htmlspecialchars(date('h:i A', strtotime($selected_time)));
                        }
                    } elseif ($selected_date) {
                        echo htmlspecialchars(date("d M Y", strtotime($selected_date)));
                    } else {
                        echo "Not selected";
                    }
                    ?>
                </span>
            </div>

            <?php
            $total = "";
            if ($selected_service_detail && isset($selected_service_detail['base_price'])) {
                $total = number_format($selected_service_detail['base_price'], 2);
            }
            ?>

            <div class="summary-total">
                <span class="summary-label">Total:</span>
                <span class="summary-value">
                    <?php echo $total ? "RM {$total}" : "Not calculated"; ?>
                </span>
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


