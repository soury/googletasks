<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
</head>
<body>

    <script>
        
        $.ajax(
            {
                url: "http://localhost/google-tasks-api/api1.php/task-lists",
                data: {
                    title: "ciao"
                },
                cache: false,
                type: "GET",
                success: function (res)
                        {
                            //res = JSON.parse(res);
                            console.log(res);
                        },
                error:  function()
                        {

                        }

            }
        )
    
    </script>
</body>
</html>