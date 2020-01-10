@component('mail::message')
Hello Friend,  {{-- use double space for line break --}}
Some one share you a story.  
We invited you to visit our storybook application, for you to view the story shared by "**{{$name}}**".  
  
Click below to start working right now
@component('mail::button', ['url' => $link])
Click Here
@endcomponent
Thank you and enjoy!  
  
Sincerely,  
Poetray.
@endcomponent