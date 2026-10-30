<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use OverflowException;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * Domain-specific product catalog used to generate realistic test data.
     *
     * @var array<int, array{
     *     name: string,
     *     description: string,
     *     price: array{0: float, 1: float},
     *     shelf_life: array{0: int, 1: int}
     * }>
     */
    private const PRODUCTS = [
        [
            'name' => 'Bio auzu pārslas 500 g',
            'description' => 'Pilngraudu auzu pārslas bez pievienota cukura ikdienas brokastīm.',
            'price' => [2.49, 3.99],
            'shelf_life' => [180, 365],
        ],
        [
            'name' => 'Griķu milti 1 kg',
            'description' => 'Naturāli griķu milti bez lipekļa maizei, pankūkām un mājās gatavotiem konditorejas izstrādājumiem.',
            'price' => [3.49, 4.99],
            'shelf_life' => [270, 540],
        ],
        [
            'name' => 'Olīveļļa Extra Virgin 500 ml',
            'description' => 'Auksti spiesta pirmās spiedes olīveļļa salātiem un gardēžu ēdieniem.',
            'price' => [8.99, 12.99],
            'shelf_life' => [365, 730],
        ],
        [
            'name' => 'Meža medus 250 g',
            'description' => 'Dabīgs daudzziedu meža medus ar bagātu garšu un aromātu.',
            'price' => [6.49, 9.99],
            'shelf_life' => [730, 1095],
        ],
        [
            'name' => 'Bio jogurts ar zemenēm 400 g',
            'description' => 'Krēmīgs bioloģiskais jogurts ar svaigām zemenēm un bez mākslīgām krāsvielām.',
            'price' => [1.29, 1.99],
            'shelf_life' => [14, 28],
        ],
        [
            'name' => 'Klasiskais siers 300 g',
            'description' => 'Nogatavināts puscietais siers ar maigu, riekstu piegaršu.',
            'price' => [3.99, 5.49],
            'shelf_life' => [30, 90],
        ],
        [
            'name' => 'Ābolu sula 1 l',
            'description' => '100% ābolu sula no Latvijas dārziem, bez pievienota cukura.',
            'price' => [1.99, 2.99],
            'shelf_life' => [180, 365],
        ],
        [
            'name' => 'Zaļā tēja 20 paciņas',
            'description' => 'Maiga zaļā tēja ar citrona garšu ikdienas atslodzei.',
            'price' => [2.49, 3.49],
            'shelf_life' => [365, 730],
        ],
        [
            'name' => 'Maltā kafija 250 g',
            'description' => 'Vidēji grauzdēta 100% arabika kafija ar šokolādes un riekstu notīm.',
            'price' => [4.99, 7.49],
            'shelf_life' => [180, 365],
        ],
        [
            'name' => 'Tumšā šokolāde 70% 100 g',
            'description' => 'Intensīva tumšā šokolāde ar 70% kakao saturu un minimālu cukura daudzumu.',
            'price' => [2.29, 3.29],
            'shelf_life' => [270, 540],
        ],
        [
            'name' => 'Šampūns pret blaugznām 400 ml',
            'description' => 'Maigs ikdienas šampūns, kas palīdz mazināt blaugznas un nomierina jutīgu galvas ādu.',
            'price' => [4.99, 7.99],
            'shelf_life' => [730, 1095],
        ],
        [
            'name' => 'Mitrinošas rokas krēms 75 ml',
            'description' => 'Ātri uzsūcošs rokas krēms ar glicerīnu un alveju sausai ādai.',
            'price' => [3.49, 5.99],
            'shelf_life' => [730, 1095],
        ],
        [
            'name' => 'Zobu pasta ar fluorīdu 75 ml',
            'description' => 'Ikdienas zobu pasta pret kariesu ar patīkamu piparmētru garšu.',
            'price' => [2.49, 3.99],
            'shelf_life' => [730, 1095],
        ],
        [
            'name' => 'Veļas pulveris 3 kg',
            'description' => 'Koncentrēts veļas pulveris krāsainai veļai ar aktīvo skābekli.',
            'price' => [7.99, 11.99],
            'shelf_life' => [730, 1095],
        ],
        [
            'name' => 'Papīra dvieļi 2 gab.',
            'description' => 'Divslāņu absorbējoši papīra dvieļi virtuvei un ikdienas uzkopšanai.',
            'price' => [1.49, 2.49],
            'shelf_life' => [1095, 1825],
        ],
        [
            'name' => 'Tualetes papīrs 8 rullīši',
            'description' => 'Mīksts trīsslāņu tualetes papīrs ar eko sertifikātu.',
            'price' => [3.99, 5.99],
            'shelf_life' => [1095, 1825],
        ],
        [
            'name' => 'Trauku mazgāšanas līdzeklis 500 ml',
            'description' => 'Efektīvs trauku mazgāšanas līdzeklis ar citronu aromātu un maigu formula ādai.',
            'price' => [1.99, 3.49],
            'shelf_life' => [730, 1095],
        ],
        [
            'name' => 'Universālais tīrīšanas līdzeklis 750 ml',
            'description' => 'Daudzfunkcionāls tīrīšanas līdzeklis virtuvei, vannas istabai un grīdām.',
            'price' => [2.99, 4.99],
            'shelf_life' => [730, 1095],
        ],
        [
            'name' => 'Bioloģiskā zobu birste',
            'description' => 'Vidēji cieta zobu birste ar bambusa rokturi un BPA brīvu suku.',
            'price' => [2.99, 4.49],
            'shelf_life' => [1825, 3650],
        ],
        [
            'name' => 'Kokvilnas dvielis 50x90 cm',
            'description' => 'Mīksts 100% kokvilnas dvielis ar augstu mitruma uzsūkšanas spēju.',
            'price' => [4.99, 8.99],
            'shelf_life' => [1825, 3650],
        ],
        [
            'name' => 'Rīsu pārslas 500 g',
            'description' => 'Gaisīgas rīsu pārslas bez glutēna vieglām brokastīm.',
            'price' => [2.29, 3.49],
            'shelf_life' => [365, 730],
        ],
        [
            'name' => 'Makaroni no cieto kviešu miltiem 500 g',
            'description' => 'Elastīgi makaroni no cieto kviešu miltiem, kas saglabā formu gatavošanas laikā.',
            'price' => [1.79, 2.79],
            'shelf_life' => [365, 730],
        ],
        [
            'name' => 'Tomātu pasta 500 g',
            'description' => 'Bieza tomātu pasta no nogatavinātiem tomātiem bez konservantiem.',
            'price' => [2.49, 3.99],
            'shelf_life' => [365, 730],
        ],
        [
            'name' => 'Konservēti zaļie zirnīši 400 g',
            'description' => 'Maigi konservēti zaļie zirnīši salātiem un karstajiem ēdieniem.',
            'price' => [1.29, 1.99],
            'shelf_life' => [730, 1095],
        ],
        [
            'name' => 'Kokosriekstu eļļa 250 ml',
            'description' => 'Nerafinēta kokosriekstu eļļa ar dabīgu aromātu ceptiem un saldajiem ēdieniem.',
            'price' => [5.99, 8.99],
            'shelf_life' => [730, 1095],
        ],
        [
            'name' => 'Mandeles 200 g',
            'description' => 'Veselas mandeles ar bagātu riekstu garšu un augstu olbaltumvielu saturu.',
            'price' => [4.99, 7.49],
            'shelf_life' => [180, 365],
        ],
        [
            'name' => 'Žāvētas aprikozes 250 g',
            'description' => 'Mīkstas žāvētas aprikozes bez pievienota cukura uzkodām un desertiem.',
            'price' => [3.49, 5.49],
            'shelf_life' => [180, 365],
        ],
        [
            'name' => 'Granola ar ogām 400 g',
            'description' => 'Kraukšķīga granola ar auzām, medu un žāvētām ogām brokastīm.',
            'price' => [3.99, 5.99],
            'shelf_life' => [180, 365],
        ],
        [
            'name' => 'Proteīna batoniņš kakao 60 g',
            'description' => 'Proteīna batoniņš ar kakao un 20 g olbaltumvielu uzkodai pēc treniņa.',
            'price' => [1.99, 2.99],
            'shelf_life' => [180, 365],
        ],
        [
            'name' => 'Enerģijas dzēriens 500 ml',
            'description' => 'Atspirdzinošs enerģijas dzēriens ar kofeīnu un B grupas vitamīniem.',
            'price' => [1.49, 2.49],
            'shelf_life' => [180, 365],
        ],
        [
            'name' => 'Minerālūdens 1.5 l',
            'description' => 'Dabīgs minerālūdens ar līdzsvarotu minerālvielu sastāvu.',
            'price' => [0.79, 1.29],
            'shelf_life' => [365, 730],
        ],
        [
            'name' => 'Augļu sulas dzēriens 1 l',
            'description' => 'Atspirdzinošs augļu sulas dzēriens ar apelsīnu un persiku garšu.',
            'price' => [1.49, 2.29],
            'shelf_life' => [180, 365],
        ],
    ];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $product = $this->randomProduct();
        $status = fake()->randomElement([
            'available',
            'available',
            'available',
            'available',
            'out_of_stock',
            'discontinued',
        ]);

        $quantity = match ($status) {
            'out_of_stock' => 0,
            'discontinued' => fake()->numberBetween(0, 10),
            default => fake()->numberBetween(1, 250),
        };

        $expirationDate = $status === 'discontinued'
            ? fake()->dateTimeBetween('-1 year', '-1 day')
            : fake()->dateTimeBetween('now', '+'.fake()->numberBetween(...$product['shelf_life']).' days');

        return [
            'name' => $product['name'],
            'quantity' => $quantity,
            'description' => $product['description'],
            'expiration_date' => $expirationDate->format('Y-m-d'),
            'status' => $status,
            'price' => fake()->randomFloat(2, ...$product['price']),
        ];
    }

    /**
     * Pick a catalog product, avoiding duplicates until the catalog is exhausted.
     *
     * @return array{
     *     name: string,
     *     description: string,
     *     price: array{0: float, 1: float},
     *     shelf_life: array{0: int, 1: int}
     * }
     */
    private function randomProduct(): array
    {
        try {
            $index = fake()->unique()->numberBetween(0, count(self::PRODUCTS) - 1);

            return self::PRODUCTS[$index];
        } catch (OverflowException) {
            return fake()->randomElement(self::PRODUCTS);
        }
    }
}
