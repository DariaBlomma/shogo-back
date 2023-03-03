<?php
    require_once 'products_controller.php';
    $controller = new ProductsController();
    $controller->id = $controller->workUrl('id');
    $controller->callView();
?>

