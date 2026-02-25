<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        fake()->seed(20260225);

        $users = collect([
            ['name' => 'Алиса Иванова', 'email' => 'alice@example.com'],
            ['name' => 'Борис Смирнов', 'email' => 'bob@example.com'],
            ['name' => 'Карина Давыдова', 'email' => 'carol@example.com'],
        ])->map(function (array $userData) {
            /** @var User $user */
            $user = User::query()->updateOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => Hash::make('password'),
                ]
            );

            $user->profile()->updateOrCreate([
                'user_id' => $user->id,
            ], [
                'phone' => fake()->e164PhoneNumber(),
                'address' => fake()->streetAddress().', '.fake()->city(),
            ]);

            return $user;
        });

        $categories = collect(['Электроника', 'Дом и кухня', 'Спорт'])->map(function (string $name) {
            return Category::query()->firstOrCreate(['name' => $name]);
        });

        $productNames = [
            'Беспроводная мышь',
            'Механическая клавиатура',
            'Наушники с шумоподавлением',
            'Зарядка USB-C',
            'Кофемолка',
            'Сковорода из нержавеющей стали',
            'Очиститель воздуха',
            'Коврик для йоги',
            'Набор гантелей',
            'Бутылка для бега',
        ];

        $products = collect($productNames)->map(function (string $name) use ($categories) {
            return Product::query()->updateOrCreate(
                ['name' => $name],
                [
                    'category_id' => $categories->random()->id,
                    'price' => fake()->randomFloat(2, 9, 299),
                ]
            );
        });

        $ordersToCreate = max(0, 3 - Order::query()->count());

        for ($i = 0; $i < $ordersToCreate; $i++) {
            /** @var Order $order */
            $order = Order::query()->create([
                'user_id' => $users[$i % $users->count()]->id,
                'status' => fake()->randomElement(['new', 'processing']),
                'total' => 0,
            ]);

            $selectedProducts = $products->random(fake()->numberBetween(2, 4));
            $total = 0;

            foreach ($selectedProducts as $product) {
                $quantity = fake()->numberBetween(1, 4);
                $linePrice = (float) $product->price;
                $total += $quantity * $linePrice;

                $order->products()->attach($product->id, [
                    'quantity' => $quantity,
                    'price_at_purchase' => number_format($linePrice, 2, '.', ''),
                ]);
            }

            $order->update([
                'total' => number_format($total, 2, '.', ''),
            ]);
        }
    }
}
