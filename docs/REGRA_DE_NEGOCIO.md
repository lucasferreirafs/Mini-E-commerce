
# Regras de Negócios

## 1. Escopo funcional desta primeira versão

Pelas telas, teremos inicialmente:

```text
E-COMMERCE
│
├── Cliente
│   ├── Cadastro
│   ├── Login
│   ├── Logout
│   ├── Recuperação de senha
│   ├── Visualização de produtos
│   ├── Categorias
│   ├── Busca
│   └── Carrinho
│
└── Administração
    └── Produtos
        └── Cadastrar produto
```

Existirão inicialmente dois papéis:

```text
CUSTOMER
ADMIN
```

A autorização deve acontecer **no backend**, nunca apenas escondendo elementos no HTML.

---

# 2. Regras de negócio — Usuário

## RN-USR-001 — Cadastro único por e-mail

Cada usuário deve possuir um e-mail único.

Antes de cadastrar:

```text
email
 ↓
normalizar
 ↓
procurar no banco
 ↓
já existe?
 ├─ SIM → rejeitar cadastro
 └─ NÃO → continuar
```

O banco também deve possuir uma restrição `UNIQUE` sobre o e-mail.

---

## RN-USR-002 — Campos obrigatórios

De acordo com a tela de cadastro:

```text
Nome completo
E-mail
Senha
Confirmação da senha
Aceite dos termos
```

Todos são obrigatórios.

A validação do HTML melhora UX, mas **todas as regras devem ser repetidas no PHP**.

---

## RN-USR-003 — E-mail válido

O backend deve validar o formato:

```php
filter_var($email, FILTER_VALIDATE_EMAIL);
```

Também recomendo normalizar:

```text
"  USER@email.com "
        ↓
"user@email.com"
```

---

## RN-USR-004 — Política de senha

A interface atualmente informa:

> Mínimo 8 caracteres

Eu adotaria como regra inicial:

```text
mínimo: 8 caracteres
máximo: 72 ou um limite explicitamente definido
```

E não obrigaria artificialmente:

```text
1 maiúscula
1 número
1 símbolo
```

Uma senha longa é mais importante do que regras excessivamente rígidas.

Podemos posteriormente adicionar indicador de força.

---

## RN-USR-005 — Confirmação da senha

Os campos:

```text
senha
confirmar_senha
```

precisam ser iguais.

A confirmação **não vai para o banco**.

---

## RN-USR-006 — Senhas nunca são armazenadas diretamente

Nunca:

```text
12345678
```

no banco.

Utilizaremos:

```php
password_hash($password, PASSWORD_DEFAULT);
```

E no login:

```php
password_verify($password, $hash);
```

A coluna deve ser algo como:

```text
password_hash
```

e não `password`.

---

## RN-USR-007 — Aceite dos termos

A conta só pode ser criada se:

```text
terms_accepted = true
```

Idealmente devemos registrar também:

```text
terms_accepted_at
```

Isso deixa registrado **quando** houve o aceite.

---

## RN-USR-008 — Papel padrão

Usuários cadastrados pela página pública sempre recebem:

```text
CUSTOMER
```

Nunca devemos aceitar algo como:

```http
POST /register

role=ADMIN
```

e confiar nesse valor.

O backend define:

```php
$role = 'CUSTOMER';
```

Usuários administradores devem ser criados por mecanismo administrativo controlado, seed ou diretamente em processo seguro.

---

# 3. Login

A tela possui:

```text
E-mail
Senha
Esqueci minha senha
Entrar
Continuar com Google
Criar uma conta
```

Para a primeira versão, eu trataria Google OAuth como **funcionalidade posterior**, a menos que você queira implementá-lo agora.

## RN-AUTH-001 — Credenciais

O login acontece através de:

```text
e-mail + senha
```

Fluxo:

```text
POST /login
     ↓
validar CSRF
     ↓
validar campos
     ↓
buscar usuário pelo e-mail
     ↓
password_verify()
     ↓
válido?
 ┌───────┴────────┐
 NÃO              SIM
  ↓                ↓
erro          criar sessão
```

---

## RN-AUTH-002 — Mensagem genérica

Não retornar:

> Este e-mail não está cadastrado.

nem:

> A senha está incorreta.

Use:

> E-mail ou senha inválidos.

Isso evita facilitar enumeração de usuários.

---

## RN-AUTH-003 — Regeneração da sessão

Depois do login:

```php
session_regenerate_id(true);
```

para reduzir risco de **session fixation**.

---

## RN-AUTH-004 — Sessão

