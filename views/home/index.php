<?php

use App\Core\View;

?>

<div id="home">
    <section id="offers">
        <div class="content-offers">
            <div class="container-content">
                <div class="badge-offers bg-accent">
                    <span class="text-white">coleção de inverno 2026</span>
                </div>

                <h1 class="text-white">Estilo que fala por você</h1>

                <P class="text-white">Até 40% de desconto em peças selecionadas. Só esta semana.</P>

                <a href="#" class="bg-accent text-white">Ver Ofertas</a>
            </div>
        </div>
    </section>

    <section id="container-main">
        <!-- FILTRO -->
        <div class="filter-links">
            <a href="?todos" class="active">Todos</a>
            <a href="?calcados">Calçados</a>
            <a href="?roupas">Roupas</a>
            <a href="?acessorios">Acessórios</a>
            <a href="?eletronicos">Eletrônicos</a>
            <a href="?bolsas">Bolsas</a>
        </div>

        <div id="container-products">
            <div class="header-products">
                <h2>Produtos em Destaque</h2>
                <a href="#">Ver todos</a>
            </div>

            <div class="container-cards">
                <?php View::component('card'); ?>
                <?php View::component('card'); ?>
                <?php View::component('card'); ?>
                <?php View::component('card'); ?>
                <?php View::component('card'); ?>
            </div>
        </div>
    </section>
</div>