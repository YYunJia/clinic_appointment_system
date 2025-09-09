<?php
require_once "IPaymentStrategy.php";

class CreditCardStrategy implements IPaymentStrategy {
    public function processPayment($data) {
        // simulate payment gateway
        if(empty($data['card_number']) || empty($data['cvv'])){
            return ['success'=>false, 'message'=>'Card details incomplete'];
        }
        $token = hash('sha256', $data['card_number'].time()); // simple token
        return ['success'=>true, 'message'=>'Credit Card Payment Successful', 'transaction_ref'=>$token];
    }
}

?>
