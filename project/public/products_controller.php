<?php
    require_once 'products_model.php';
    require_once 'catalogue_view.php';
    require_once 'card_view.php';
    class ProductsController {
        public $id;
        public $page;
        private $model;
        private $catalogue_view;
        private $article_view;
        private $news_row;
        private $article;
        private $article_row;
        private $btns;
        private $per_page;
        private $variant;

        public function __construct() {
            $this->model = new ProductsModel('mysql', 'laraveldb', 'laravel', '12345678');
        }
        
        public function workUrl($param) {
            if (isset($_GET[$param])){
                $var = $_GET[$param];
            } else $var = 1;

            if ($param === 'page') {
                $this->page = $var;
            } else {
                $this->id = $var;
            }

            $this->variant = $param;
            $this->callModel();
            return $var;
        }

        public function getPage() {
            echo $this->page;
        }

        private function callModel() {
            $this->model->connect();
            $this->model->createTables();
            $this->model->fillTables();
            if ($this->variant === 'page') {
                $this->news_row = $this->model->selectNews($this->page);
                $this->btns = $this->model->selectBtnPages();
                $this->per_page = $this->model->givePerPage();
            } else {
                $this->article = $this->model->selectArticle($this->id);
            }
        }

        public function callView() {
            if ($this->variant === 'page') {
                $this->catalogue_view = new CatalogueView($this->news_row, $this->btns, $this->per_page);
                $this->catalogue_view->printCatalogue();
            } else {
                $this->card_view = new CardView($this->article);
                $this->card_view->printCard();
            }
        }
    }
?>