Para o site tradicional PHP, **sessão será nossa autenticação principal**.

O cookie deve ser configurado com:

```text
HttpOnly
SameSite
Secure (produção/HTTPS)
```

Isso é importante porque anteriormente discutimos JWT, mas não precisamos transformar JWT em autenticação principal só porque instalamos a biblioteca.

### Site tradicional

```text
Browser
  ↓
Session Cookie
  ↓
PHP Session
```

### API futura

```text
Client
 ↓
JWT
 ↓
API
```

Essa separação é mais adequada.

---

## RN-AUTH-005 — Rate limiting

Login deve possuir limitação de tentativas.

Por exemplo, podemos definir uma política como:

```text
5 tentativas
dentro de 15 minutos
```

seguida de limitação temporária.

Os valores exatos podem virar configuração.

---

# 4. Recuperação de senha

A tela possui:

> Esqueci minha senha

Portanto precisamos prever essa funcionalidade.

## RN-AUTH-006

Usuário informa:

```text
email
```

O sistema sempre responde algo genérico como:

> Se existir uma conta associada a esse e-mail, enviaremos as instruções.

Novamente evitando enumeração.

---

## RN-AUTH-007 — Token

Gerar token criptograficamente seguro:

```php
random_bytes(32);
```

Não armazenaria o token puro no banco.

Fluxo:

```text
token
 ↓
hash
 ↓
Banco
```

O link contém o token original.

Também deverá existir:

```text
expires_at
used_at
```

O token:

- expira;
- só pode ser utilizado uma vez;
- torna-se inválido após alteração da senha.

---

# 5. Catálogo / Home

A Home apresenta:

```text
Busca

Categorias
├── Todos
├── Calçados
├── Roupas
├── Acessórios
├── Eletrônicos
└── Bolsas

Produtos em Destaque
```

## RN-CAT-001 — Somente produtos ativos

A loja pública só pode mostrar produtos:

```text
status = ACTIVE
```

Um produto inativo continua existindo no banco, mas desaparece da vitrine.

---

## RN-CAT-002 — Produtos em destaque

A Home possui "Produtos em Destaque".

Portanto, precisamos de uma propriedade como:

```text
is_featured
```

Somente produtos:

```text
ACTIVE
+
is_featured = true
```

aparecem nessa seção.

---

## RN-CAT-003 — Categorias

Cada produto pertence a uma categoria.

Por exemplo:

```text
Tênis Runner
     ↓
Calçados
```

Eu não armazenaria:

```text
category = "Calçados"
```

diretamente em `products`.

Teríamos:

```text
categories

id
name
slug
```

e:

```text
products.category_id
```

---

## RN-CAT-004 — "Todos"

`Todos` **não é uma categoria no banco**.

É simplesmente:

```sql
SELECT ...
FROM products
WHERE status = 'ACTIVE';
```

Enquanto `Calçados`, por exemplo, adiciona filtro por categoria.

---

# 6. Busca

A barra:

> Buscar produtos...

deve procurar inicialmente por:

```text
nome
descrição
```

Eu começaria simples.

Exemplo:

```text
"tenis"

↓ procura

Tênis Runner
Tênis Casual
Tênis Infantil
```

Posteriormente podemos melhorar relevância usando recursos do PostgreSQL.

---

## RN-CAT-005 — Busca vazia

Busca vazia não deve gerar erro.

Pode:

```text
redirecionar para catálogo
```

ou retornar todos os produtos.

---

## RN-CAT-006 — Paginação

Não devemos carregar 5.000 produtos de uma vez.

A listagem deverá ser paginada.

Por exemplo:

```text
24 produtos por página
```

Esse número pode ser configurável.

---

# 7. Produto

A tela administrativa define muito bem os dados iniciais:

```text
Nome
Categoria
Estoque inicial
Preço
Descrição
Imagens
Status
```

Mas eu acrescentaria alguns campos técnicos.

## RN-PRD-001 — Produto

Inicialmente:

```text
Product
├── id
├── name
├── slug
├── description
├── price
├── stock
├── category_id
├── status
├── is_featured
├── created_at
└── updated_at
```

---

## RN-PRD-002 — Nome

Obrigatório.

Recomendo:

```text
3–150 caracteres
```

O backend remove espaços desnecessários nas extremidades.

---

## RN-PRD-003 — Slug

O sistema gera automaticamente.

Por exemplo:

```text
Tênis Esportivo Runner Pro
```

vira:

```text
tenis-esportivo-runner-pro
```

Esse slug poderá ser utilizado futuramente:

```text
/produtos/tenis-esportivo-runner-pro
```

