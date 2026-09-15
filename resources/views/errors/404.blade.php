<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Page Not Found</title>
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
        <h1>404</h1>
        <p>This cake isn't on our menu! The page you're looking for doesn't exist.</p>
        <a href="{{ url('/') }}">Go to Homepage</a>
    </div>
</body>
</html>