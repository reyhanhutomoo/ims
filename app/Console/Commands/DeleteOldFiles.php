<?php

namespace App\Console\Commands;

use App\WeeklyReports;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class DeleteOldFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'files:delete-old';
    protected $description = 'Delete files older than one month';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $files = WeeklyReports::where('created_at', '<', Carbon::now()->subMonth())->get();

        foreach ($files as $file) {
            Storage::delete('public/pdf_reports/' . $file->file);
            $file->delete();
        }

        $this->info('Old files deleted successfully.');
    }
}
