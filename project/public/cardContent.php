<?php
    while ($this->card_row = $this->card->fetch(PDO::FETCH_LAZY)) {
        $this->name = $this->card_row->name;
        $this->content = $this->card_row->content;
        $this->url = $this->card_row->url;
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class='header'>
        <h1 class='primary-title'><?php echo $this->name ?></h1>
    </header>
    <main class='main view-main'>
        <?php echo "
            <p>" . $this->content . "</p>
             <img src=" . $this->url . " width='500px height='500px' >"
        ?>
    </main>
    <footer class='footer'>
            <!-- <a href='news.php'>Все новости > ></a> -->
        <a class='return' href='index.php'>All products > ></a>
    </footer> 
</body>
<script>
    // перенаправить на страницу, откуда переходили на эту статью
    const redirect = () => {
        const link = document.querySelector('.return'),
        pageNumber = localStorage.getItem('pageNumber');

        if (pageNumber) {
            link.href = `index.php?page=${pageNumber}`;
        }  
    };
    redirect();
</script>
</html>
