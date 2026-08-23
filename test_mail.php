<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $t = App\Models\Transaction::with('event')->first();
    if (!$t) {
        echo 'No tx';
        exit;
    }
    $mail = new App\Mail\TicketPurchased($t);
    $mail->render();
    echo 'OK1 ';
    
    $mail2 = new App\Mail\EventCertificate($t, 'pdf');
    $mail2->render();
    echo 'OK2';
} catch (\Exception $e) {
    echo $e->getMessage();
}