Não peça ao administrador para digitá-lo.

---

# 8. Preço

Essa regra é especialmente importante em e-commerce.

## RN-PRD-004 — Preço positivo

Produto não pode possuir:

```text
R$ -10
```

Preço deve ser:

```text
>= 0
```

ou, caso produtos gratuitos não façam sentido no negócio:

```text
> 0
```

Eu adotaria `> 0` para essa loja.

---

## RN-PRD-005 — Nunca use `float` para dinheiro

No PHP não quero:

```php
$price = 189.90;
```

como representação principal de dinheiro.

Uma estratégia simples e segura é trabalhar internamente em **centavos**:

```text
R$ 189,90

↓

18990
```

Assim:

```text
price_cents = 18990
```

Isso evita vários problemas de precisão.

---

# 9. Promoções

A Home apresenta:

```text
R$ 189,90
R$ 249,90
Sale
```

Portanto existe implicitamente uma regra de promoção.

Eu modelaria:

```text
price_cents
compare_at_price_cents
```

Exemplo:

```text
price_cents            = 18990
compare_at_price_cents = 24990
```

Então:

```text
R$ 189,90    R$ 249,90
```

---

## RN-PRD-006 — Regra de promoção

Se houver preço anterior:

```text
compare_at_price > price
```

obrigatoriamente.

Não faz sentido:

```text
Preço atual:    R$ 200
Preço anterior: R$ 150
```

O selo:

```text
Sale
```

pode ser calculado automaticamente.

Não precisamos armazenar:

```text
is_sale = true
```

se a promoção pode ser deduzida pelos preços.

---

# 10. Estoque

Na administração existe:

> Estoque Inicial

## RN-STK-001

Estoque:

```text
>= 0
```

e deve ser inteiro.

Não existe:

```text
1.5 tênis
```

---

## RN-STK-002 — Sem estoque

Quando:

```text
stock = 0
```

o produto pode continuar aparecendo na loja, mas deverá mostrar:

> Produto indisponível

e o botão:

> Adicionar ao carrinho

fica desabilitado.

Isso é melhor do que simplesmente apagar o produto da vitrine.

---

# 11. Imagens

A administração mostra:

```text
PNG, JPG
até 10 MB
```

## RN-IMG-001

Eu permitiria:

```text
JPEG
PNG
WebP
```

E validaria **o MIME type real do arquivo no backend**, não apenas a extensão.

Não devemos confiar em:

```text
produto.jpg
```

porque um arquivo malicioso pode simplesmente ter sido renomeado.

---

## RN-IMG-002 — Tamanho

De acordo com o design:

```text
máximo 10 MB por imagem
```

---

## RN-IMG-003 — Quantidade

Pelo design temos três slots de imagem.

Então podemos definir inicialmente:

```text
máximo: 3 imagens
```

E:

```text
primeira imagem = imagem principal
```

---

## RN-IMG-004 — Nome do arquivo

Nunca salvar usando diretamente:

```text
$_FILES['image']['name']
```

como nome final.

Geramos um identificador próprio, por exemplo:

```text
UUID/random identifier
```

e armazenamos o caminho/URL no banco.

---

# 12. Status

A tela possui:

> Ativo (visível na loja)

Eu trabalharia inicialmente com:

```text
ACTIVE
INACTIVE
```

### ACTIVE

Aparece no catálogo.

### INACTIVE

Continua cadastrado, mas não aparece publicamente.

Isso permite ao administrador retirar temporariamente um produto sem excluí-lo.

---

# 13. Cadastro administrativo de produto

Agora chegamos exatamente à tela Admin que você pediu.

## RN-ADM-001 — Somente ADMIN

A rota:

```text
/admin/products/create
```

só pode ser acessada por:

```text
role = ADMIN
```

Cliente tentando acessar:

```text
403 Forbidden
```

ou redirecionamento apropriado.

---

## RN-ADM-002 — Campos

Obrigatórios:

```text
Nome
Categoria
Preço
Estoque
Status
```

Descrição pode ser obrigatória ou opcional. Para este e-commerce eu a tornaria **obrigatória**, pois é informação essencial de produto.

Imagem principal também deveria ser obrigatória antes de um produto ficar `ACTIVE`.

---

## RN-ADM-003 — Produto ativo exige imagem

Podemos permitir:

```text
INACTIVE
sem imagem
```

para o administrador preparar um cadastro.

Mas:

```text
ACTIVE
```

deve exigir pelo menos:

```text
1 imagem válida
```

Isso evita produtos quebrados na vitrine.

---

# 14. Carrinho

