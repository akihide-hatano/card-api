<?php
    use Illuminate\Support\Facades\Route;
    use App\Http\Controllers\CardController;

    Route::prefix('v1')->group(function () {
    Route::apiResource('cards', CardController::class);
    Route::post('cards/{card}/archive', [CardController::class, 'archive'])
        ->name('cards.archive');
    });
?>