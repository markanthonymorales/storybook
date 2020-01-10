@extends('layouts.app')
@section('footer')
    <script src="https://js.stripe.com/v3/"></script>
@endsection
@section('custom_css')
<style type="text/css">
    main{ background-color: #f8fafc !important; }
	#summaryPage { 
        margin-top: 100px;
    }
    #summaryPage .card {
        box-shadow: 0 4px 8px 0 #b3b3b3, 0 6px 20px 0 #b3b3b3;
    }
    #summaryPage h1 { font-size: 18px; font-weight: 700; }
    .btn-checkout { padding: 5px 25px; }
</style>
@endsection
@section('content')
    @if(isset(Auth()->user()->id))
        <cart-summary-page 
          fullname-data="{{ Auth()->user()->firstname.' '.Auth()->user()->lastname }}"  
          email-data="{{ Auth()->user()->email }}"
        ></cart-summary-page>
    @else
        <cart-summary-page 
          fullname-data=""  
          email-data=""
        ></cart-summary-page>
    @endif
@endsection