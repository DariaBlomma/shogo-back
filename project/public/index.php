<?php
    require_once 'products_controller.php';
    $controller = new ProductsController();
    $controller->page = $controller->workUrl('page');
    $controller->callView();
?>