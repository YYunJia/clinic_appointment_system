<?php
require_once "IPaymentStrategy.php";

class EWalletStrategy implements IPaymentStrategy {
    public function processPayment($data) {
        // Simulate eWallet verification
        if(!empty($data['ewallet_id'])) {
            return ['success' => true, 'message' => 'E-Wallet Payment Successful', 'transaction_ref' => $data['ewallet_id']];
        }
        return ['success' => false, 'message' => 'Invalid E-Wallet ID'];
    }
}
?>
