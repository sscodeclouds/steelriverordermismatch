<?php


namespace App\Http\Controllers;

use App\Models\GA_ecommerce;
use Illuminate\Support\Facades\DB;
use Illuminate\Bus\Batch;
use App\Jobs\GACsvProcess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Bus;

class HomeController extends Controller{
    public function index() {
        return view('home');
    }

    public function uploadGA(Request $request)
    {
        $request->validate([
            'ga_csv' => 'required|csv',
        ]);
        $header = [];
        $batch = Bus::batch([])->dispatch();
        $csv    = file($request->ga_csv);

        $chunks = array_chunk($csv,1000);

        foreach ($chunks as $key => $chunk) {
            $data = array_map('str_getcsv', $chunk);
            if($key == 0){
                $header = $data[0];
                unset($data[0]);
            }
            $batch->add(new GACsvProcess($header, $data));
        }

        return $batch;
    }

    public function exportGACompReport(Request $request) {
        $sd = $request->sd;
        $ed = $request->ed;
        $fileName = 'ga_report_'.$sd.'-'.$ed.'.csv';
        $tasks = DB::select("SELECT
        t1.order_id AS sticky_order_id, t1.revenue AS sticky_order_total,  date_format(str_to_date(t1.created_at, '%Y-%m-%d %H:%i:%s'),'%m/%d/%Y') AS sticky_order_date, t1.order_status AS order_status, t2.order_id AS ga_order_id, t2.revenue AS ga_order_total,  date_format(str_to_date(t2.order_date, '%Y%m%d'),'%m/%d/%Y') AS ga_order_date, CASE WHEN t1.order_id = t2.order_id THEN CASE WHEN t1.revenue = t2.revenue THEN 'Matched' ELSE 'Price Not Match' END ELSE 'Order Skip' END AS reason
      FROM stickies t1
      LEFT JOIN g_a_ecommerces t2
        ON t1.order_id = t2.order_id WHERE t1.created_at BETWEEN '$sd 00:00:00' AND '$ed 23:59:59' ORDER BY sticky_order_id");

        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        $columns = array('Sticky Order ID', 'Sticky Order Total
        ', 'Sticky Order Date
        ', 'GA Order Id
        ', 'GA Order Total
        ', 'GA Order Date
        ', 'Dev Comment/ Reason');

        $callback = function() use($tasks, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($tasks as $task) {
                if($task->order_status == 'approved') {
                    $row['Sticky Order ID']  = $task->sticky_order_id;
                    $row['Sticky Order Total']    = $task->sticky_order_total;
                    $row['Sticky Order Date']    = $task->sticky_order_date;
                    $row['GA Order Id']  = $task->ga_order_id;
                    $row['GA Order Total']  = $task->ga_order_total;
                    $row['GA Order Date']  = $task->ga_order_date;
                    $row['Dev Comment/ Reason']  = $task->reason;

                    fputcsv($file, array($row['Sticky Order ID'], $row['Sticky Order Total'], $row['Sticky Order Date'], $row['GA Order Id'], $row['GA Order Total'], $row['GA Order Date'], $row['Dev Comment/ Reason']));
                }
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
