<?php
    class CardView {
        private $card;
        private $card_row;
        private $name;
        private $content;

        public function __construct($card) {
            $this->card = $card;
        }

        public function printCard() {
            include_once 'cardContent.php';
        }
    }
?>