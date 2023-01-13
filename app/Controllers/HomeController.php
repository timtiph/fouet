<?php

declare(strict_types=1);

namespace App\Controllers;

use Tmoi\Foundation\AbstractController;
use Tmoi\Foundation\Authentication as Auth;
use Tmoi\Foundation\Session;
use Tmoi\Foundation\Validator;
use Tmoi\Foundation\View;;

class HomeController extends AbstractController
{
    public function index(): void
    {
        if (!Auth::check()) {
            $this->redirect('login.form');
        }
        $user = Auth::get();
        View::render('home', [
            'cuisinier' => $user,
        ]);
    }

    public function updateName(): void
    {
        if (!Auth::check()) {
            $this->redirect('login.form');
        }
        $validator = Validator::get($_POST);
        $validator->mapFieldsRules([
            'name' => ['required', ['lengthMin', 5]],
        ]);

        if (!$validator->validate()) {
            Session::addFlash(Session::ERRORS, $validator->errors());
            Session::addFlash(Session::OLD, $_POST);
            $this->redirect('home');
        }

        $user = Auth::get();
        $user->name = $_POST['name'];
        $user->save();

        Session::addFlash(Session::STATUS, 'Votre nom a été mis à jour !');
        $this->redirect('home');
    }

    public function updateEmail(): void
    {
        if (!Auth::check()) {
            $this->redirect('login.form');
        }

        $validator = Validator::get($_POST);
        $validator->mapFieldsRules([
            'email' => ['required', 'email', ['unique', 'email', 'cuisiniers']],
        ]);

        if (!$validator->validate()) {
            Session::addFlash(Session::ERRORS, $validator->errors());
            Session::addFlash(Session::OLD, $_POST);
            $this->redirect('home');
        }

        $cuisinier = Auth::get();
        $cuisinier->email = $_POST['email'];
        $cuisinier->save();

        Session::addFlash(Session::STATUS, 'Votre adresse e-mail a été mise à jour !');
        $this->redirect('home');
    }

    

    public function updatePassword(): void
    {
        if (!Auth::check()) {
            $this->redirect('login.form');
        }

        $validator = Validator::get($_POST);
        $validator->mapFieldsRules([
            'password_old' => ['required', 'password'],
            'password' => ['required', ['lengthMin', 8], ['equals', 'password_confirmation']],
        ]);

        if (!$validator->validate()) {
            Session::addFlash(Session::ERRORS, $validator->errors());
            $this->redirect('home');
        }

        $user = Auth::get();
        $user->password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $user->save();

        Session::addFlash(Session::STATUS, 'Votre mot de passe a été mis à jour !');
        $this->redirect('home');
    }

    public function delete(): void
    {
        if (!Auth::checkIsAdmin()) {
            $this->redirect('login.form');
        }

        $cuisinier = Auth::get();
        $cuisinier->deleteOrFail();

        Auth::logout();   
        Session::remove(Session::STATUS);
        $this->redirect('index');

    }
}
