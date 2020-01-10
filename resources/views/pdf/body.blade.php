
		@foreach($contents as $k => $content)
			<div width="953px">{!! $content['value'] !!}</div>
			@if($k < count($contents) - 1)
				<div class="page-break"></div>
			@endif
		@endforeach
	