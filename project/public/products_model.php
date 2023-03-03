<?php
    class ProductsModel {
        public $host;
        public $db_name;
        public $user;
        public $password;    
        public $db;

        private $per_page = 5;
        private $art;
        private $news;
        private $news_row;
        private $btns;

        private $id;
        private $title;
        private $content;
        private $article;
        private $article_row;

        public function __construct($host, $db_name, $user, $password) {
            $this->host = $host;
            $this->db_name = $db_name;
            $this->user = $user;
            $this->password = $password;
        }

        public function connect() {
            if (!$this->db) {
                try {
                    $this->db = new PDO('mysql:host=' .$this->host .';dbname=' .$this->db_name , $this->user, $this->password, [
                       PDO::ATTR_ERRMODE => PDO::ERRMODE_SILENT,
                   ]);
                } catch (PDOException $e) {
                    print "Error!: " . $e->getMessage();
                    die();
                }
            }

            return $this->db;
        }

        public function createTables() {
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                $this->db->query("DROP TABLE IF EXISTS `product`;");
               $this->db->query("CREATE TABLE product (
                   id int(10) unsigned NOT NULL AUTO_INCREMENT,
                   position int(11) DEFAULT 0,
                   url varchar(255) NOT NULL,
                   name varchar(255) NOT NULL,
                   articul varchar(255) NOT NULL,
                   price decimal(10,2) NOT NULL,
                   currency_id int(10) unsigned DEFAULT NULL,
                   price_old decimal(10,2) NOT NULL,
                   notice text,
                   content text,
                   visible tinyint(1) NOT NULL,
                   PRIMARY KEY (id),
                   UNIQUE KEY url (url),
                   KEY currency_id (currency_id)
               ) ENGINE=InnoDB DEFAULT CHARSET=utf8");

               $this->db->query("DROP TABLE IF EXISTS `product_section`;");
               $sectionSql = "CREATE TABLE `product_section` (
                `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                `position` int(11) DEFAULT '0',
                `url` varchar(255) NOT NULL,
                `name` varchar(255) NOT NULL,
                `notice` text,
                `visible` tinyint(1) DEFAULT '0',
                PRIMARY KEY (`id`),
                KEY `url` (`url`)
              ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
              $this->db->query($sectionSql);

              $this->db->query("DROP TABLE IF EXISTS `product_type`;");
              $typeSql = "CREATE TABLE `product_type` (
                `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                `position` int(11) DEFAULT '0',
                `url` varchar(255) NOT NULL,
                `name` varchar(255) NOT NULL,
                `notice` text,
                `visible` tinyint(1) DEFAULT '0',
                PRIMARY KEY (`id`),
                KEY `url` (`url`)
              ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
              $this->db->query($typeSql);

               $this->db->query("DROP TABLE IF EXISTS `product_param_name`;");
              $paramNameSql = "CREATE TABLE `product_param_name` (
                 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                 `position` int(11) DEFAULT '0',
                 `visible` tinyint(1) NOT NULL,
                 `name` varchar(1024) NOT NULL,
                 PRIMARY KEY (`id`)
               ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
                $this->db->query($paramNameSql);

                 $this->db->query("DROP TABLE IF EXISTS `product_param_variant`;");
                $paramVariantSql = "CREATE TABLE `product_param_variant` (
                  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                  `param_id` int(10) unsigned NOT NULL,
                  `name` text NOT NULL,
                  `position` int(1) DEFAULT '0',
                  PRIMARY KEY (`id`),
                  UNIQUE KEY `value` (`param_id`,`name`(64)),
                  KEY `param_id` (`param_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
                $this->db->query($paramVariantSql);

                 $this->db->query("DROP TABLE IF EXISTS `product_assignment`;");
                $assignmentSql = "CREATE TABLE `product_assignment` (
                    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                    `product_id` int(10) unsigned NOT NULL,
                    `section_id` int(10) unsigned NOT NULL,
                    `type_id` int(10) unsigned NOT NULL,
                    `visible` tinyint(1) NOT NULL DEFAULT '1',
                    PRIMARY KEY (`id`),
                    KEY `product_id` (`product_id`),
                    KEY `section_id` (`section_id`),
                    KEY `type_id` (`type_id`),
                    KEY `visible` (`visible`)
                  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

                   $this->db->query($assignmentSql);

                    $this->db->query("DROP TABLE IF EXISTS `product_params`;");
                   $productParamsSql = "CREATE TABLE product_params (
                    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                    `product_id` int(10) unsigned NOT NULL,
                    `product_param_variant_id` int(10) unsigned NOT NULL,
                    PRIMARY KEY (`id`)
                   ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

                   $this->db->query($productParamsSql);

                   $this->db->query("CREATE OR REPLACE VIEW product_param_variant_name AS
                   SELECT
                        product_param_variant.id AS variant_id,
                        product_param_variant.name AS variant_name,
                        product_param_variant.param_id,
                        product_param_name.name AS param_name
                    FROM product_param_variant
                    INNER JOIN product_param_name ON product_param_variant.param_id=product_param_name.id
                    ");

                   $this->db->query("CREATE OR REPLACE VIEW product_view AS
                   SELECT
                        product_assignment.id,
                        product_assignment.product_id,
                        product.name AS product_name,
                        product.url AS product_url,
                        product.articul AS product_articul,
                        product.price AS product_price,
                        product.price_old AS product_price_old,
                        product_assignment.section_id,
                        product_section.name AS section_name,
                        product_assignment.type_id,
                        product_type.name AS type_name,
                        product_params.product_param_variant_id,
                        product_param_variant_name.variant_name,
                        product_param_variant_name.param_name
                    FROM product_assignment
                    LEFT JOIN product ON product_assignment.product_id=product.id
                    INNER JOIN product_section ON product_assignment.section_id=product_section.id
                    INNER JOIN product_type ON product_assignment.type_id=product_type.id
                    INNER JOIN product_params ON product_assignment.product_id=product_params.product_id
                    INNER JOIN product_param_variant_name ON product_param_variant_name.variant_id=product_params.product_param_variant_id
                    ");
        }

        public function fillTables() {
            $stmt = $this->db->query("SELECT COUNT(*) FROM product");
            $amount = $stmt->fetch();
            if ($amount[0] > 0) {
                return;
            }


           for ($i = 0; $i < 10; $i++) {
               $names = [
                   'happy new year',
                   'rosmarin',
                   'harpa toner',
                   'otherwise',
                   'awake and alive',
                   'suurin',
                   'mamma mia',
                   'oh, christmas tree',
                   'I surrender',
                   'I will always love you'
               ];
               $images = [
                   'https://lookw.ru/8/828/1476173423-125.jpg',
                   'https://www.wallpaperflare.com/static/204/345/205/cat-grass-lie-down-striped-wallpaper.jpg',
                   'https://files.globalgiving.org/pfil/6054/pict_featured_jumbo.jpg?t=1652172532000',
                   'https://1dens.files.wordpress.com/2013/10/cute-cats-068.jpg',
                   'https://wiki.mininuniver.ru/images/1/10/Animal-pet-cat-mammal.jpeg',
                   'https://rare-gallery.com/uploads/posts/742052-Cats-Glance.jpg',
                   'https://cdn.mos.cms.futurecdn.net/tjvena7BNVijpzVQ3fsCPX-1920-80.jpg',
                   'https://on-desktop.com/wps/Animals___Cats_Cat_resting_079697_.jpg',
                   'https://www.wallpaperflare.com/static/141/760/46/face-animals-cat-brown-wallpaper.jpg',
                   'https://media.baamboozle.com/uploads/images/635953/1645606501_207815_url.jpeg',
               ];
               $position = rand(1, 10);
               $visible = rand(0, 9);
               $minPrice = 1;
               $maxPrice = 1000;
               $randomPrice = rand($minPrice * 100, $maxPrice * 100) / 100;
               $randomOldPrice = rand($minPrice * 100, $maxPrice * 100) / 100;
               $articul = substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil(10/strlen($x)) )),1,10);
               $currency_id = rand(1, 5000);
               $param_id = $this->getParamForSection($i + 1);
                $data = [
                    'position' => $position,
                    'url' => $images[$i],
                    'name' => $names[$i],
                    'articul' => $articul,
                    'price' => $randomPrice,
                    'currency_id' => $currency_id,
                    'price_old' => $randomOldPrice,
                    'notice' => 'Some notice',
                    'content' => 'Some content',
                    'visible' => $visible,
                ];
                $rawPDO = $this->db->prepare("INSERT INTO product (position, url, name, articul, price, currency_id, price_old, notice, content, visible)
                    VALUES (:position, :url, :name, :articul, :price, :currency_id, :price_old, :notice, :content, :visible)");

               $rawPDO->execute($data);

               $dataParams = [
                'product_id' => $i + 1,
                'variant_id' => $param_id,
               ];
               $rawPDOParams = $this->db->prepare("INSERT INTO product_params (product_id, product_param_variant_id)
                VALUES (:product_id, :variant_id)");

               $rawPDOParams->execute($dataParams);
           }


            $this->db->query("INSERT INTO product_section (position, url, name, notice, visible) VALUES
            (
                1,
                'https://www.aixtec-components.de/files/website_data/Bilder/Beispielbilder/AdobeStock_140202697.jpg',
                'electronics',
                'computers, mobile phones, etc',
                9
           ),
           (
                2,
                'https://lachica.ru/wp-content/uploads/2022/10/185d71bd21b4175bb40af684a8f6edd8.jpeg',
                'furniture',
                'doors, chairs, tables, etc',
                7
           ),
           (
                3,
                'https://oir.mobi/uploads/posts/2021-03/1616606665_26-p-fon-odezhda-28.jpg',
                'clothes',
                'casual, tracking, business',
                6
           ),
           (
               4,
               'https://mobimg.b-cdn.net/v3/fetch/b0/b0c1455423885ff32ff45ee569997447.jpeg',
               'animals',
               'cats, dogs, birds, hamsters',
               5
           )
           ");

           $this->db->query("INSERT INTO product_type (position, url, name, notice, visible) VALUES
           (
               1,
               'https://www.collinsdictionary.com/images/full/mobilephone_103792316.jpg',
               'mobile phone',
               'some notice',
               1
           ),
           (
                2,
                'https://mebhome.ru/imgup/f16700___1965_.jpg',
                'desk',
                'some notice',
                2
           ),
           (
            3,
            'https://meindl.de/wp-content/uploads/2016/05/image-schuh-identity.jpg',
            'tracking shoes',
            'some notice',
            3
           ),
           (
            4,
            'https://lookw.ru/1/523/1402242672-oboi-1920h1080.-siamchiki-1.jpg',
            'kitten',
            'some notice',
            4
           )
           ");

           $this->db->query("INSERT INTO product_param_name (name, position, visible) VALUES
           (
                'for tracking',
                2,
                9
           ),
            (
                'with fur',
                3,
                8
            ),
           (
                'made of wood',
                4,
                7
           ),
           (
                'color',
                5,
                6
           )
           ");


           $this->db->query("INSERT INTO product_param_variant (name, position, param_id) VALUES
           (
            'high',
            3,
            1
           ),
           (
            'white',
            4,
            2
           ),
           (
            'pine',
            5,
            3
           ),
           (
            'black',
            6,
            4
           )
           ");

           $this->db->query("INSERT INTO product_assignment (product_id, section_id, type_id, visible) VALUES
                ( 1, 1, 1, 1 ),
                ( 2, 2, 2, 2 ),
                ( 3, 3, 3, 3 ),
                ( 4, 4, 4, 4 ),
                ( 5, 1, 1, 1 ),
                ( 6, 2, 2, 2 ),
                ( 7, 3, 3, 3 ),
                ( 8, 4, 4, 4 ),
                ( 9, 3, 3, 3 ),
                ( 10, 4, 4, 4 )
           ");

                         //var_dump($rawPDOSection->errorCode());
                         //echo ("<br />");
                         //var_dump($rawPDOSection->errorInfo());
        }

        private function getParamForSection($section_id) {
            switch ($section_id) {
                case 1 : return 4;
                case 2 : return 3;
                case 3 : return 1;
                case 4 : return 2;
                default : return rand(1, 4);
            }
        }

        public function selectNews($page) {
            $this->art = ($page * $this->per_page) - $this->per_page;
            $this->news = $this->db->prepare("SELECT * FROM product_view ORDER BY id ASC LIMIT :art, :page ");
            $this->news->bindValue(':art', $this->art, PDO::PARAM_INT);
            $this->news->bindValue(':page', $this->per_page, PDO::PARAM_INT);
            $this->news->execute();
            $this->news_row = $this->news->fetchAll(PDO::FETCH_ASSOC);
            return $this->news_row;
        }

        public function selectBtnPages() {
            $this->btns = $this->db->query("SELECT COUNT(*) FROM product_view");
            return $this->btns;
        }

        public function selectArticle($id) {
            $this->article = $this->db->prepare("SELECT name, content, url FROM product WHERE id = ? ");
            $this->article->execute([$id]);
            return $this->article;
        }

        public function givePerPage() {
            return $this->per_page;
        }
    }
?>