<?php

namespace App\Auth;

use Illuminate\Auth\Passwords\DatabaseTokenRepository;

class CustomPasswordTokenRepository extends DatabaseTokenRepository
{
    public function create($user)
    {
        $correo = $user->getEmailForPasswordReset();
        $this->deleteExisting($user);

        $token = $this->createNewToken();

        $this->getTable()->insert([
            'correo'     => $correo,
            'token'      => $this->hasher->make($token),
            'created_at' => now(),
        ]);

        return $token;
    }

    public function exists($user, $token)
    {
        $record = $this->getTable()
            ->where('correo', $user->getEmailForPasswordReset())
            ->first();

        return $record && !$this->tokenExpired($record->created_at)
            && $this->hasher->check($token, $record->token);
    }

    protected function deleteExisting($user)
    {
        return $this->getTable()
            ->where('correo', $user->getEmailForPasswordReset())
            ->delete();
    }
}
