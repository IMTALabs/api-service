<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <title>Document</title>
</head>
<body>
<style>
    h1 {
        font-size: 24px;
        font-weight: bold;
        color: #333;
        text-align: center;
    }

    h2 {
        font-size: 16px;
        font-weight: bold;
        color: #333;
    }

    p {
        text-align: justify;
        font-size: 16px;
        line-height: 1.5;
        color: #555;
    }
</style>
<div class="container text-justify ">
    {!! $convertedParagraph !!}
</div>
<div class=" container fs-3 text-center">Read and do questions 1 to 10 and choose the correct answer A, B, C or D</div>
<div class="container mt-5 px-5">
    @foreach($quiz as $key=>$value)
        <h2>{{$key+1 .' : '.$value['question']}}</h2>
        @foreach($value['choices'] as $index=>$item)
            <p>{{$index .' : '.$item}}</p>
        @endforeach
    @endforeach
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
        crossorigin="anonymous"></script>
</body>
</html>
