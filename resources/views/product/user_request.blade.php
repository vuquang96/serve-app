@extends('master')

@section('title', 'Danh sách dữ liệu')

@section('content')

    <div class="row">
		<table class="table able-hover">
		    <thead>
		        <tr>
		            <th scope="col">STT</th>
		            <th scope="col">Thời gian bắt đầu</th>
		            <th scope="col">Thời gian kết thúc</th>
		            <th scope="col">Số sản phẩm</th>
		            <th scope="col">&nbsp;</th>
		        </tr>
		    </thead>
		    <tbody>
		    	@php
		    		$i = 1;
		    	@endphp
		    	@foreach($userRequest as $item)
		    		<tr>
			            <th scope="row">{{ $i++ }}</th>
			            <td><a href="{{ route('product.list', ['requestID' => $item->id]) }}">{{ date('Y/m/d H:i:s', strtotime($item->time_start)) }}</a></td>
			            <td><a href="{{ route('product.list', ['requestID' => $item->id]) }}">{{ date('Y/m/d H:i:s', strtotime($item->time_end)) }}</a></td>
			            <td>{{ $item->getTotalProduct() }}</td>
			            <td>
			            	<a href="javascript:void(0)" data-requestid="{{$item->id}}" class="del-item text-danger">Xóa</a>
			            </td>
			        </tr>
		    	@endforeach
		    </tbody>
		</table>
		<div class="d-felx justify-content-center">
            {{ $userRequest->links() }}
        </div>
	</div>
	
@endsection

@section('script')
	<script type="text/javascript">
		$(document).ready(function(){
			$(".del-item").click(function(){
				if (confirm("Xóa dữ liệu này, sẽ không thể khôi phục lại ?") == true) {
				  	var id = $(this).data('requestid');
				  	window.location.href = `/serve-app/public/${id}/destroy`;
				}
			});
		});
	</script>
@endsection