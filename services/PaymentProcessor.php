<?php
class PaymentProcessor {
    private $strategy;

    public function __construct($strategy) {
        $this->strategy = $strategy;
    }

    public function setStrategy($strategy) {
        $this->strategy = $strategy;
    }

    public function executePayment($data) {
        return $this->strategy->processPayment($data);
    }
}
?>
