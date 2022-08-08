<?php

namespace App\Console\Commands;

use Illuminate\Support\Facades\Validator;
use App\Lib\StickyOrder;
use App\Models\Sticky;
use Illuminate\Console\Command;

class FetchSticky extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sticky:fetch';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetching the orders from Sticky.io and saved it into stickies table';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    private array $orderStatus;
    public function __construct()
    {
        parent::__construct();
        $this->orderStatus = [
            '2'  => 'approved',
            '6'  => 'refunded',
            '7'  => 'declined',
            '8'  => 'shipped',
            '11' => 'pending',
        ];
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $start_date = $this->ask('Please provide the start date (mm/dd/yyyy)');
        $end_date = $this->ask('Please provide the end date (mm/dd/yyyy)');
        $is_test_order = $this->choice(
            'Do you want to fetch test orders only?',
            ['y', 'n'],
            1
        );
        $validator = Validator::make([
            'start_date' => $start_date,
            'end_date' => $end_date,
            'is_test_order' => $is_test_order
        ], [
            'start_date' => ['required', 'date_format:m/d/Y'],
            'end_date' => ['required', 'date_format:m/d/Y'],
            'is_test_order' => ['required', 'in:y,n']
        ]);
        if ($validator->fails()) {
            $this->info('Sticky data can not be fetched. See error messages below:');

            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }
            return 1;
        }
        $stickyUsername = env('SFD_STICKY_USERNAME');
        $stickyPassword = env('SFD_STICKY_PASSWORD');
        $stickyDomain = env('SFD_STICKY_DOMAIN');
        $stickyOrder = new StickyOrder($stickyDomain, $stickyUsername, $stickyPassword);
        $orderResponse = $stickyOrder->v1GetOrders($start_date, $end_date, $is_test_order == 'y' ? true : false);
        $invalid_order_ids = [];
        if($orderResponse['success'] == true) {
            if($orderResponse['data']['response_code'] == 100) {
                $bar = $this->output->createProgressBar(count($orderResponse['data']['order_id']));
                $bar->start();
                foreach ($orderResponse['data']['order_id'] as $order_id) {
                    $order_details = $stickyOrder->v1GetOrderDetails($order_id);
                    if($order_details['success'] == true) {
                        if($order_details['data']['response_code'] == 100) {
                            Sticky::updateOrCreate([
                                'order_id'   => $order_id,
                            ],[
                                'is_test_order' => $is_test_order == 'y' ? 'yes' : 'no',
                                'revenue'       => $order_details['data']['order_total'],
                                'order_status'       => $this->orderStatus[$order_details['data']['order_status']],
                                'created_at'    => $order_details['data']['time_stamp']
                            ]);
                        }else {
                            array_push($invalid_order_ids, ['order_id' => $order_id]);
                        }
                    }
                    $bar->advance();
                }
                $bar->finish();
                $this->newLine(2);
                $this->info('The process was successful!');
                if(count($invalid_order_ids)) {
                    $this->newLine(2);
                    $this->info('Following order ids can not be processed!');
                    $this->newLine();
                    $this->table(
                        ['Order ID'],
                        $invalid_order_ids
                    );
                }
            }else {
                $this->info('The process failed due to lack of data!');
            }
        }else {
            $this->error('Something went wrong!');
        }
    }
}
