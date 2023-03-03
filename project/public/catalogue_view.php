<?php
    class CatalogueView {
        private $catalogue_row;
        private $btns;
        private $per_page;

        public function __construct($catalogue_row, $btns, $per_page) {
            $this->catalogue_row = $catalogue_row;
            $this->btns = $btns;
            $this->per_page = $per_page;
        }

        public function printCatalogue() {
            include_once 'catalogueContent.php';
        }
    }
?>