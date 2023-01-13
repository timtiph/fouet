<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Cuisinier;
use Cocur\Slugify\Slugify;
use Tmoi\Foundation\AbstractController;
use Tmoi\Foundation\Authentication as Auth;
use Tmoi\Foundation\Session;
use Tmoi\Foundation\Validator;
use Tmoi\Foundation\View;

class AuthController extends AbstractController
{
    public function registerForm(): void
    {
        if (Auth::check()) {
            $this->redirect('home');
        }
        View::render('auth.register');
    }

    public function register(): void
    {
        if (Auth::check()) {
            $this->redirect('home');
        }

        $validator = Validator::get($_POST);
        $validator->mapFieldsRules([
            'name' => ['required', ['lengthMin', 5]],
            'email' => ['required', 'email', ['unique', 'email', 'cuisiniers']],
            'password' => ['required', ['lengthMin', 8], ['equals', 'password_confirmation']],
            'file' => ['required_file', 'image'],
        ]);

        if (!$validator->validate()) {
            Session::addFlash(Session::ERRORS, array_column($validator->errors(), 0));
            Session::addFlash(Session::OLD, $_POST);
            $this->redirect('register.form');
        }

        $slug = $this->slugify($_POST['name']);
        $ext = pathinfo(
            $_FILES['file']['name'],
            PATHINFO_EXTENSION
        );
        $filename = sprintf('%s.%s',$slug, $ext);

        if (!move_uploaded_file(
            $_FILES['file']['tmp_name'],
            sprintf('%s/public/img/%s', ROOT, $filename)
        )) {
            Session::addFlash(Session::ERRORS, ['file' => [
                'Il y a eu un problème lors de l\'envoi. Retentez votre chance !',
            ]]);
            Session::addFlash(Session::OLD, $_POST);
            $this->redirect('register.form');
        }

        $cuisinier = Cuisinier::create([
            'name' => $_POST['name'],
            'slug' => $slug,
            'email' => $_POST['email'],
            'password' => password_hash($_POST['password'], PASSWORD_DEFAULT),
            'img' => $filename,
        ]);



        Auth::authenticate($cuisinier->id);
        $this->redirect('home');
    }

    public function loginForm(): void
    {
        if (Auth::check()) {
            $this->redirect('home');
        }

        View::render('auth.login');
    }



    public function login(): void
    {
        if (Auth::check()) {
            $this->redirect('home');
        }

        $validator = Validator::get($_POST);
        $validator->mapFieldsRules([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if ($validator->validate() && Auth::verify($_POST['email'], $_POST['password'])) {
            $cuisinier = Cuisinier::where('email', $_POST['email'])->first();
            Auth::authenticate($cuisinier->id);
            $this->redirect('home');
        }

        Session::addFlash(Session::ERRORS, ['Identifiants erronés']);
        Session::addFlash(Session::OLD, $_POST);
        $this->redirect('login.form');
    }

    public function logout(): void
    {
        if (Auth::check()) {
            Auth::logout();
        }

        $this->redirect('login.form');
    }


    protected function slugify(string $name): string
    {
        $slugify = new Slugify();
        $slug = $slugify->slugify($name);
        $i = 1;
        $unique_slug = $slug;
        while (Cuisinier::where('slug', $unique_slug)->exists()) {
            $unique_slug = sprintf('%s-%s', $slug, $i++);
        }
        return $unique_slug;
    }



}
