# Arquitetura MVC do Sistema.

```text
Browser
   ↓
Router
   ↓
Controller
   ↓
Service
   ↓
Repository
   ↓
PostgreSQL

Controller
   ↓
View
   ↓
HTML + CSS + JS
```

Então o MVC fica com responsabilidades claras: **Model/Entity representa dados**, **View renderiza a interface** e **Controller coordena a requisição**.

## Estrutura recomendada

```text
ecommerce/
│
├── app/
│   ├── Controllers/
│   │   ├── HomeController.php
│   │   ├── AuthController.php
│   │   ├── ProductController.php
│   │   ├── CartController.php
│   │   └── Admin/
│   │       └── ProductController.php
│   │
│   ├── Models/
│   │   ├── User.php
│   │   ├── Product.php
│   │   ├── Category.php
│   │   └── ProductImage.php
│   │
│   ├── Services/
│   │   ├── AuthService.php
│   │   ├── ProductService.php
│   │   ├── CartService.php
│   │   └── ImageUploadService.php
│   │
│   ├── Repositories/
│   │   ├── UserRepository.php
│   │   ├── ProductRepository.php
│   │   ├── CategoryRepository.php
│   │   └── ProductImageRepository.php
│   │
│   ├── Middleware/
│   │   ├── AuthMiddleware.php
│   │   ├── AdminMiddleware.php
│   │   └── CsrfMiddleware.php
│   │
│   ├── Core/
│   │   ├── Router.php
│   │   ├── Request.php
│   │   ├── Response.php
│   │   ├── View.php
│   │   ├── Bootstrap.php
│   │   ├── Env.php
│   │   └── DatabaseConnection.php
│   │
│   └── Config/
│       ├── App.php
│       ├── Database.php
│       ├── Session.php
│       └── Jwt.php
│
├── views/
│   ├── layouts/
│   │   ├── main.php
│   │   ├── auth.php
│   │   └── admin.php
│   │
│   ├── components/
│   │   ├── header.php
│   │   ├── footer.php
│   │   ├── product-card.php
│   │   ├── category-filter.php
│   │   ├── form-input.php
│   │   └── flash-message.php
│   │
│   ├── home/
│   │   └── index.php
│   │
│   ├── auth/
│   │   ├── login.php
│   │   ├── register.php
│   │   └── forgot-password.php
│   │
│   ├── products/
│   │   ├── index.php
│   │   └── show.php
│   │
│   ├── cart/
│   │   └── index.php
│   │
│   └── admin/
│       └── products/
│           └── create.php
│
├── public/
│   ├── index.php
│   │
│   ├── css/
│   │   ├── reset.css
│   │   ├── variables.css
│   │   ├── base.css
│   │   ├── components/
│   │   │   ├── header.css
│   │   │   ├── buttons.css
│   │   │   ├── forms.css
│   │   │   ├── product-card.css
│   │   │   └── sidebar.css
│   │   └── pages/
│   │       ├── home.css
│   │       ├── auth.css
│   │       └── admin-product-create.css
│   │
│   └── js/
│       ├── main.js
│       ├── auth/
│       │   └── password-toggle.js
│       ├── cart/
│       │   └── cart.js
│       └── admin/
│           └── product-images.js
│
├── routes/
│   ├── web.php
│   └── admin.php
│
├── storage/
│   └── logs/
│
├── tests/
│
├── docker/
│   └── apache/
│       └── 000-default.conf
│
├── .env
├── .env.example
├── composer.json
├── composer.lock
├── Dockerfile
└── compose.yaml
```

## Como as telas entram nessa arquitetura

### Home / Vitrine

A tela da Home seria:

```text
GET /
 ↓
HomeController@index
 ↓
ProductService
 ↓
ProductRepository
 ↓
PostgreSQL
 ↓
views/home/index.php
```

O Controller poderia pedir:

```php
$featuredProducts = $productService->getFeaturedProducts();
$categories = $categoryService->getActiveCategories();
```

E passar isso para a View.

A View `home/index.php` cuidaria de montar:

```text
Header
Banner
Categorias
Produtos em Destaque
Footer
```

Os cards poderiam ser reutilizados:

```text
views/components/product-card.php
```

Isso evita repetir o mesmo HTML.

---

### Login

Fluxo:

```text
GET /login
 ↓
AuthController@showLogin
 ↓
views/auth/login.php
```

Ao enviar:

```text
POST /login
 ↓
CsrfMiddleware
 ↓
AuthController@login
 ↓
AuthService
 ↓
UserRepository
 ↓
PostgreSQL
```

O Controller não deveria verificar senha diretamente.

Isso fica no:

```text
AuthService
```

Por exemplo:

