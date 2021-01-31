<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Send payment</title>
</head>
<body>
    <form method="post" action="{{ $url }}" id="atos-payment">
        @foreach ($fields as $name => $value)
            <input type="hidden" name="{{ $name }}" value="{{ $value }}">
        @endforeach
        <input type="submit" value="Send payment">
    </form>
    <script>
        document.getElementById("atos-payment").submit();
    </script>
</body>
</html>
