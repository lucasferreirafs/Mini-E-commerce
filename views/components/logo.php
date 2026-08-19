<?php
use App\Core\View;
use App\Core\Html;

?>

<div class="container-logo">
    <div class="logo bg-primary">
        <span class="text-logo-icon text-white">S</span>
    </div>
    <span class="text-logo <?= Html::escape($class ?? "") ?>">ShopX</span>
</div>