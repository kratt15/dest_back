<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Item;
use App\Models\User;
use App\Models\Order;
use App\Models\Store;
use App\Enums\RoleEnum;
use App\Models\Manager;
use App\Models\Payment;
use App\Models\Calendar;
use App\Models\Category;
use App\Models\Purchase;
use App\Models\Customer;
use App\Models\Provider;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
            $user= User::factory(5)->create();

            // foreach ($users as $user) {
            //     $user->assignRole('user');
            // }
            //
            //  Providers::factory(15)->create();
            // Customers::factory(15)->create();
            // Manager::factory(10)->create();
        //    Category::factory(10)->create();
        //    Provider::factory(10)->create();
        //     Item::factory(10)->create();





        // $adminrole = Role::firstWhere('name', RoleEnum::ADMIN->value);

        // User::factory(10)->create()->each(function (User $user) {
        //     $user->assignRole($adminrole);
        // });

        // User::factory()->create(
        //     [
        //         'name' => 'user ad',
        //         'email' => 'user@xpl.com',
        //     ]
        // )->assignRole(Role::firstWhere('name', RoleEnum::ADMIN->value));


        // $standartRole = Role::firstWhere('name', RoleEnum::STANDART->value);

        // User::factory()->has(

        // )





        $Customers = Customer::factory(10)->create();

        $Categories = Category::factory(10)->create();

        $Provider = Provider::factory(10)->create();

        $Stores = Store::factory(10)->create();

        // $Managers = Manager::factory(10)->create();

        $Calendars = Calendar::factory(10)->create();


        // Relation

        // relation de 1 a Plusieurs entre article et cat , article et fournisseur

        $items = Item::factory(10)->create()->each(function ($item) use ($Categories, $Provider) {
            $category = $Categories->random();
            $provider = $Provider->random();

            $item->category_id = $category->id;
            $item->provider_id = $provider->id;
            $item->save();
        });

        // relation DF entre  commandes et Magasins

        $Orders = Order::factory(10)->create()->each(function ($order) use ($Stores) {
            $order->store_id = $Stores->random()->id;
            $order->save();
        });

        // relation 1,n entre commandes et articles

        $Orders->each(function ($order) use ($items) {
            $quantity = rand(1, 100);
            $order->items()->attach($items->random(), ['quantity' => $quantity]);
        });

        // relation DF entre Achat et client , Achat et Magasins

        $Purchases = Purchase::factory(10)->create()->each(function ($purchase) use ($Customers, $Stores) {
            $customer = $Customers->random();
            $store = $Stores->random();

            $purchase->customer_id = $customer->id;
            $purchase->store_id = $store->id;
            $purchase->save();
        });


        //relation DF entre Payement et client, Payement et  Achat

        $Payments = Payment::factory(10)->create()->each(function ($payment) use ($Purchases, $Customers) {
            $purchase = $Purchases->random();
            // $customer = $Customers->random();

            // $payment->customer_id = $customer->id;
            $payment->purchase_id = $purchase->id;
            $payment->save();
        });

        //relation  1,n entre achat et article

        $Purchases->each(function ($purchase) use ($items) {
            $quantity = rand(1, 100);
            $purchase->items()->attach($items->random(), ['quantity' => $quantity]);
        });

        //relation  1,n entre article et magasin

        $Stores->each(function ($store) use ($items) {
            $quantity = rand(1, 100);
            $store->items_stock()->attach($items->random(), ['quantity' => $quantity]);
        });



        // $Calendars->each(function ($calendar) use ($Managers,$Stores) {

        //     $calendar->managers_manage()->attach($Managers->random());
        //     $calendar->stores_manage()->attach($Stores->random());
        // });

        // // $Calendars->each(function ($calendar) use ($Stores) {

        // //     $calendar->stores_manage()->attach($Stores->random());

        // // });










    }
}
