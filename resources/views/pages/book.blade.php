@extends('layouts.app')
@section('footer')
    <script src="https://js.stripe.com/v3/"></script>
@endsection
@section('custom_css')
<style type="text/css">
    .el-calendar__body {
	    padding: 5px 10px !important;
	}
	.el-calendar-table thead th {
	    padding: 5px 0 !important;
	}
	.el-calendar-table .el-calendar-day {
	    height: 35px !important;
	    padding: 5px !important;
	}
	.el-calendar__title{ width: 100%; text-align: center; }
	.el-calendar__button-group{ display: none; }
	.el-carousel__item h3 {
		color: #475669;
		font-size: 14px;
		opacity: 0.75;
		line-height: 200px;
		margin: 0;
	}
	.el-carousel__item--card.is-active{
		border: 1px solid #e2e2e2;
	}

	.el-carousel__item:nth-child(2n) {
		background-color: #99a9bf;
	}

	.el-carousel__item:nth-child(2n+1) {
		background-color: #d3dce6;
	}
	.big-paper.is_cover{
    	background-position: center;
      	background-repeat: no-repeat;
      	background-size: contain;
      	/*background-size: cover;*/
      	height: inherit;
    }
</style>
@endsection
@section('content')
    <book-page :book-data="{{ $book }}" :auth-id="{{ Auth::user()->id }}" :author-data="'{{ Auth::user()->firstname }} {{ Auth::user()->lastname }}'" :markup-price="{{ $markup_price }}" :ebook-markup-price="{{ $ebook_markup_price }}"></book-page>
@endsection