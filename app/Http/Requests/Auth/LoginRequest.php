<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'identificacao' => ['required', 'string'],
            'password' => ['required', 'string'],
        ];
    }

    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $identificacao = $this->input('identificacao');
        $password = $this->input('password');
        $remember = $this->boolean('remember');

        $isEmail = filter_var($identificacao, FILTER_VALIDATE_EMAIL);
        
        $credentialsAdmin = [];
        $credentialsDoador = [];
        $credentialsHemocentro = [];

        // Se for e-mail, pode ser qualquer um dos 3 tipos de usuário
        if ($isEmail) {
            $credentialsAdmin = ['email' => $identificacao, 'password' => $password];
            $credentialsDoador = ['email' => $identificacao, 'password' => $password];
            $credentialsHemocentro = ['email' => $identificacao, 'password' => $password];
        } else {
            // sabemos que é Doador ou Hemocentro pelo tamanho
            $documentoLimpo = preg_replace('/[^0-9]/', '', $identificacao);
            
            if (strlen($documentoLimpo) <= 11) {
                $credentialsDoador = ['cpf' => $documentoLimpo, 'password' => $password];
            } else {
                $credentialsHemocentro = ['cnpj' => $documentoLimpo, 'password' => $password];
            }
        }

        // Administrador
        if (!empty($credentialsAdmin) && Auth::guard('web')->attempt($credentialsAdmin, $remember)) {
            RateLimiter::clear($this->throttleKey());
            return;
        }

        // Doador
        if (!empty($credentialsDoador) && Auth::guard('doador')->attempt($credentialsDoador, $remember)) {
            RateLimiter::clear($this->throttleKey());
            return;
        }

        // Hemocentro
        if (!empty($credentialsHemocentro) && Auth::guard('hemocentro')->attempt($credentialsHemocentro, $remember)) {
            RateLimiter::clear($this->throttleKey());
            return;
        }

        RateLimiter::hit($this->throttleKey());

        throw ValidationException::withMessages([
            'identificacao' => trans('auth.failed'),
        ]);
    }

    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));
        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'identificacao' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->input('identificacao')).'|'.$this->ip());
    }
}
