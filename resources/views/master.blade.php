<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="shortcut icon" href="{{ asset('favicon.png') }}">
	<title>@yield('title')</title>
	<link href="{{ asset('bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
	<link href="{{ asset('fontawesome/css/all.css') }}" rel="stylesheet">
	<link href="{{ asset('mdb/css/mdb.min.css') }}" rel="stylesheet">
	<link href="{{ asset('mdb/css/addons/datatables.min.css') }}" rel="stylesheet">

	<style type="text/css">
	    table.dataTable thead .sorting:after,
		table.dataTable thead .sorting:before,
		table.dataTable thead .sorting_asc:after,
		table.dataTable thead .sorting_asc:before,
		table.dataTable thead .sorting_asc_disabled:after,
		table.dataTable thead .sorting_asc_disabled:before,
		table.dataTable thead .sorting_desc:after,
		table.dataTable thead .sorting_desc:before,
		table.dataTable thead .sorting_desc_disabled:after,
		table.dataTable thead .sorting_desc_disabled:before {
			bottom: .5em;
		}
	</style>
</head>
<body>
	<div class="container">
		<div class="row">
			<nav class="navbar navbar-expand-lg navbar-red bg-red">
			    <div class="container-fluid">
			        <a class="navbar-brand" href="{{ route('product.request') }}">Danh sách dữ liệu</a>
			    </div>
			</nav>
		</div>
        @yield('content')
    </div>
</body>
	<script src="{{ asset('jquery-3.6.0.min.js') }}" ></script>
	<script src="{{ asset('bootstrap/js/bootstrap.min.js') }}" ></script>
	<script src="{{ asset('bootstrap/js/bootstrap.bundle.min.js') }}" ></script>
	<script src="{{ asset('mdb/js/popper.min.js') }}" ></script>
	<script src="{{ asset('mdb/js/addons/datatables.min.js') }}" ></script>

	<script type="text/javascript">
		$(document).ready(function(){
			$(function () {
			  $('[data-toggle="tooltip"]').tooltip()
			});
		});
	</script>
	@yield('script')
</html>