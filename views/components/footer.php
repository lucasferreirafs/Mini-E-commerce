<?php
use App\Core\View;
?>

<footer class="bg-foreground">
    <div class="container-footer">
        <div id="social-media">
            <?php View::component('logo', ["class" => "text-white"]) ?>

            <p class="text-white">
                Sua loja online com os melhores preços e curadoria de produtos.
            </p>

            <div id="links-social">
                <a href="https://www.instagram.com/" target="_blank" class="footer-link-social">
                    <i class="fa-brands fa-instagram"></i>
                </a>

                <a href="https://x.com/" target="_blank" class="footer-link-social">
                    <i class="fa-brands fa-x-twitter"></i>
                </a>

                <a href="https://www.facebook.com/" target="_blank" class="footer-link-social">
                    <i class="fa-brands fa-facebook-f"></i>
                </a>
            </div>
        </div>

        <div id="quick-links">
            <h3 class="text-white">Links Rápidos</h3>
            <ul id="list-quick-links">
                <li>
                    <a href="#" class="text-white">Sobre nós</a>
                </li>
                <li>
                    <a href="#" class="text-white">Política de Privacidade</a>
                </li>
                <li>
                    <a href="#" class="text-white">Termos de Uso</a>
                </li>
                <li>
                    <a href="#" class="text-white">Contato</a>
                </li>
                <li>
                    <a href="#" class="text-white">Ajuda</a>
                </li>
            </ul>
        </div>

        <div id="payment-methods">
            <h3 class="text-white">Formas de Pagamento</h3>

            <div id="footer-methods">
                <span class="text-white">
                    <img width="48" height="48" src="/assets/image/visa.webp" alt="Visa"/>
                    Visa
                </span>
                <span class="text-white">
                    <img width="48" height="48" src="/assets/image/mastercard.webp" alt="Mastercard">
                    Mastercard
                </span>
                <span class="text-white">
                    <img width="48" height="48" src="/assets/image/pix.webp" alt="Pix"/>
                    Pix
                </span>
                <span class="text-white">
                    <img width="48" height="48" src="/assets/image/boleto.webp" alt="Boleto Bancario"/>
                    Boleto
                </span>
            </div>
        </div>
    </div>
</footer>