```php
final class AuthService
{
    public function login(string $email, string $password): bool
    {
        $user = $this->users->findByEmail($email);

        if ($user === null) {
            return false;
        }

        return password_verify(
            $password,
            $user->passwordHash
        );
    }
}
```

---

### Cadastro

Fluxo:

```text
GET /register
 ↓
AuthController@showRegister
 ↓
views/auth/register.php
```

Depois:

```text
POST /register
 ↓
CsrfMiddleware
 ↓
AuthController@register
 ↓
AuthService
 ↓
UserRepository
 ↓
PostgreSQL
```

O Service cuida das regras:

```text
e-mail válido
e-mail único
senha >= 8
confirmação da senha
aceite dos termos
role = CUSTOMER
hash da senha
```

---

### Admin / Cadastro de Produto

Essa tela teria:

```text
GET /admin/products/create
 ↓
AuthMiddleware
 ↓
AdminMiddleware
 ↓
Admin\ProductController@create
 ↓
CategoryRepository
 ↓
views/admin/products/create.php
```

Ao salvar:

```text
POST /admin/products
 ↓
AuthMiddleware
 ↓
AdminMiddleware
 ↓
CsrfMiddleware
 ↓
Admin\ProductController@store
 ↓
ProductService
 ↓
ProductRepository
 ↓
ImageUploadService
 ↓
PostgreSQL
```

O `ProductService` valida coisas como:

```text
nome obrigatório
categoria válida
preço > 0
estoque >= 0
status válido
imagem obrigatória se ACTIVE
máximo 3 imagens
```

## Onde fica o frontend?

Mesmo usando PHP, o frontend continua sendo composto por:

```text
views/
public/css/
public/js/
```

O PHP renderiza a estrutura HTML, enquanto CSS e JavaScript cuidam da apresentação e comportamento.

Então pense assim:

```text
FRONTEND
├── views/
│   └── HTML/PHP
├── public/css/
└── public/js/
```

E:

```text
BACKEND
├── Controllers/
├── Services/
├── Repositories/
├── Models/
├── Middleware/
├── Core/
└── Config/
```

## Separação de responsabilidades

Eu seguiria estas regras:

| Camada | Responsabilidade |
|---|---|
| View | HTML e apresentação |
| JavaScript | Interações da interface |
| Controller | HTTP/request/response |
| Service | Regra de negócio |
| Repository | SQL e banco |
| Model/Entity | Representação dos dados |
| Middleware | Autenticação, CSRF, autorização |
| Core | Infraestrutura da aplicação |
| Config | Configurações |

Isso evita, por exemplo, colocar:

```php
SELECT * FROM products
```

dentro de:

```text
views/home/index.php
```

ou colocar:

```php
password_hash(...)
```

diretamente no Controller.

## Rotas iniciais

Seu `routes/web.php` poderia começar conceitualmente assim:

```php
$router->get('/', [HomeController::class, 'index']);

$router->get('/login', [AuthController::class, 'showLogin']);
$router->post('/login', [AuthController::class, 'login']);

$router->get('/register', [AuthController::class, 'showRegister']);
$router->post('/register', [AuthController::class, 'register']);

$router->post('/logout', [AuthController::class, 'logout']);

$router->get('/products', [ProductController::class, 'index']);
$router->get('/products/{slug}', [ProductController::class, 'show']);

$router->get('/cart', [CartController::class, 'index']);
$router->post('/cart/items', [CartController::class, 'store']);
```

E o `routes/admin.php`:

```php
$router->get(
    '/admin/products/create',
    [Admin\ProductController::class, 'create']
);

$router->post(
    '/admin/products',
    [Admin\ProductController::class, 'store']
);
```

Por enquanto, **essas seriam as únicas rotas de administração**.

## Layouts baseados nas imagens

Eu criaria três layouts.

`views/layouts/main.php`:

```text
Header da loja
Conteúdo
Footer
```

para Home, produtos e carrinho.

`views/layouts/auth.php`:

```text
Página centralizada
Card branco
Logo
Conteúdo do formulário
```

para Login e Cadastro.

`views/layouts/admin.php`:

```text
Sidebar preta
Header administrativo
Conteúdo
```

para cadastro de produto.

Isso casa muito bem com as telas que você enviou e evita duplicação de HTML.

A arquitetura final fica, de forma resumida:

```text
                         Browser
                            │
                            ▼
                      public/index.php
                            │
                            ▼
                          Router
                            │
                  ┌─────────┴─────────┐
                  ▼                   ▼
             Middleware          Controller
                                      │
                                      ▼
                                   Service
                                      │
                                      ▼
                                  Repository
                                      │
                                      ▼
                                     PDO
                                      │
                                      ▼
                                  Supabase

Controller
    │
    ▼
   View
    │
    ├── Layout
    ├── Components
    ├── CSS
    └── JavaScript
```
