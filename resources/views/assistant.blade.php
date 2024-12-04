<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <form action="{{ route('assistant') }}" method="post" enctype="multipart/form-data">
        @csrf
        <input type="file" name="image" id="image">
        <input type="text" name="message"> </br>
        <button type="submit">do it</button>
    </form>
</body>
</html>
