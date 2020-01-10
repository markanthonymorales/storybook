@component('mail::message')
Hello **{{$name}}**  
  
  
You have successfully placed your order for the "**{{$title}}**" Book 
  

**Order Details**:  
Book Title: **{{$title}}**  
Author: **{{$author}}**  
Width: **{{$with}}**  
Height: **{{$height}}**  
Total Pages: **{{$total_page}}**  
Coloured Pages: **{{$total_colored_page}}**  
Coloured Pages Position: **{{$colored_index}}**  
Paper: **{{$paper}}**  
Binding: **{{$binding}}**  
Back: **{{$cover}}**  
Finish: **{{$lamination}}**  
Jacket: **N/A**  
  

**Delivery Details**:  
Address: **{{$address}}**  
Street: **{{$street}}**  
City: **{{$city}}**  
Zip: **{{$zipcode}}**  
Country: **{{$country}}**  
Shipping Type: **{{$shipping_option}}**  
Delivery Cost: **{{$shipping_price}}**  
  
  
**Payment Details**:  
Payment Type: Card  
Total Amount Paid: **{{$amount}}**  
  
  
Sincerely,  
Poetray.
@endcomponent