<?php

namespace App\Filament\Pages\Auth;

use Filament\Forms\Components\Component;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Pages\Auth\Register as BaseRegister;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;

class Register extends BaseRegister
{
    protected function getForms(): array
    {
        return [
            'form' => $this->form(
                $this->makeForm()
                    ->schema([
                        $this->getNameFormComponent(),
                        $this->getEmailFormComponent(),
                        $this->getPasswordFormComponent(),
                        $this->getPasswordConfirmationFormComponent(),
                        $this->getRoleFormComponent(), 
                    ])
                    ->statePath('data'),
            ),
        ];
    }
 
    protected function getRoleFormComponent(): Component
    {
        return Select::make('role')
            ->options([
                'owner' => 'Owner',
                'user' => 'Tenant',
            ])
            ->default('user')
            ->required();
    }

    protected function handleRegistration(array $data): Model
    {
        $user = parent::handleRegistration($data);
        
        // Set user as inactive by default
        $user->isActive = "inactive";
        $user->save();

        // Show notification to user
        Notification::make()
            ->title('Registration successful')
            ->body('Your account has been created. Please wait for admin approval ang pay for the property registration fee.')
            ->success()
            ->send();

        // Redirect to login page instead of dashboard
        $this->redirect(route('filament.admin.auth.login'));

        return $user;
    }
}