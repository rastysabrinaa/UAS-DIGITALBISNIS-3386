<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

putenv('CLOUDINARY_URL=cloudinary://272131663378165:Yc8ym5yu-oAqxpw53MdfOaS8PmE@y2y9yjzp');
file_put_contents('test.jpg', 'fake image content');

try {
    $path = \Illuminate\Support\Facades\Storage::disk('cloudinary')->putFile('posters', new \Illuminate\Http\File('test.jpg'));
    echo "SUCCESS PATH: " . $path . "\n";
    echo "SUCCESS URL: " . \Illuminate\Support\Facades\Storage::disk('cloudinary')->url($path) . "\n";
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
