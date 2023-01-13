<?php

declare(strict_types=1);

namespace App\Controllers;

// use App\Models\Comment;

use App\Models\Cuisinier;
use App\Models\Recette;
use Tmoi\Foundation\AbstractController;
use Tmoi\Foundation\Authentication as Auth;
use Tmoi\Foundation\Session;
use Cocur\Slugify\Slugify;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Tmoi\Foundation\Exceptions\HttpException;
use Tmoi\Foundation\Validator;
use Tmoi\Foundation\View;

class RecetteController extends AbstractController
{
    public function index(): void
    {
        $recettes = Recette::orderBy('id', 'desc')->get();
        $cuisiniers = Cuisinier::orderBy('name')->get();
        View::render('index', [
            'recettes' => $recettes,
            'cuisiniers' => $cuisiniers,
        ]);
    }

    public function show(string $slug): void
    {

        $recette = Recette::where('slug', $slug)->firstOrFail();

        $cuisinier = Cuisinier::join('recettes', 'recettes.cuisinier_id', '=', 'cuisiniers.id')->where('recettes.slug', '=', $slug)->get();

        View::render('recettes.show', [
            'recette' => $recette,
            'cuisinier' => $cuisinier,
        ]);
    }


    public function shows(): void
    {
        if (!Auth::check()) {
            $this->redirect('login.form');
        }


        $id = Auth::id();
        $recettes = Recette::where('cuisinier_id', $id)->get();

        View::render('recettes.shows', [
            'recettes' => $recettes,
        ]);
    }

    public function showCook(string $slug): void
    {
        $recettes = Recette::select('*', 'recettes.img', 'recettes.slug')->join('cuisiniers', 'recettes.cuisinier_id', '=', 'cuisiniers.id')->where('cuisiniers.slug', '=', $slug)->get();
        $cuisinier = Cuisinier::where('slug', $slug)->firstOrFail();
        
        View::render('cuisiniers.showCook', [
            'recettes' => $recettes,
            'cuisinier' => $cuisinier,
        ]);
    }



    // public function comment(string $slug): void
    // {
    //     if (!Auth::check()) {
    //         $this->redirect('login.form');
    //     }

    //     $recette = Recette::where('slug', $slug)->firstOrFail();

    //     $validator = Validator::get($_POST);
    //     $validator->mapFieldsRules([
    //         'comment' => ['required', ['lengthMin', 3]],
    //     ]);

    //     if (!$validator->validate()) {
    //         Session::addFlash(Session::ERRORS, $validator->errors());
    //         Session::addFlash(Session::OLD, $_POST);
    //         $this->redirect('recettes.show', ['slug' => $slug]);
    //     }

    //     Comment::create([
    //         'body' => $_POST['comment'],
    //         'cuisinier_id' => Auth::id(),
    //         'recette_id' => $recette->id,
    //     ]);

    //     Session::addFlash(Session::STATUS, 'Votre commentaire a été publié !');
    //     $this->redirect('recettes.show', ['slug' => $slug]);
    // }



    public function delete(string $slug)
    {
        if (!Auth::checkIsAdmin()) {
            $this->redirect('login.form');
        }

        $recette = Recette::where('slug', $slug)->firstOrFail();

        unlink(sprintf('%s/public/img/%s', ROOT, $recette->img));
        $recette->delete();

        $this->redirect('index');
    }




    public function create(): void
    {
        if (!Auth::checkIsAdmin()) {
            $this->redirect('login.form');
        }

        View::render('recettes.create');
    }