A Home já possui:

> Adicionar ao Carrinho

Então precisamos definir suas regras.

## RN-CART-001 — Visitante pode ter carrinho

Eu recomendo **não obrigar login para adicionar produto ao carrinho**.

Fluxo:

```text
Visitante
   ↓
Adicionar produto
   ↓
Carrinho na sessão
```

O login pode ser exigido posteriormente no checkout.

Isso reduz atrito de compra.

---

## RN-CART-002 — Quantidade

Quantidade:

```text
>= 1
```

e:

```text
<= estoque disponível
```

---

## RN-CART-003 — Backend valida estoque

Mesmo que o JavaScript mostre:

```text
Estoque: 5
```

e limite a interface a 5 unidades, o PHP **precisa verificar novamente**.

Nunca confiar no navegador.

---

## RN-CART-004 — Preço confiável

Essa é uma regra crítica.

O navegador **nunca determina o preço**.

Nunca confie em algo enviado como:

```http
product_id=10
price=1.00
```

O cliente envia essencialmente:

```text
product_id
quantity
```

O servidor busca:

```text
product_id = 10
 ↓
Banco
 ↓
price = 18990
```

O preço válido vem do banco.

---

# 15. Avaliações

A Home mostra estrelas e:

```text
(128)
(87)
(214)
```

Isso implica um sistema de reviews.

Mas eu **não implementaria avaliações agora**, porque ainda não temos pedidos.

A regra futura ideal será:

> Somente clientes que compraram o produto podem fazer uma avaliação verificada.

Por enquanto eu removeria ou trataria essas estrelas como parte futura do design, em vez de inventarmos dados falsos.

---

# 16. Banner e coleção

A Home possui:

```text
Coleção de inverno 2025
Estilo que fala por você
Até 40% de desconto
Ver ofertas
```

Para a primeira versão eu **não criaria um CMS de banners**.

Podemos deixar esse conteúdo estático no frontend.

Depois podemos criar:

```text
banners
campaigns
promotions
```

quando o restante do e-commerce estiver pronto.

---

# 17. Regras de segurança globais

Além das regras de negócio, eu definiria desde já estas regras técnicas:

| Código | Regra |
|---|---|
| SEG-001 | Toda entrada externa é não confiável |
| SEG-002 | Toda validação importante acontece no backend |
| SEG-003 | SQL usa Prepared Statements |
| SEG-004 | Saída HTML dinâmica é escapada |
| SEG-005 | Formulários POST usam CSRF Token |
| SEG-006 | Senhas usam `password_hash()` |
| SEG-007 | Login possui rate limiting |
| SEG-008 | Sessão é regenerada após autenticação |
| SEG-009 | Autorização ADMIN é verificada no servidor |
| SEG-010 | Upload valida MIME, tamanho e quantidade |
| SEG-011 | Erros internos não são exibidos em produção |
| SEG-012 | Operações críticas de estoque usam transações |

---

# 18. Modelo inicial do banco

Com essas regras, eu começaria com aproximadamente:

```text
users
├── id
├── name
├── email UNIQUE
├── password_hash
├── role
├── terms_accepted_at
├── created_at
└── updated_at

categories
├── id
├── name
├── slug
└── created_at

products
├── id
├── category_id FK
├── name
├── slug UNIQUE
├── description
├── price_cents
├── compare_at_price_cents NULL
├── stock
├── status
├── is_featured
├── created_at
└── updated_at

product_images
├── id
├── product_id FK
├── path
├── position
├── is_primary
└── created_at

password_reset_tokens
├── id
├── user_id FK
├── token_hash
├── expires_at
├── used_at
└── created_at
```

O carrinho inicialmente pode permanecer na **sessão**, portanto ainda não precisamos obrigatoriamente de `carts` e `cart_items`.

---

## Arquitetura resultante

Com isso, nosso backend começa a ter uma direção bem clara:

```text
                        Browser
                           │
                           ▼
                        Router
                           │
                    ┌──────┴──────┐
                    ▼             ▼
               Middleware     Controller
              Auth / CSRF          │
                                   ▼
                                Service
                                   │
                    ┌──────────────┼──────────────┐
                    ▼              ▼              ▼
               UserService   ProductService   CartService
                    │              │
                    ▼              ▼
               Repository     Repository
                    │              │
                    └───────┬──────┘
                            ▼
                           PDO
                            │
                            ▼
                     PostgreSQL
                       Supabase
```

Regra arquitetural importante: **Controller trata HTTP, Service contém regra de negócio, Repository contém SQL e Model/Entity representa os dados do domínio**.