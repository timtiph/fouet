<?php

declare(strict_types=1);

namespace Tmoi\Foundation;

use Illuminate\Database\Capsule\Manager as Capsule;
use Valitron\Validator as ValitronValidator;

class Validator 
{
    public static function get(array $data): ValitronValidator
    {
        $validator = new ValitronValidator(
            data: $data,
            lang: 'fr' 
        );
        $validator->labels(require ROOT . '/resources/lang/validation.php');
        static::addCustomRules($validator);
        return $validator;
    }

    protected static function addCustomRules(ValitronValidator $validator): void
    {
        // Custom rules here

        $validator->addRule('unique', function (string $field, mixed $value, array $params, array $fields) {
            return !Capsule::table($params[1])->where($params[0], $value)->exists();
        }, '{field} est invalide');

        $validator->addRule('password', function (string $field, mixed $value, array $params, array $fields) {
            $cuisinier = Authentication::get();
            return password_verify($value, $cuisinier->password);
        }, '{field} est erroné');

        $validator->addRule('required_file', function (string $field, mixed $value, array $params, array $fields) {
            return isset($_FILES[$field]) && $_FILES[$field]['error'] === UPLOAD_ERR_OK;
        }, '{field} est obligatoire');

        $validator->addRule('image', function (string $field, mixed $value, array $params, array $fields) {
            if (isset($_FILES[$field]) && $_FILES[$field]['error'] === UPLOAD_ERR_OK) {
                return str_starts_with($_FILES[$field]['type'], 'image/');
            }
            return false;
        }, '{field} doit être une image');

        $validator->addRule('square', function (string $field, mixed $value, array $params, array $fields) {
            if (isset($_FILES[$field]) && $_FILES[$field]['error'] === UPLOAD_ERR_OK) {
                [$width, $height] = getimagesize($_FILES[$field]['tmp_name']);
                return $width === $height;
            }
            return false;
        }, '{field} doit être carré (hauteur = largeur)');


    }
}