    public function store(): void
    {
        if (!Auth::checkIsAdmin()) {
            $this->redirect('login.form');
        }

        $validator = Validator::get($_POST + $_FILES);
        $validator->mapFieldsRules([
            'title'         => ['required', ['lengthMin', 3]],
            'difficulty'    => ['required', ['lengthMin', 1]],
            'cout'          => ['required', ['lengthMin', 1]],
            'temps'         => ['required', ['lengthMin', 1]],
            'description'   => ['required', ['lengthMin', 5]],
            'nb_pax'        => ['required', ['lengthMin', 1]],
            'file'          => ['required_file', 'image'],
            'ingredients'   => ['required', ['lengthMin', 1]],
            'etapes'        => ['required', ['lengthMin', 1]],

        ]);

        if (!$validator->validate()) {
            Session::addFlash(Session::ERRORS, $validator->errors());
            Session::addFlash(Session::OLD, $_POST);
            $this->redirect('recettes.create');
        }

        $slug = $this->slugify($_POST['title']);
        $ext = pathinfo(
            $_FILES['file']['name'],
            PATHINFO_EXTENSION
        );
        $filename = sprintf('%s.%s', $slug, $ext);

        if (!move_uploaded_file(
            $_FILES['file']['tmp_name'],
            sprintf('%s/public/img/%s', ROOT, $filename)
        )) {
            Session::addFlash(Session::ERRORS, ['file' => [
                'Il y a eu un problème lors de l\'envoi. Retentez votre chance !',
            ]]);
            Session::addFlash(Session::OLD, $_POST);
            $this->redirect('recettes.create');
        }
        // vérifier ici si Auth id ne fonctionne pas
        $recette = Recette::create([
            'cuisinier_id'          => Auth::id(),
            'title'                 => $_POST['title'],
            'slug'                  => $slug,
            'description'           => $_POST['description'],
            'difficulty'            => $_POST['difficulty'],
            'temps'                 => $_POST['temps'],
            'cout'                  => $_POST['cout'],
            'nb_pax'                => $_POST['nb_pax'],
            'img'                   => $filename,
            'ingredients'           => $_POST['ingredients'],
            'etapes'                => $_POST['etapes'],
        ]);

        Session::addFlash(Session::STATUS, 'Votre recette a été publié !');
        $this->redirect('recettes.show', ['slug' => $recette->slug]);
    }

    public function edit(string $slug): void
    {
        if (!Auth::checkIsAdmin()) {
            $this->redirect('login.form');
        }

        try {
            $recette = Recette::where('slug', $slug)->firstOrFail();
        } catch (ModelNotFoundException) {
            HttpException::render();
        }

        View::render('recettes.edit', [
            'recette' => $recette,
        ]);
    }

    public function update(string $slug): void
    {
        if (!Auth::checkIsAdmin()) {
            $this->redirect('login.form');
        }

        $recette = Recette::where('slug', $slug)->firstOrFail();

        $validator = Validator::get($_POST);
        $validator->mapFieldsRules([
            'title'         => ['required', ['lengthMin', 3]],
            'difficulty'    => ['required', ['lengthMin', 1]],
            'cout'          => ['required', ['lengthMin', 1]],
            'temps'         => ['required', ['lengthMin', 1]],
            'description'   => ['required', ['lengthMin', 5]],
            'nb_pax'        => ['required', ['lengthMin', 1]],
            'ingredients'   => ['required', ['lengthMin', 1]],
            'etapes'        => ['required', ['lengthMin', 1]],
        ]);

        if (!$validator->validate()) {
            Session::addFlash(Session::ERRORS, $validator->errors());
            Session::addFlash(Session::OLD, $_POST);
            $this->redirect('recettes.edit', ['slug' => $recette->slug]);
        }

        $recette->fill([
            'title'                 => $_POST['title'],
            'difficulty'            => $_POST['difficulty'],
            'cout'                  => $_POST['cout'],
            'temps'                 => $_POST['temps'],
            'description'           => $_POST['description'],
            'nb_pax'                => $_POST['nb_pax'],
            'ingredients'           => $_POST['ingredients'],
            'etapes'                => $_POST['etapes'],
        ]);
        $recette->save();

        Session::addFlash(Session::STATUS, 'Votre recette a été mis à jour !');
        $this->redirect('recettes.show', ['slug' => $recette->slug]);
    }


    protected function slugify(string $title): string
    {
        $slugify = new Slugify();
        $slug = $slugify->slugify($title);
        $i = 1;
        $unique_slug = $slug;
        while (Recette::where('slug', $unique_slug)->exists()) {
            $unique_slug = sprintf('%s-%s', $slug, $i++);
        }
        return $unique_slug;
    }
}
