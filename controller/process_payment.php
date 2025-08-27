<?php

/* 
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHP.php to edit this template
 */

require_once '../model/CreditCardPayment.php';
require_once '../model/PackagePayment.php';
require_once '../model/PaymentContext.php';

$appointmentId = $_POST['appointment_id'];
$amount = $_POST['amount'];
$paymentMethod = $_POST['payment_method']; // 'credit' or 'package'

if ($paymentMethod == 'credit') {
    $paymentStrategy = new CreditCardPayment();
} elseif ($paymentMethod == 'package') {
    $paymentStrategy = new PackagePayment();
} else {
    die("Invalid payment method.");
}

$context = new PaymentContext($paymentStrategy);
$context->executePayment($appointmentId, $amount);
?>
<br><a href="../view/payment_form.php">Back to Payment</a>