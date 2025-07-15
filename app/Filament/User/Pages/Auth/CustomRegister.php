<?php

namespace App\Filament\User\Pages\Auth;

use App\Models\User;
use Filament\Pages\Auth\Register;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Illuminate\Support\Facades\Hash;

class CustomRegister extends Register
{
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Nama')
                    ->required(),

                TextInput::make('nim')
                    ->label('NIM')
                    ->numeric()
                    ->rule('digits_between:5,12')
                    ->required()
                    ->unique(User::class, 'nim'),

                TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required()
                    ->unique(User::class, 'email')
                    ->rule(function () {
                        return 'regex:/^[\w\.-]+@(?:gmail\.com|mhsw\.pnj\.ac\.id)$/';
                    })
                    ->validationMessages([
                        'regex' => 'Email harus menggunakan domain @gmail.com atau @mhsw.pnj.ac.id.',
                    ]),

                TextInput::make('prodi')
                    ->label('Program Studi (Prodi)')
                    ->required(),

                Select::make('jurusan')
                    ->label('Jurusan')
                    ->required()
                    ->options([
                        'Administrasi Niaga' => 'Administrasi Niaga',
                        'Akuntansi' => 'Akuntansi',
                        'Teknik Elektro' => 'Teknik Elektro',
                        'Teknik Mesin' => 'Teknik Mesin',
                        'Teknik Sipil' => 'Teknik Sipil',
                        'Teknik Elektro' => 'Teknik Elektro',
                        'Teknik Informatika & Komputer' => 'Teknik Informatika & Komputer',
                        'Teknik Grafika dan Penerbitan' => 'Teknik Grafika dan Penerbitan',
                    ])
                    ->searchable(),

                TextInput::make('password')
                    ->label('Password')
                    ->password()
                    ->required()
                    ->minLength(8)
                    ->same('password_confirmation'),

                TextInput::make('password_confirmation')
                    ->label('Konfirmasi Password')
                    ->password()
                    ->required(),
            ]);
    }

    protected function handleRegistration(array $data): User
    {
        return User::create([
            'name' => $data['name'],
            'nim' => $data['nim'],
            'email' => $data['email'],
            'prodi' => $data['prodi'],
            'jurusan' => $data['jurusan'],
            'password' => Hash::make($data['password']),
        ]);
    }
}
