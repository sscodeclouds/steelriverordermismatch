<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Steel River</title>
    <!-- Core theme CSS (includes Bootstrap)-->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <link href="{{ URL::asset('css/styles.css') }}" rel="stylesheet" />
</head>
<body id="page-top">
<!-- Navigation-->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top" id="mainNav">
    <div class="container px-4">
        <a class="navbar-brand" href="#page-top">Steel River</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
        <div class="collapse navbar-collapse" id="navbarResponsive">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="#fetch_sticky">Fetch Sticky Order</a></li>
                <li class="nav-item"><a class="nav-link" href="#import_ga">Import GA Data</a></li>
                <li class="nav-item"><a class="nav-link" href="#compare">Compare Sticy with GA</a></li>
            </ul>
        </div>
    </div>
</nav>
<!-- Header-->
<header class="bg-primary bg-gradient text-white">
    <div class="container px-4 text-center">
        <h1 class="fw-bolder">Welcome to Steel River Order Playground</h1>
    </div>
</header>
<!-- Fetch Sticky section-->
<section id="fetch_sticky">
    <div class="container px-4">
        <div class="row gx-4 justify-content-center">
            <div class="col-lg-8">
                <h2>Fetch Sticky Order</h2>
                <p class="lead">This will fetch the data from Sticky.io and save the the into stickies database table. You need to run this command to process: </p>
                <ul>
                    <li>php artisan sticky:fetch</li>
                    <li>Then place the start date</li>
                    <li>Then place the end date</li>
                    <li>Then choose if you want to fetch test orders or not.</li>
                </ul>
                <div class="console">
                    <div class="console-header">
                        <p>test@codeclouds.biz</p>
                    </div>
                    <div class="consolebody">
                        <p>> php artisan sticky:fetch</p>
                        <p>Please provide the start date (mm/dd/yyyy):</p>
                        <p>> 01/01/2022</p>
                        <p>Please provide the end date (mm/dd/yyyy):</p>
                        <p>> 01/02/2022</p>
                        <p>Do you want to fetch test orders only? [n]:
                            [0] y
                            [1] n
                        </p>
                        <p>> n</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Import GA section-->
<section class="bg-light" id="import_ga">
    <div class="container px-4">
        <div class="row gx-4 justify-content-center">
            <div class="col-lg-8">
                <h2>Import GA Data</h2>
                <form class="form-inline" action="{{ route('upload_ga') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <label for="upload_ga_csv" class="sr-only">Upload GA CSV</label>
                    <input type="file" name="ga_csv" class="form-control-file mb-2 mr-sm-2" id="upload_ga_csv">
                    <button type="submit" class="btn btn-primary mb-2">Submit</button>
                </form>
                <div class="clearfix"></div>
                <button type="button" class="btn btn-secondary mt-5">Download Sample CSV</button>
            </div>
        </div>
    </div>
</section>
<!-- compare section-->
<section id="compare">
    <div class="container px-4">
        <div class="row gx-4 justify-content-center">
            <div class="col-lg-8">
                <h2>Compare Sticy with GA</h2>
                <input type="text" name="daterange" value="01/01/2022 - 01/31/2022" />
                <span data-href="/exportReport?sd=2022-01-01&ed=2022-01-31" id="export-report" class="btn btn-success btn-sm" onclick="exportTasks(event.target);">Export</span>
            </div>
        </div>
    </div>
</section>
<!-- Footer-->
<footer class="py-5 bg-dark">
    <div class="container px-4"><p class="m-0 text-center text-white">Copyright &copy; Steel River {{date('Y')}}</p></div>
</footer>
<!-- Bootstrap core JS-->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script>
$(function() {
  $('input[name="daterange"]').daterangepicker({
    opens: 'left'
  }, function(start, end, label) {
    $('#export-report').attr('data-href', `/exportReport?sd=${start.format('YYYY-MM-DD')}&ed=${end.format('YYYY-MM-DD')}`);
  });
});
function exportTasks(_this) {
    let _url = $(_this).data('href');
    window.location.href = _url;
}
</script>
<!-- Core theme JS-->
<script src="{{ URL::asset('js/scripts.js') }}"></script>
</body>
</html>

