<?php

namespace App\Jobs;

use App\Models\GA_ecommerce;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GACsvProcess implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    public $header; 
    public $data; 
    public function __construct($header, $data)
    {
        $this->header = $header;
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        foreach($this->data as $item) {
            $gaData = array_combine($this->header,$item);
            GA_ecommerce::insertOrIgnore($gaData);
        }
    }
}
