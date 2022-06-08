@extends('master')

@section('title', 'Chi tiết')

@section('content')
    <div class="row">
		<table id="data-detail-csgoroll" class="table table-striped table-bordered table-sm table-hover" cellspacing="0" width="100%">
		    <thead>
		        <tr>
		            <th scope="col">STT</th>
		            <th scope="col">Tên Sản Phẩm</th>
		            <th scope="col">Buff.163.com</th>
		            <th scope="col">Csgoroll.com</th>
		            <th scope="col">Rate</th>
		            <th scope="col">Thời gian</th>
		        </tr>
		    </thead>
		    <tbody>
		    	@php
		    		$i = 1;
		    	@endphp
		    	@foreach($csgoroll as $item)
		    		<tr>
			            <td>{{$i++}}</td>
			            <td>
			            	{{$item->full_name}}
			            </td>
			            <td 
			            	@if($item->buff)
			            		data-toggle="tooltip" data-placement="top" title="{{$item->buff/$item->conversion_price_buff}}"
		            		@endif
			            >
			            	@if($item->buff)
			            		{{number_format($item->buff, 2)}}
		            		@endif
			            </td>
			            <td 
			            	@if($item->price_difference)
			            		data-toggle="tooltip" data-placement="top"  title="{{$item->price_difference}}"
		            		@endif
		            	>
			            	{{number_format($item->csgoroll, 2)}}
			            </td>
			            <td>{{$item->rate}}</td>
			            <td>{{ date('Y/m/d H:i:s', strtotime($item->created_at)) }}</td>
			        </tr>
		    	@endforeach
		    </tbody>
		</table>
	</div>
@endsection

@section('script')
	<script type="text/javascript">
		$(document).ready(function(){
			$('#data-detail-csgoroll').DataTable({
	        	"language": {
				    "decimal":        "",
				    "emptyTable":     "Không có dữ liệu",
				    "info":           "Hiển thị _START_ đến _END_ trong _TOTAL_ sản phẩm",
				    "infoEmpty":      "Hiển thị 0 đến 0 trong 0 sản phẩm",
				    "infoFiltered":   "(Được tìm từ _MAX_ sản phẩm)",
				    "infoPostFix":    "",
				    "thousands":      ",",
				    "lengthMenu":     "Hiển thị _MENU_ sản phẩm",
				    "loadingRecords": "Loading...",
				    "processing":     "Processing...",
				    "search":         "Tìm kiếm:",
				    "zeroRecords":    "Không tìm thấy kết quả",
				    "paginate": {
				        "first":      "First",
				        "last":       "Last",
				        "next":       '<i class="fas fa-angle-right"></i>',
				        "previous":   '<i class="fas fa-angle-left"></i>'
				    },
				    "aria": {
				        "sortAscending":  ": activate to sort column ascending",
				        "sortDescending": ": activate to sort column descending"
				    }
				}
	      	});
			$('.dataTables_length').addClass('bs-select');
		});
	</script>
@endsection