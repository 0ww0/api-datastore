<DOCTYPE html>
<html lang="en-US">
    <head>
        <meta charset="utf-8">
    </head>
    <body>
        <h2> Hi {{$data['name']}}, we’re glad you’re here! Following are your account details:</h2> <br>
        <h2>Name : {{ $data['name'] }}</h2>
        <h2>Email : {{ $data['email'] }}</h2>
        <h2>Token : <a href="{{ route('email.verify'). '?token=' . $data['verification_token'] }}">Verifed Token</a> </h2>
    </body>
</html>
