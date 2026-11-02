# Produktu pārvaldības aplikācija

Vienkārša Laravel lietojumprogramma produktu uzskaitei: produktu saraksts, izveide,
apskate, rediģēšana, dzēšana un daudzuma maiņa (pievedums un izpirkšana) ar AJAX
atbalstu.

## Saturs

- [Tehnoloģijas](#tehnoloģijas)
- [Funkcionalitāte](#funkcionalitāte)
- [Maršruti](#maršruti)
- [Datu modelis](#datu-modelis)
- [Servera puses loģika](#servera-puses-loģika)
- [Testi](#testi)
- [Noderīgas komandas](#noderīgas-komandas)

## Tehnoloģijas

| Komponente | Versija / rīks |
| --- | --- |
| PHP | 8.3+ (projektā darbojas 8.5) |
| Laravel | 13.x |
| Datubāze | SQLite (`database/database.sqlite`) |
| Testi | Pest 5 |
| Koda stils | Laravel Pint |
| Frontend | Vite 8, Tailwind CSS 4 |
| JavaScript | Vaniļa JS (`resources/js/app.js`) |

## Funkcionalitāte

### 1. Produktu saraksts

- Maršruts: `GET /products` (`products.index`).
- Parāda visus produktus, jaunākos vispirms.
- Katram produktam redzams nosaukums, daudzums, derīguma termiņš, statuss un cena
  (ja norādīta).
- Darbības: apskatīt, rediģēt, dzēst.
- Ja produktu nav, tiek parādīts paziņojums “Produktu vēl nav.”.

### 2. Produkta izveide

- Maršruts: `GET /products/create` (`products.create`), saglabāšana ar `POST /products`.
- Lauki: nosaukums, daudzums, apraksts, derīguma termiņš, statuss, cena.
- Validācija:
  - `name` — obligāts, teksts, līdz 255 rakstzīmēm;
  - `quantity` — obligāts, vesels skaitlis, vismaz 0;
  - `description` — neobligāts teksts;
  - `expiration_date` — obligāts datums;
  - `status` — obligāts teksts, līdz 255 rakstzīmēm;
  - `price` — neobligāts skaitlis, vismaz 0.
- Pēc izveides notiek pāradresācija uz produkta apskates lapu ar veiksmes paziņojumu.

### 3. Produkta apskate

- Maršruts: `GET /products/{product}` (`products.show`).
- Parāda pilnu produkta informāciju.
- Satur daudzuma maiņas sadaļu ar divām darbībām:
  - **Pievedums (+)** — palielina daudzumu;
  - **Izpirkšana (−)** — samazina daudzumu.
- Ja daudzums ir 0, poga “Izpirkšana” ir atspējota.
- Nezināmam produkta ID tiek atgriezta `404` kļūda.

### 4. Daudzuma maiņa

- Atsevišķi maršruti:
  - `POST /products/{product}/quantity/increase` (`products.quantity.increase`);
  - `POST /products/{product}/quantity/decrease` (`products.quantity.decrease`).
- Ievades lauks `amount`: obligāts vesels skaitlis no 1 līdz 1 000 000.
- Samazināt zem 0 nav iespējams — tiek atgriezta validācijas kļūda ar pieejamo daudzumu.
- Izmaiņas notiek `DB::transaction()` iekšienē ar `lockForUpdate()`, lai novērstu
  vienlaicīgu pieprasījumu radītus zudumus.
- Produkta `status` netiek automātiski sinhronizēts ar daudzumu.
- **AJAX režīms:** `resources/js/app.js` pārtver formas iesniegšanu, nosūta `fetch`
  pieprasījumu ar JSON un atjaunina daudzumu lapā bez pārlādes.
  - Veiksmes atbilde: `{ "quantity": 13, "message": "Produkta daudzums palielināts par 3!" }`.
  - Kļūdas atbilde: `422` ar `errors.amount`.
  - Poga tiek atspējota pieprasījuma laikā, lai novērstu dubultu iesniegšanu.
- **Bez JavaScript:** forma darbojas arī parastā režīmā — pāradresācija atpakaļ uz
  produkta lapu ar `success` paziņojumu.

### 5. Produkta rediģēšana

- Maršruts: `GET /products/{product}/edit` (`products.edit`), saglabāšana ar
  `PUT/PATCH /products/{product}` (`products.update`).
- Izmanto to pašu formu un validācijas noteikumus kā izveidei.
- Pēc saglabāšanas notiek pāradresācija uz produkta apskates lapu.

### 6. Produkta dzēšana

- Maršruts: `DELETE /products/{product}` (`products.destroy`).
- Pirms dzēšanas pārlūkā tiek prasīts apstiprinājums.
- Pēc dzēšanas notiek pāradresācija uz produktu sarakstu.

### 7. Paziņojumi

- `resources/views/components/flash-success.blade.php` — veiksmes paziņojumi.
- `resources/views/components/flash-errors.blade.php` — validācijas kļūdu kopsavilkums.
- AJAX daudzuma maiņa papildus izmanto iekļauto paziņojumu zonu (`.flash`) tieši formā.

### 8. Lapas izkārtojums

- Kopējais izkārtojums: `resources/views/components/layout.blade.php`.
- “Holy grail” struktūra: galvene, kreisā navigācija, galvenais saturs, labā
  informācijas josla un kājene.
- Navigācija uz produktu sarakstu un jauna produkta pievienošanu.

### 9. Sākuma datu ģenerēšana

- `Database\Seeders\ProductSeeder` izveido 30 nejaušus produktus, izmantojot
  `ProductFactory`.

## Maršruti

| Metode | URI | Nosaukums | Darbība |
| --- | --- | --- | --- |
| GET | `/` | — | Pāradresē uz `products.index` |
| GET | `/products` | `products.index` | Produktu saraksts |
| GET | `/products/create` | `products.create` | Jauna produkta forma |
| POST | `/products` | `products.store` | Produkta izveide |
| GET | `/products/{product}` | `products.show` | Produkta apskate |
| GET | `/products/{product}/edit` | `products.edit` | Rediģēšanas forma |
| PUT/PATCH | `/products/{product}` | `products.update` | Produkta atjaunināšana |
| DELETE | `/products/{product}` | `products.destroy` | Produkta dzēšana |
| POST | `/products/{product}/quantity/increase` | `products.quantity.increase` | Daudzuma palielināšana |
| POST | `/products/{product}/quantity/decrease` | `products.quantity.decrease` | Daudzuma samazināšana |

## Datu modelis

Tabula `products` (modelis `App\Models\Product`):

| Kolonna | Tips | Īpašības |
| --- | --- | --- |
| `id` | bigint unsigned | Primārā atslēga |
| `name` | string | Obligāts |
| `description` | text | Neobligāts |
| `price` | decimal(10,2) | Neobligāts |
| `quantity` | unsigned integer | Noklusējums 0 |
| `expiration_date` | date | Neobligāts |
| `status` | string | Noklusējums `available` |
| `created_at`, `updated_at` | timestamp | Automātiski |

Modeļa `casts()`:

- `quantity` → `integer`;
- `expiration_date` → `date`;
- `price` → `decimal:2`.

## Servera puses loģika

### `App\Http\Controllers\ProductController`

- `index()` — ielādē produktus un atgriež saraksta skatu.
- `create()` — atgriež izveides formu.
- `store()` — validē datus, izveido produktu, pāradresē uz apskati.
- `show()` — atgriež produkta apskates skatu.
- `increaseQuantity()` — validē `amount`, izsauc `Product::increaseQuantity()`,
  atgriež JSON vai pāradresāciju.
- `decreaseQuantity()` — validē `amount`, izsauc `Product::decreaseQuantity()`,
  pārvērš `InsufficientQuantityException` par validācijas kļūdu, atgriež JSON vai
  pāradresāciju.
- `edit()` — atgriež rediģēšanas formu.
- `update()` — validē datus, atjaunina produktu, pāradresē uz apskati.
- `destroy()` — dzēš produktu, pāradresē uz sarakstu.
- `validateQuantityChange()` — kopīga `amount` validācija abām daudzuma darbībām.

### `App\Models\Product`

- `increaseQuantity(int $amount): int` — transakcijā ar rindas bloķēšanu palielina
  daudzumu un atgriež jauno vērtību.
- `decreaseQuantity(int $amount): int` — transakcijā ar rindas bloķēšanu samazina
  daudzumu, ja pietiek krājuma; pretējā gadījumā izmet
  `InsufficientQuantityException`.

### `App\Exceptions\InsufficientQuantityException`

- Domēna izņēmums ar publisku `availableQuantity` īpašību.
- Ziņojums: `Nepietiekams produkta daudzums. Pieejams: {quantity}.`.

## Testi

Testi atrodas mapē `tests/`. Visus testus palaiž ar:

```bash
php artisan test --compact
```

Pašlaik kopā: **52 testi**.

### `tests/Feature/ProductControllerTest.php`

Kontroliera darbību un HTTP slāņa testi (22 testi).

| Tests | Ko pārbauda |
| --- | --- |
| renders the product list | `GET products.index` atgriež 200, skatu `products.index` un produktu kolekciju ar izveidoto produktu. |
| renders the create form | `GET products.create` atgriež 200 un skatu `products.create`. |
| creates a product and redirects to its page | Derīgi dati izveido produktu ar pareiziem laukiem, pāradresē uz `products.show` un uzstāda veiksmes paziņojumu. |
| rejects invalid product data | Tukšs nosaukums, negatīvs daudzums, nederīgs datums, tukšs statuss un neskaitliska cena rada validācijas kļūdas attiecīgajiem laukiem. |
| renders a product | `GET products.show` atgriež 200, skatu `products.show` un pareizo produktu. |
| returns 404 for an unknown product | Neesošs produkta ID atgriež `404`. |
| increases the product quantity after a delivery | `POST` uz `quantity.increase` ar `amount=5` palielina daudzumu no 10 uz 15, pāradresē un parāda veiksmes ziņojumu. |
| decreases the product quantity after a purchase | `POST` uz `quantity.decrease` ar `amount=4` samazina daudzumu no 10 uz 6, pāradresē un parāda veiksmes ziņojumu. |
| does not decrease the quantity below zero | Mēģinājums samazināt 3 vienības par 4 atgriež validācijas kļūdu laukā `amount` un daudzums paliek 3. |
| requires a positive whole amount | `amount=0` un `amount=1.5` rada validācijas kļūdu, un daudzums nemainās. |
| shows the quantity controls on the product page | Apskates lapā ir abas pogas, abi daudzuma maršruti un `data-quantity-form` / `data-quantity-value` atribūti. |
| updates the quantity with an ajax request | `postJson` uz `quantity.increase` ar `amount=3` atgriež 200 un JSON ar `quantity=13` un ziņojumu; datubāzē daudzums ir 13. |
| returns validation errors for an ajax decrease below zero | `postJson` samazinājums zem 0 atgriež `422`, `errors.amount`, un daudzums nemainās. |
| decreases the quantity with an ajax request | `postJson` uz `quantity.decrease` ar `amount=4` atgriež 200 un JSON ar `quantity=6` un ziņojumu; datubāzē daudzums ir 6. |
| requires a positive whole amount when decreasing | `amount=0` un `amount=1.5` samazināšanā atgriež `422` ar `errors.amount`, un daudzums nemainās. |
| requires an amount for a quantity change | Pieprasījums bez `amount` abos virzienos atgriež `422` ar `errors.amount`, un daudzums nemainās. |
| accepts the maximum allowed amount | `amount=1000000` tiek pieņemts, atgriež 200 un datubāzē saglabā 1 000 000. |
| rejects an amount above the allowed maximum | `amount=1000001` abos virzienos atgriež `422` ar `errors.amount`, un daudzums nemainās. |
| renders the edit form | `GET products.edit` atgriež 200 un skatu `products.edit` ar pareizo produktu. |
| updates a product and redirects to its page | `PUT products.update` atjaunina visus laukus, pāradresē uz apskati un uzstāda veiksmes paziņojumu. |
| rejects invalid product data | (Atjaunināšanas tests.) Nederīgi dati `PUT products.update` rada kļūdas laukiem `name`, `quantity`, `expiration_date`, `status`. |
| deletes a product and redirects to the index | `DELETE products.destroy` izdzēš produktu, pāradresē uz sarakstu un modelis vairs neeksistē. |

### `tests/Feature/ProductQuantityTest.php`

Modeļa daudzuma loģikas testi (8 testi).

| Tests | Ko pārbauda |
| --- | --- |
| increases the quantity and returns the new value | `increaseQuantity(4)` no 10 atgriež 14, atjaunina modeļa instanci un datubāzi. |
| increases the quantity from zero | `increaseQuantity(5)` pie daudzuma 0 atgriež 5 un saglabā to datubāzē. |
| uses the current database value when the model is stale | (Palielināšana.) Ja datubāzē daudzums ir 20, bet modeļa instancē — 10, `increaseQuantity(5)` izmanto datubāzes vērtību un atgriež 25. |
| decreases the quantity and returns the new value | `decreaseQuantity(4)` no 10 atgriež 6, atjaunina modeļa instanci un datubāzi. |
| allows decreasing the quantity to zero | `decreaseQuantity(5)` no 5 atgriež 0 un saglabā 0 datubāzē. |
| uses the current database value when the model is stale | (Samazināšana.) Ja datubāzē daudzums ir 20, bet modeļa instancē — 10, `decreaseQuantity(5)` izmanto datubāzes vērtību un atgriež 15. |
| throws when the requested amount exceeds the available quantity | `decreaseQuantity(3)` pie daudzuma 2 izmet `InsufficientQuantityException` ar konkrētu ziņojumu un daudzums paliek 2. |
| exposes the available quantity on the exception | Ja izņēmums netiek izmests, tests neizdodas; pretējā gadījumā pārbauda, ka `availableQuantity` ir 2. |

### `tests/Feature/ProductWorkflowTest.php`

Pilnu lietotāja scenāriju integrācijas testi (8 testi). Šie testi iet cauri visam
pieprasījuma ceļam — maršruts, validācija, kontrolieris, modelis, datubāze un skats —
un pārbauda, kā vienības darbojas kopā, nevis katra atsevišķi.

| Tests | Ko pārbauda |
| --- | --- |
| creates a product through the form and shows it on the show and index pages | Atver izveides formu, iesniedz datus, pēc pāradresācijas redz veiksmes ziņojumu un produktu apskates lapā, pārbauda saglabātos laukus datubāzē un produkta parādīšanos sarakstā. |
| edits a product through the form and shows the updated values | Atver rediģēšanas formu ar aizpildītām vecajām vērtībām, saglabā jaunos datus, pēc pāradresācijas redz atjauninātās vērtības un pārbauda tās datubāzē un sarakstā. |
| increases the quantity from the product page and persists the change | No apskates lapas veic pievedumu, pēc pāradresācijas redz jauno daudzumu un ziņojumu, pārbauda datubāzi un sarakstu. |
| decreases the quantity from the product page and persists the change | No apskates lapas veic izpirkšanu, pēc pāradresācijas redz jauno daudzumu un ziņojumu, pārbauda datubāzi un sarakstu. |
| rejects a decrease larger than the stock and keeps the product unchanged | Mēģina izpirkt vairāk par pieejamo; lapa parāda validācijas kļūdu ar pieejamo daudzumu, un produkts datubāzē nemainās. |
| handles quantity changes through ajax and renders the persisted values on the next page load | Veic AJAX palielināšanu un samazināšanu, pārbauda JSON atbildes, pēc tam ielādē lapu no jauna un pārbauda saglabātās vērtības skatā un sarakstā. |
| returns ajax validation errors when the requested amount exceeds the stock | AJAX samazinājums virs krājuma atgriež `422` ar konkrētu `errors.amount` ziņojumu, un daudzums nemainās. |
| disables the decrease control for an empty product and enables it after a delivery | Produktam ar daudzumu 0 poga “Izpirkšana” ir atspējota; pēc pieveduma tā vairs nav atspējota un daudzums ir atjaunināts. |

### `tests/Feature/ProductTest.php`

Vispārīgi produkta funkciju un skatu testi (12 testi).

| Tests | Ko pārbauda |
| --- | --- |
| the product index lists products | Saraksta lapa parāda izveidoto produktu nosaukumus. |
| the product seeder creates at least 20 products | `ProductSeeder` izveido vismaz 20 produktus (faktiski 30). |
| the product create page is displayed | Izveides lapa atgriež 200 un satur virsrakstu “Jauns produkts”. |
| a product can be created | Produkts tiek izveidots ar visiem laukiem, tostarp datumu, statusu un cenu. |
| the product show page is displayed | Apskates lapa parāda produkta nosaukumu un aprakstu. |
| the product edit page is displayed | Rediģēšanas lapa atgriež 200 un parāda produkta nosaukumu. |
| a product can be updated | Atjaunināšana saglabā jaunos laukus un pāradresē uz apskati. |
| a product requires valid data | Nederīgi izveides dati rada kļūdas laukiem `name`, `quantity`, `expiration_date`, `status`, `price`. |
| validation errors are displayed in the flash errors component | Pēc neveiksmīgas iesniegšanas lapā parādās `flash-errors` komponente ar kļūdu virsrakstu. |
| success messages are displayed in the flash success component | Pēc veiksmīgas izveides lapā parādās `flash-success` komponente ar ziņojumu. |
| a product can be deleted | Dzēšana pāradresē uz sarakstu un produkts vairs neeksistē. |
| the product pages use the holy grail layout | Lapā pareizā secībā ir galvene, kreisā navigācija, galvenais saturs, labā josla un kājene. |

### `tests/Feature/ExampleTest.php`

| Tests | Ko pārbauda |
| --- | --- |
| the application redirects to the product index | Saknes maršruts `/` pāradresē uz `products.index`. |

### `tests/Unit/ExampleTest.php`

| Tests | Ko pārbauda |
| --- | --- |
| that true is true | Vienkāršs tests, kas pārbauda testu vides darbību (`true` ir `true`). |

## Noderīgas komandas

```bash
# Visa testu kopa
php artisan test --compact

# Konkrēts tests vai fails
php artisan test --compact --filter=testName
vendor/bin/pest tests/Feature/ProductQuantityTest.php

# Koda formatēšana
vendor/bin/pint --dirty --format agent

# Frontend būvēšana
npm run build

# Izstrādes serveris ar Vite
composer run dev

# Datubāzes migrācijas un sākuma dati
php artisan migrate
php artisan db:seed --class=ProductSeeder
```
