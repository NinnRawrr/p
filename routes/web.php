<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('bernew');
});

Route::get('/profilsekolah', function () {
    return view('profil');
});

Route::get('/pengumuman', function () {
    return view('pengumuman');
});
