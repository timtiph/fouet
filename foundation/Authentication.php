<?php

declare(strict_types=1);

namespace Tmoi\Foundation;

use App\Models\Cuisinier;



class Authentication
{
    protected const SESSION_ID = 'cuisinier_id';

    public static function check(): bool
    {
        return (bool) Session::get(static::SESSION_ID);
    }

    public static function checkIsAdmin(): bool
    {
        return static::check() && static::get()->role === 'admin';
    }

    public static function verify(string $email, string $password): bool
    {
        $cuisinier = Cuisinier::where('email', $email)->first();
        return $cuisinier && password_verify($password, $cuisinier->password);
    }

    public static function authenticate(int $id): void
    {
        Session::add(static::SESSION_ID, $id);
    }

    public static function logout(): void
    {
        Session::remove(static::SESSION_ID);
    }

    public static function id(): ?int
    {
        return Session::get(static::SESSION_ID);
    }

    public static function get(): ?Cuisinier
    {
        return Cuisinier::find(static::id());
    }

}