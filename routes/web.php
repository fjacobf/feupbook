<?php

use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\PostController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\CardController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CommentController;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;

// Chats
use App\Http\Controllers\GroupChatController;
use App\Http\Controllers\MessageController;

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

// Posts
Route::controller(PostController::class)->group(function () {
    Route::get('/home/forYou', 'forYou')->name('forYou');
    Route::get('/home', 'list')->name('home');
    Route::get('/post/create', 'create')->name('post.create');
    Route::post('/post/create', 'store')->name('post.store');
    Route::get('/post/bookmarks', 'listBookmarks')->name('post.bookmarks');
    Route::get('/post/{id}', 'show')->name('post.show');
    Route::get('/post/{id}/edit', 'edit')->name('post.edit');
    Route::put('/post/{id}/edit', 'update')->name('post.update');   
    Route::delete('/post/{id}/delete', 'delete')->name('post.delete');
    Route::post('/post/{id}/like', 'like')->name('post.like');
    Route::delete('/post/{id}/dislike', 'dislike')->name('post.dislike');
    Route::post('/post/{id}/bookmark', 'bookmark')->name('post.bookmark');
    Route::delete('/post/{id}/unbookmark', 'unbookmark')->name('post.unbookmark');
});

// Comments
Route::controller(CommentController::class)->group(function () {
    Route::post('/comment/create', 'store')->name('comment.store');
    Route::delete('/comment/{id}/delete', 'delete')->name('comment.delete');
    Route::post('/comment/{id}/like', 'like')->name('comment.like');
    Route::post('/comment/{id}/dislike', 'dislike')->name('comment.dislike');
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
    Route::get('/user/{id}', 'show')->name('user.profile');
    Route::post('/user/{id}/follow', 'follow')->name('user.follow');
    Route::post('/user/{id}/unfollow', 'unfollow')->name('user.unfollow');
    Route::get('/user/{id}/followers', 'showFollowerPage')->name('user.followers');
    Route::get('/user/{id}/following', 'showFollowingPage')->name('user.following');
    Route::get('/user/{id}/edit', 'showEditPage')->name('user.showEditPage');
    Route::put('/user/{id}/delete', 'deleteProfile')->name('user.deleteProfile');
    Route::put('/user/{id}/update', 'updateProfile')->name('user.updateProfile');
    Route::post('/user/{id}/removefollower', 'removeFollower')->name('user.removeFollower');
    Route::put('/user/{id}/password', 'updatePassword')->name('user.updatePassword');
});

// Search
Route::controller(SearchController::class)->group(function () {
    Route::get('/search','show')->name('search.show');
    Route::get('/api/user_json', 'search_json')->name('search_json.api');
    Route::get('/api/user', 'search')->name('search.api');
});

// Admin Management
Route::controller(AdminController::class)->group(function () {
    Route::get('/admin/user/{id}/edit', 'showUserManagement')->name('admin.manageUser');
    Route::post('/admin/user/{id}/edit', 'updateUser')->name('admin.updateUser');
    Route::post('/admin/user/{id}/delete', 'deleteUser')->name('admin.deleteUser');
    Route::put('/admin/user/{id}/restore', 'restoreUser')->name('admin.restoreUser');
});

// Chats
// Group Chat Routes
Route::get('/group-chats', [GroupChatController::class, 'index']);
Route::get('/group-chats/{groupChat}', [GroupChatController::class, 'show']);
Route::get('/group-chats/{groupChat}/edit', [GroupChatController::class, 'edit'])->name('group-chats.edit');
Route::post('/group-chats/{groupChat}/send-message', [GroupChatController::class, 'sendMessage'])->name('group-chats.sendMessage');
Route::get('/group-chats/{groupChat}/messages', [GroupChatController::class, 'getMessages'])->name('group-chats.getMessages');
Route::post('/api/group-chats/create', [GroupChatController::class, 'create'])->name('group-chats.create.api');
Route::post('/api/group-chats/{groupChat}/add-member', [GroupChatController::class, 'addMember'])->name('group-chats.addMember.api');
Route::post('/api/group-chats/{groupChat}/remove-member', [GroupChatController::class, 'removeMember'])->name('group-chats.removeMember.api');
Route::post('/api/group-chats/{groupChat}/update', [GroupChatController::class, 'update'])->name('group-chats.update.api');
Route::post('/api/group-chats/{groupChat}/accept-invite', [GroupChatController::class, 'acceptInvite'])->name('group-chats.acceptInvite.api');
Route::post('/api/group-chats/{groupChat}/reject-invite', [GroupChatController::class, 'rejectInvite'])->name('group-chats.rejectInvite.api');
Route::delete('/api/group-chats/{groupChat}/delete', [GroupChatController::class, 'delete'])->name('group-chats.delete.api');
