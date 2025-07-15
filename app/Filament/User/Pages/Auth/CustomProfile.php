<?php

namespace App\Filament\User\Pages\Auth;

use Filament\Forms;
use Filament\Pages\Auth\EditProfile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Filament\Notifications\Notification;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;

class CustomProfile extends EditProfile
{
    protected static ?string $title = 'My Custom Profile Page';

    public static function getRouteName(?string $panel = null): string
    {
        return 'filament.user.auth.profile';
    }    

    public static function getRoutePath(): string
    {
        return 'profile';
    }

    protected function getFormSchema(): array
    
    {
        dd(static::class); 
        return [
            Section::make('Profile Information')
                ->schema([
                    Grid::make(2)->schema([
                        TextInput::make('name')
                            ->label('Name')
                            ->maxLength(255),

                        TextInput::make('email')
                            ->label('Email address')
                            ->email()
                            ->maxLength(255),

                        TextInput::make('prodi')
                            ->label('Prodi')
                            ->disabled(),

                        TextInput::make('jurusan')
                            ->label('Jurusan')
                            ->disabled(),
                    ]),
                ]),

            Section::make('Change Password')
                ->schema([
                    TextInput::make('new_password')
                        ->label('New Password')
                        ->password()
                        ->maxLength(255)
                        ->rules([Password::defaults()])
                        ->dehydrated(fn ($state) => filled($state)) // hanya kirim jika diisi
                        ->dehydrateStateUsing(fn ($state) => Hash::make($state)),
                ]),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Ubah password jika ada input
        if (!empty($data['new_password'])) {
            $data['password'] = $data['new_password'];
        }

        // Hapus field-field yang tidak perlu disimpan
        unset($data['new_password'], $data['prodi'], $data['jurusan']);

        return $data;
    }

    protected function getFormModel(): \Illuminate\Database\Eloquent\Model
    {
        return auth()->user();
    }

    protected function afterSave(): void
    {
        Notification::make()
            ->title('Profile updated successfully')
            ->success()
            ->send();
    }
}
