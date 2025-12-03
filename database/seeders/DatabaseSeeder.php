<?php


namespace Database\Seeders;


use App\Models\User;

use App\Models\Product;

use Illuminate\Support\Facades\Hash;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use Illuminate\Database\Seeder;


class DatabaseSeeder extends Seeder
{
    
    use WithoutModelEvents;

    
    public function run(): void
    {
        
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'is_admin' => true,
        ]);

        
        User::create([
            'name' => 'Customer User',
            'email' => 'customer@example.com',
            'password' => Hash::make('password'),
            'is_admin' => false,
        ]);

        
        Product::create([
            'name' => 'Wooden Chair',
            'description' => 'Simple wooden chair for home or office.',
            'price' => 49.99,
            'stock' => 10,
            'image_url' => null,
        ]);

        
        Product::create([
            'name' => 'Desk Lamp',
            'description' => 'LED desk lamp, perfect for study or work.',
            'price' => 19.99,
            'stock' => 25,
            'image_url' => null,
        ]);
    }
}

