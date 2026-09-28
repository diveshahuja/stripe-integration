<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Checkout</title>
</head>
<body>
    <form action="{{route('orders.store')}}" method="POST">
        @csrf
        <select name="payment_method">
            @foreach($paymentMethods as $paymentMethod)
                <option value="{{$paymentMethod}}">{{$paymentMethod}}</option>
            @endforeach
        </select>
        <button type="submit">Pay</button>
    </form>
</body>
</html>
