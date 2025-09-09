<?php
require_once __DIR__ . '/../database.php';

class Payment {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Save payment record
    public function save($appointment_id, $patient_id, $amount, $method, $status = 'paid', $transaction_ref = null) {
        $paymentID = 'PAY' . uniqid(); // safer unique ID
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO payments 
                (payment_id, appointment_id, patient_id, amount, payment_method, payment_status, transaction_date, invoice_id)
                VALUES 
                (:payment_id, :appointment_id, :patient_id, :amount, :payment_method, :payment_status, NOW(), :invoice_id)
            ");
            $stmt->execute([
                ':payment_id' => $paymentID,
                ':appointment_id' => $appointment_id,
                ':patient_id' => $patient_id,
                ':amount' => $amount,
                ':payment_method' => $method,
                ':payment_status' => $status,
                ':invoice_id' => $transaction_ref
            ]);
        } catch (PDOException $e) {
            throw new Exception("Database error: " . $e->getMessage());
        }
        return $paymentID;
    }
}
?>
