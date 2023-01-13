<?php

declare(strict_types=1);

namespace Tmoi\Foundation\Exceptions;

use Tmoi\Foundation\View;

class HttpException extends \Exception
{
   
    public static function render(int $httpCode = 404, string $message = 'Page non trouvée'): void
    {
        http_response_code($httpCode);
        echo "<h1>Erreur $httpCode : $message</h1>";
        View::render('errors.default', [
            'httpCode' => $httpCode, 
            'message' => $message,
        ]);
        die;
    }
}
