<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Deal;

echo "=== UNIQUE DEAL STATUSES ===\n";
$statuses = Deal::select('status')->distinct()->get()->pluck('status')->toArray();
print_r($statuses);

echo "\n=== ALL DEALS WITH STATUSES ===\n";
foreach (Deal::all() as $d) {
    echo "ID: {$d->id} | Status: {$d->status} | Deal Amount: {$d->deal_amount}\n";
}
