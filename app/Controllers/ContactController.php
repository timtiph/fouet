<?php

declare(strict_types=1);

namespace App\Controllers;

use Tmoi\Foundation\AbstractController;
use Tmoi\Foundation\View;

class ContactController extends AbstractController{

    public function contact(): void
    {
        View::render('contact');
    }
}