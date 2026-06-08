<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Mail\DataClearAlertMail;
use Illuminate\Support\Facades\Mail;
use App\Models\user;
class SendDataClearAlert extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alert:data-clear';
    protected $description = 'Kirim email peringatan penghapusan data ke owner';

    public function handle()
    {
        $year      = now()->year;
        $clearDate = now()->addDays(7)->translatedFormat('d F Y');

        // Mengambil email dari .env
        $emailTujuan = env('OWNER_EMAIL'); 

        Mail::to($emailTujuan)->send(
            new DataClearAlertMail($clearDate, $year)
        );

        $this->info("✅ Email alert terkirim ke: {$emailTujuan}");
    }
}
