<?php

namespace App\Filament\User\Widgets;

use Filament\Widgets\Widget;

class ProdiJurusanWidget extends Widget
{
    protected static string $view = 'filament.user.widgets.prodi-jurusan-widget';
    protected int | string | array $columnSpan = 'full';

    public function getViewData(): array   // data dikirim ke Blade
    {
        return [
            'user' => auth()->user(),
        ];
    }
}
