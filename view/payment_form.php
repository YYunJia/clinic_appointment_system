<?php
require_once __DIR__ . '/../database.php';
ini_set('display_errors', 1);
error_reporting(E_ALL);

$appointmentID = $_GET['appointment_id'] ?? 'APT1001';
$conn = getDBConnection();

$stmt = $conn->prepare("
    SELECT 
        a.appointment_id,
        a.patient_id,
        a.service_id,
        p.emergency_contact_name,
        s.base_price
    FROM appointments a
    JOIN patients p ON a.patient_id = p.patient_id
    JOIN services s ON a.service_id = s.service_id
    WHERE a.appointment_id = :appointment_id
");
$stmt->bindParam(':appointment_id', $appointmentID);
$stmt->execute();
$appointment = $stmt->fetch(PDO::FETCH_ASSOC);

$patientName = $appointment['emergency_contact_name'] ?? "Unknown";
$amountDue = $appointment['base_price'] ?? 0;
$packageSessions = 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SmileMaker Dental - Payment</title>
  <link rel="stylesheet" href="./homepageStyle.css">
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f4faff;
      margin: 0;
      padding: 0;
    }
    .payment-container {
      max-width: 700px;
      margin: 60px auto;
      padding: 25px;
      background: #fff;
      border-radius: 12px;
      box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    .payment-header {
      text-align: center;
      margin-bottom: 25px;
    }
    .payment-header h2 {
      color: #0056b3;
    }
    .payment-details p {
      margin: 6px 0;
      font-size: 15px;
    }
    .highlight {
      font-weight: bold;
      color: #0056b3;
    }

    /* Payment option cards */
    .payment-options {
      display: flex;
      flex-wrap: wrap;
      gap: 15px;
      margin-top: 20px;
    }
    .payment-card {
      flex: 1 1 45%;
      border: 2px solid #ccc;
      border-radius: 10px;
      text-align: center;
      padding: 15px;
      cursor: pointer;
      transition: all 0.3s ease;
      background: #fafafa;
    }
    .payment-card img {
      height: 40px;
      margin-bottom: 8px;
    }
    .payment-card.active {
      border-color: #007bff;
      background: #e9f2ff;
      box-shadow: 0 0 10px rgba(0,123,255,0.3);
    }

    /* Hidden expandable form */
    .payment-form {
      display: none;
      margin-top: 20px;
      padding: 15px;
      border: 1px solid #ddd;
      border-radius: 8px;
      background: #f9f9f9;
    }
    .payment-form label {
      display: block;
      margin: 8px 0 5px;
      font-weight: bold;
    }
    .payment-form input[type="text"],
    .payment-form input[type="password"] {
      width: 100%;
      padding: 10px;
      margin-bottom: 12px;
      border: 1px solid #ccc;
      border-radius: 6px;
    }

    button {
      width: 100%;
      padding: 14px;
      background: #007bff;
      color: #fff;
      font-size: 16px;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      transition: 0.3s;
      margin-top: 20px;
    }
    button:hover {
      background: #0056b3;
    }

    /* Checkbox styling */
    .checkbox-label {
      display: inline-flex;
      align-items: center;
      justify-content: flex-start;
      gap: 8px;
      font-weight: normal;
      cursor: pointer;
      margin-top: 10px;
      width: 100%;
    }
    .checkbox-label input[type="checkbox"] {
      transform: scale(1.1);
      accent-color: #007bff;
      margin: 0;
    }
    .checkbox-instruction {
      font-size: 14px;
      color: #555;
      margin-bottom: 8px;
    }

  </style>
</head>
<body>
  <div class="payment-container">
    <div class="payment-header">
      <h2>Secure Payment</h2>
      <p>Confirm your details and choose a payment method</p>
    </div>

    <div class="payment-details">
      <p>Appointment ID: <span class="highlight"><?php echo htmlspecialchars($appointmentID); ?></span></p>
      <p>Patient: <span class="highlight"><?php echo htmlspecialchars($patientName); ?></span></p>
      <p>Amount Due: <span class="highlight">RM <?php echo number_format($amountDue, 2); ?></span></p>
    </div>

    <div class="payment-options">
      <div class="payment-card" onclick="selectMethod('visa')">
        <img src="https://upload.wikimedia.org/wikipedia/commons/0/04/Visa.svg" alt="Visa"><p>Visa</p>
      </div>
      <div class="payment-card" onclick="selectMethod('mastercard')">
        <img src="https://upload.wikimedia.org/wikipedia/commons/2/2a/Mastercard-logo.svg" alt="MasterCard"><p>MasterCard</p>
      </div>
      <div class="payment-card" onclick="selectMethod('ewallet')">
        <img src="image/tng-ewallet.png" alt="Touch 'n Go eWallet" style="height:50px;"><p>E-Wallet</p>
      </div>
      <div class="payment-card" onclick="selectMethod('package')">
        <img src="https://cdn-icons-png.flaticon.com/512/833/833472.png" alt="Package" style="height:50px;"><p>Service Package</p>
      </div>
    </div>

    <!-- Forms -->
    <form id="visa-form" class="payment-form">
      <input type="hidden" name="payment_method" value="visa">
      <input type="hidden" name="appointment_id" value="<?php echo htmlspecialchars($appointmentID); ?>">
      <label>Card Number</label><input type="text" name="card_number" placeholder="XXXX-XXXX-XXXX-XXXX" required>
      <label>Expiry Date</label><input type="text" name="expiry_date" placeholder="MM/YY" required>
      <label>CVV</label><input type="password" name="cvv" placeholder="***" required>
    </form>

    <form id="mastercard-form" class="payment-form">
      <input type="hidden" name="payment_method" value="mastercard">
      <input type="hidden" name="appointment_id" value="<?php echo htmlspecialchars($appointmentID); ?>">
      <label>Card Number</label><input type="text" name="card_number" placeholder="XXXX-XXXX-XXXX-XXXX" required>
      <label>Expiry Date</label><input type="text" name="expiry_date" placeholder="MM/YY" required>
      <label>CVV</label><input type="password" name="cvv" placeholder="***" required>
    </form>

    <form id="ewallet-form" class="payment-form">
      <input type="hidden" name="payment_method" value="ewallet">
      <input type="hidden" name="appointment_id" value="<?php echo htmlspecialchars($appointmentID); ?>">
      <label>Touch 'n Go eWallet ID / Phone</label><input type="text" name="ewallet_id" placeholder="Enter your eWallet number" required>
    </form>

    <form id="package-form" class="payment-form">
      <input type="hidden" name="payment_method" value="package">
      <input type="hidden" name="appointment_id" value="<?php echo htmlspecialchars($appointmentID); ?>">
      <p>You have <span class="highlight"><?php echo htmlspecialchars($packageSessions); ?> sessions remaining</span> in your package.</p>
      <label class="checkbox-label">
        <input type="checkbox" id="confirm-package" name="confirm_package" required> I confirm to use 1 session
      </label>
    </form>

    <button type="submit" id="payment-btn">Proceed to Payment</button>
  </div>

<script>
    function selectMethod(method) {
      document.querySelectorAll('.payment-card').forEach(c => c.classList.remove('active'));
      document.querySelectorAll('.payment-form').forEach(f => f.style.display = 'none');

      const selectedCard = document.querySelector(`.payment-card[onclick="selectMethod('${method}')"]`);
      if (selectedCard) selectedCard.classList.add('active');

      const form = document.getElementById(`${method}-form`);
      if (form) form.style.display = 'block';
    }

    document.getElementById('payment-btn').addEventListener('click', function(e){
      e.preventDefault();

      let visibleForm = null;
      document.querySelectorAll('.payment-form').forEach(f => { if(f.offsetParent!==null) visibleForm=f; });

      if(!visibleForm){ alert('Please select a payment method.'); return; }

      if(visibleForm.id==='package-form'){
        const checkbox=document.getElementById('confirm-package');
        if(!checkbox.checked){ alert('Please confirm that you want to use 1 session from your package.'); return; }
      }

      const formData=new FormData(visibleForm);

      fetch('../controller/PaymentController.php?action=process', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())  // will now parse JSON correctly
    .then(data => {
        if(data.success){
            alert('Payment Successful! Payment ID: ' + data.payment_id);
            window.location.href = 'payment_success.php?payment_id=' + data.payment_id;
        } else {
            alert('Payment Failed: ' + data.message);
        }
    })
    .catch(err => { alert('Payment error: ' + err); });

    });
</script>
</body>
</html>