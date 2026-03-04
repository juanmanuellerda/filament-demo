<?php

namespace App\Filament\Pages\Auth;

use App\Enums\TypeEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;

class Register extends \Filament\Auth\Pages\Register
{
    public function getTitle(): string | Htmlable
    {
        return __('Crear cuenta');
    }

    public function getHeading(): string | Htmlable | null
    {
        return __('Crear una cuenta nueva');
    }

    protected function getNameFormComponent(): Component
    {
        return parent::getNameFormComponent()
            ->label(__('Nombre completo'));
    }

    protected function getEmailFormComponent(): Component
    {
        return parent::getEmailFormComponent()
            ->label(__('Correo electrónico'));
    }

    protected function getPasswordFormComponent(): Component
    {
        return parent::getPasswordFormComponent()
            ->label(__('Contraseña'));
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                $this->getNameFormComponent(),
                $this->getEmailFormComponent(),
                $this->getTypeFormComponent(),
                $this->getPasswordFormComponent(),
                $this->getPasswordConfirmationFormComponent(),
            ]);
    }

    protected function getTypeFormComponent(): Component
    {
        return Select::make('type')
            ->label(__('Tipo'))
            ->options(TypeEnum::class)
            ->required()
            ->native(false);
    }

    protected function getPasswordConfirmationFormComponent(): Component
    {
        return parent::getPasswordConfirmationFormComponent()
            ->label(__('Confirmar contraseña'));
    }

    public function getRegisterFormAction(): Action
    {
        return parent::getRegisterFormAction()
            ->label(__('Crear cuenta'));
    }

    public function loginAction(): Action
    {
        return parent::loginAction()
            ->label(__('Iniciar sesión'));
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function handleRegistration(array $data): Model
    {
        $userModel = $this->getUserModel();

        /** @var Model $user */
        $user = new $userModel;

        $user->forceFill([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'type' => $data['type'],
            'admin' => false,
        ]);

        $user->save();

        return $user;
    }
}
