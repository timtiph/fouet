<?php

declare(strict_types=1);

use App\Controllers\AuthController;
use App\Controllers\ContactController;
use App\Controllers\HomeController;
use App\Controllers\RecetteController;
use Tmoi\Foundation\Router\Route;

return [

    // Authentification 

    'register.form' => Route::get('/inscription', [AuthController::class, 'registerForm']),
    'register.request' => Route::post('/inscription', [AuthController::class, 'register']),
    'login.form' => Route::get('/connexion', [AuthController::class, 'loginForm']),
    'login.request' => Route::post('/connexion', [AuthController::class, 'login']),
    'logout' => Route::post('/deconnexion', [AuthController::class, 'logout']),

    // Espace membre

    'home' => Route::get('/compte', [HomeController::class, 'index']),
    'home.updateName' => Route::patch('/compte', [HomeController::class, 'updateName']),
    'home.updateEmail' => Route::patch('/compte/email', [HomeController::class, 'updateEmail']),
    'home.updatePassword' => Route::patch('/compte/password', [HomeController::class, 'updatePassword']),
    'home.delete' => Route::delete('/compte', [HomeController::class, 'delete']),

    // Cuisiniers
    'cuisiniers.showCook' => Route::get('/cuisinier/{slug}',[RecetteController::class, 'showCook']),

    // Contact
    'contact' => Route::get('/contact', [ContactController::class, 'contact']),
    
    
    'index' => Route::get('/', [RecetteController::class, 'index']),
    
    
    // Recette
    
    'recettes.create' => Route::get('/recettes/creer', [RecetteController::class, 'create']),
    'recettes.store' => Route::post('/recettes/creer', [RecetteController::class, 'store']),
    'recettes.show' => Route::get('/recettes/{slug}', [RecetteController::class, 'show']),
    'recettes.shows' => Route::get('/recettes', [RecetteController::class, 'shows']),
    // 'recettes.comment' => Route::post('/recettes/{slug}', [RecetteController::class, 'comment']),
    'recettes.delete' => Route::delete('/recettes/{slug}', [RecetteController::class, 'delete']),
    
    'recettes.edit' => Route::get('/recettes/{slug}/modifier', [RecetteController::class, 'edit']),
    'recettes.update' => Route::patch('/recettes/{slug}/modifier', [RecetteController::class, 'update']),
    
];
