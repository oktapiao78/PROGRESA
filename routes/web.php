<?php

use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return view('login');
});

Route::post('/login', function () {
    return redirect('/mahasiswa');
});


Route::get('/mahasiswa', function () {
    return view('dashboard.mahasiswa');
});
Route::get('/mahasiswa/bab', function () {
    return view('dashboard.isi_skripsi');
});