<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Something Went Wrong</title>
    <style>
        body { font-family: Arial, sans-serif; background:#FFF8F0; display:flex; align-items:center; justify-content:center; height:100vh; margin:0; text-align:center; }
        .box { max-width:400px; }
        h1 { color:#A31E42; font-size:4rem; margin:0; }
        p { color:#555; }
        a { background:#A31E42; color:#fff; padding:10px 24px; border-radius:6px; text-decoration:none; display:inline-block; margin-top:16px; }
    </style>
</head>
<body>
    <div class="box">
        <h1>Oops!</h1>
        <p>Something went wrong on our end. Our team has been notified. Please try again in a moment.</p>
        <a href="{{ url('/') }}">Go to Homepage</a>
    </div>
</body>
</html>