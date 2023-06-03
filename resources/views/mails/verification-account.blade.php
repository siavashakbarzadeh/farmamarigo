<!doctype html>
<html lang="en">
<head>
    <title>Document</title>
</head>
<body>
<a href="{{ $user->generateVerificationLink() }}" target="_blank">{{ $user->email }}</a>
</body>
</html>
