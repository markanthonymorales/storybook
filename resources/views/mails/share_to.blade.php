@component('mail::message')
Hello **{{$name}}**,  {{-- use double space for line break --}}
  
You shared a story to "**{{$shared_to}}**".  
  
Thank you and enjoy!  
  
Sincerely,  
Poetray.
@endcomponent