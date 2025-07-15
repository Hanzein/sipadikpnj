<?php

// App\Http\Middleware\CheckSubmissionPeriod.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\PengaturanWaktu;
use Filament\Notifications\Notification;
use Filament\Facades\Filament;

class CheckSubmissionPeriod
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Hanya cek untuk route create dan edit pengajuan
        if (!$request->routeIs('filament.user.resources.riwayat-pengajuan.create') && 
            !$request->routeIs('filament.user.resources.riwayat-pengajuan.edit')) {
            return $next($request);
        }

        $currentPeriod = PengaturanWaktu::getCurrentPeriod();
        
        // Jika tidak ada periode aktif
        if (!$currentPeriod) {
            Notification::make()
                ->title('Periode Pengajuan Tidak Aktif')
                ->body('Saat ini tidak ada periode pengajuan yang aktif. Silakan hubungi admin untuk informasi lebih lanjut.')
                ->danger()
                ->persistent()
                ->send();
            
            return redirect()->route('filament.user.resources.riwayat-pengajuan.index');
        }

        // Jika periode belum dimulai
        if ($currentPeriod->isUpcoming()) {
            $startsIn = $currentPeriod->tanggal_buka->diffForHumans();
            
            Notification::make()
                ->title('Periode Pengajuan Belum Dimulai')
                ->body("Periode pengajuan akan dimulai $startsIn ({$currentPeriod->tanggal_buka->format('d/m/Y H:i')})")
                ->warning()
                ->persistent()
                ->send();
            
            return redirect()->route('filament.user.resources.riwayat-pengajuan.index');
        }

        // Jika periode sudah berakhir
        if ($currentPeriod->isExpired()) {
            Notification::make()
                ->title('Periode Pengajuan Sudah Berakhir')
                ->body("Periode pengajuan telah berakhir pada {$currentPeriod->tanggal_tutup->format('d/m/Y H:i')}")
                ->danger()
                ->persistent()
                ->send();
            
            return redirect()->route('filament.user.resources.riwayat-pengajuan.index');
        }

        return $next($request);
    }
}