<?php

namespace App\Console\Commands;

use App\Models\Umkm;
use Illuminate\Console\Command;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

class GenerateSitemap extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-sitemap';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate sitemap otomatis untuk Peta Kuliner Sumenep';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $sitemap = Sitemap::create();

        // 1. Tambahkan halaman statis utama
        $sitemap->add(Url::create('/')
            ->setPriority(1.0)
            ->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY));

        $sitemap->add(Url::create('/map')
            ->setPriority(0.9)
            ->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY));

        // 2. Looping data dari Database UMKM Kuliner Sumenep
        // Menggunakan chunk agar memori server tetap aman jika data ribuan
        Umkm::chunk(200, function ($umkms) use ($sitemap) {
            foreach ($umkms as $umkm) {
                $sitemap->add(Url::create("/kuliner/{$umkm->slug}")
                    ->setLastModificationDate($umkm->updated_at)
                    ->setPriority(0.8)
                    ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY));
            }
        });

        // 3. Simpan file ke folder public
        $sitemap->writeToFile(public_path('sitemap.xml'));

        $this->info('Sitemap Peta Kuliner Sumenep berhasil di-generate!');
    }
}
