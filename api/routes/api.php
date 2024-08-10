<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API;

Route::get('/balance/{wallet}', [ API::class, 'Balance'      ]); // complete
Route::get('/transaction/{id}', [ API::class, 'Transaction'  ]); // complete
Route::any('/transactions',     [ API::class, 'Transactions' ]); // complete
Route::post('/transfer',        [ API::class, 'Transfer'     ]); // complete
Route::post('/verify',          [ API::class, 'Verify'       ]); // complete
Route::post('/generate',        [ API::class, 'Generate'     ]); // complete
Route::get('/{any}',            [ API::class, 'Invalid'      ])->where([ 'any' => '.*' ]); // complete
