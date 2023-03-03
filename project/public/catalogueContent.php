<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header class='header'>
    <h1 class='primary-title'>Products</h1>
</header>
<main class='main stretched'>
    <?php
        foreach ($this->catalogue_row as $k => $v){
            echo "
            <article class='card'>
                <span class='articul'>" . $v['product_articul'] . "</span>
                <a class='title' href='product.php?id=" . $v['product_id'] . " '>" . $v['product_name'] . "</a>
                <p>Product id " . $v['product_id'] . "</p>
                <p>Product price: " . $v['product_price'] . "</p>
                <p>Product price old: " . $v['product_price_old'] . "</p>
                <p>Section name: " . $v['section_name'] . "</p>
                <p>Type name: " . $v['type_name'] . "</p>
                <p>Parameter name: " . $v['param_name'] . "</p>
                <p>Parameter variant: " . $v['variant_name'] . "</p>
                <img src=" . $v['product_url'] . " width='150px height='150px'>
            </article>";
        }
    ?>
</main>
<footer class='footer'>
    <h3 class='title-3'>Pages :</h3>
    <div class='pages-btns'>
    <?php
        // кол-во строк в таблице
        while ($btns_row = $this->btns->fetch()) {
            $btns_total = $btns_row[0];
            // кол-во страниц
            $pages_amount = ceil($btns_total / $this->per_page);
            for ($i = 1; $i <= $pages_amount; $i++) {
                echo "<a href='index.php?page=".$i."' class='btn'>" . $i . "</a>";
            }
        }
    ?>
    </div>
</footer>
</body>
<script src='script.js'></script>
</html>
