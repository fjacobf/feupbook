<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\PostController;
use App\Http\Controllers\CardController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CommentController;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Home
Route::get('/', function () {
    return view('welcome');
})->middleware('guest')->name('welcome');


Route::view('/about', 'pages.about')->name('about');
Route::view('/help', 'pages.help')->name('help');
Route::view('/faq', 'pages.faq')->name('faq');
Route::view('/contacts', 'pages.contacts')->name('contacts');


Route::controller(PostController::class)->group(function () {
    Route::get('/home/forYou', 'forYou')->name('forYou');
    Route::get('/home', 'list')->name('home');
    Route::get('/post/create', 'create')->name('createPost');
    Route::post('/post/create', 'store')->name('storePost');
    Route::get('/post/{id}', 'show')->name('showPost');
    Route::get('/post/{id}/edit', 'edit')->name('editPost');
    Route::put('/post/{id}/edit', 'update')->name('updatePost');   
    Route::delete('/post/{id}/delete', 'delete')->name('deletePost');
});

Route::controller(CommentController::class)->group(function () {
    Route::post('/comment', 'store')->name('storeComment');
    Route::get('/comment/delete', 'delete')->name('deleteComment');
});

// Authentication
Route::controller(LoginController::class)->group(function () {
    Route::get('/login', 'showLoginForm')->name('login');
    Route::post('/login', 'authenticate');
    Route::get('/logout', 'logout')->name('logout');
});

Route::controller(RegisterController::class)->group(function () {
    Route::get('/register', 'showRegistrationForm')->name('register');
    Route::post('/register', 'register');
});

// User
Route::controller(UserController::class)->group(function () {
    Route::get('/user/{id}', [UserController::class, 'show'])->name('user.profile');
    Route::post('/user/{id}/follow', [UserController::class, 'follow'])->name('user.follow');
    Route::post('/user/{id}/unfollow', [UserController::class, 'unfollow'])->name('user.unfollow');
});