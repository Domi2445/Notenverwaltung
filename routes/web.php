<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Welche Routen die Oberfläche braucht (Name, Methode, URL), steht in
| docs/PLAN.md unter „Routen“.
|
*/

Route::get('/', function () {
    return redirect('/login');
});
