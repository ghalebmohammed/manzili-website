<?php

$migrationsDir = __DIR__ . '/database/migrations/';

$files = scandir($migrationsDir);

$schemas = [
    'categories' => "
            \$table->id();
            \$table->string('name_ar');
            \$table->string('name_en');
            \$table->string('slug')->unique();
            \$table->string('icon')->nullable();
            \$table->boolean('is_active')->default(true);
            \$table->timestamps();
    ",
    'stores' => "
            \$table->id();
            \$table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            \$table->string('name');
            \$table->string('store_name')->nullable();
            \$table->string('store_type')->nullable();
            \$table->string('slug')->unique();
            \$table->text('description')->nullable();
            \$table->string('logo')->nullable();
            \$table->string('cover')->nullable();
            \$table->string('cover_image')->nullable();
            \$table->text('contact_info')->nullable();
            \$table->enum('kyc_status', ['pending', 'approved', 'rejected'])->default('pending');
            \$table->enum('status', ['pending', 'active', 'inactive', 'suspended'])->default('pending');
            \$table->integer('views')->default(0);
            \$table->timestamp('verified_at')->nullable();
            \$table->timestamps();
    ",
    'products' => "
            \$table->id();
            \$table->foreignId('store_id')->constrained('stores')->onDelete('cascade');
            \$table->foreignId('category_id')->nullable()->constrained('categories')->onDelete('set null');
            \$table->string('name');
            \$table->string('slug')->unique();
            \$table->text('description')->nullable();
            \$table->decimal('price', 10, 2);
            \$table->json('images')->nullable();
            \$table->enum('status', ['active', 'hidden', 'available', 'unavailable', 'deleted'])->default('active');
            \$table->integer('views')->default(0);
            \$table->timestamps();
    ",
    'favorites' => "
            \$table->id();
            \$table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            \$table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            \$table->timestamps();
    ",
    'followers' => "
            \$table->id();
            \$table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            \$table->foreignId('store_id')->constrained('stores')->onDelete('cascade');
            \$table->timestamps();
    ",
    'sales_logs' => "
            \$table->id();
            \$table->foreignId('store_id')->constrained('stores')->onDelete('cascade');
            \$table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            \$table->foreignId('customer_id')->nullable()->constrained('users')->onDelete('set null');
            \$table->string('customer_contact')->nullable();
            \$table->enum('status', ['pending', 'confirmed', 'cancelled'])->default('pending');
            \$table->timestamps();
    ",
    'product_reviews' => "
            \$table->id();
            \$table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            \$table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            \$table->integer('rating');
            \$table->text('comment')->nullable();
            \$table->timestamps();
    ",
    'store_reviews' => "
            \$table->id();
            \$table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            \$table->foreignId('store_id')->constrained('stores')->onDelete('cascade');
            \$table->integer('rating');
            \$table->text('comment')->nullable();
            \$table->timestamps();
    ",
    'ads' => "
            \$table->id();
            \$table->foreignId('store_id')->nullable()->constrained('stores')->onDelete('cascade');
            \$table->string('banner_image');
            \$table->string('link')->nullable();
            \$table->enum('status', ['pending', 'active', 'expired'])->default('pending');
            \$table->timestamp('start_date')->nullable();
            \$table->timestamp('end_date')->nullable();
            \$table->timestamps();
    ",
    'notifications' => "
            \$table->uuid('id')->primary();
            \$table->string('type');
            \$table->morphs('notifiable');
            \$table->text('data');
            \$table->timestamp('read_at')->nullable();
            \$table->timestamps();
    ",
    'settings' => "
            \$table->id();
            \$table->string('key')->unique();
            \$table->text('value')->nullable();
            \$table->string('type')->default('string');
            \$table->timestamps();
    "
];

foreach ($files as $file) {
    if ($file == '.' || $file == '..')
        continue;

    foreach ($schemas as $table => $schema) {
        if (strpos($file, 'create_' . $table . '_table') !== false) {
            $content = file_get_contents($migrationsDir . $file);
            $newContent = preg_replace('/Schema::create\(\'' . $table . '\', function \(Blueprint \$table\) \{(.*?)\}\);/s', "Schema::create('$table', function (Blueprint \$table) { $schema });", $content);
            file_put_contents($migrationsDir . $file, $newContent);
            echo "Updated $file\n";
        }
    }
}

// Updating users table manually
$usersFile = $migrationsDir . '0001_01_01_000000_create_users_table.php';
if (file_exists($usersFile)) {
    $content = file_get_contents($usersFile);
    if (strpos($content, 'role') === false) {
        $userSchema = "
            \$table->id();
            \$table->string('name');
            \$table->string('email')->unique();
            \$table->string('phone')->nullable();
            \$table->enum('role', ['customer', 'seller'])->default('customer');
            \$table->timestamp('email_verified_at')->nullable();
            \$table->string('password');
            \$table->rememberToken();
            \$table->timestamps();
        ";
        $newContent = preg_replace('/Schema::create\(\'users\', function \(Blueprint \$table\) \{(.*?)\}\);/s', "Schema::create('users', function (Blueprint \$table) { $userSchema });", $content);
        file_put_contents($usersFile, $newContent);
        echo "Updated users table\n";
    }
}
