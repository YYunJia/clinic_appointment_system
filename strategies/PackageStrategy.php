<?php
require_once __DIR__ . '/../models/Package.php';
require_once __DIR__ . '/../models/Payment.php';

class PackageStrategy implements IPaymentStrategy {
    public function processPayment($data) {
        $patient_id = $data['patient_id'];
        $package_id = $data['package_id'];
        $appointment_id = $data['appointment_id'];

        $package = new Package();
        $paymentModel = new Payment();

        $success = $package->deductSession($patient_id, $package_id);

        if ($success) {
            $paymentID = $paymentModel->save($appointment_id, $patient_id, 0, 'package', 'paid', null, 1);

            return [
                'success' => true,
                'message' => 'Package applied. Payment recorded successfully.',
                'payment_id' => $paymentID
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Insufficient sessions. Cannot apply package.'
            ];
        }
    }
}
