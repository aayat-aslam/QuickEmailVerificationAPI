<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]>      <html class="no-js"> <!--<![endif]-->
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>Example email verification Code</title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="./assets/vendor/bootstrap/css/bootstrap.min.css">
    </head>
    <body>
        <!--[if lt IE 7]>
            <p class="browsehappy">You are using an <strong>outdated</strong> browser. Please <a href="#">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->

        <div class="container">
            <div class="row">
                <div class="col">
                    <h2>Welcome to Email verification</h2>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <label for="email">Enter email to verify</label>
                        <input type="email" id="email" name="email" class="form-control email">
                        <small style="color: red;" id="showError"></small>
                        <small style="color: green;" id="showSuccess"></small>
                    </div>
                    <div class="form-group">
                        <button id="btnSubmit" class="btn btn-primary">Submit</button>
                    </div>
                </div>
            </div>
        </div>
        
        <script src="./assets/vendor/jquery/jquery.js"></script>
        <script src="./assets/vendor/bootstrap/js/bootstrap.min.js"></script>
        <script>
            $(document).ready(function(){
                $('#btnSubmit').click(function(){
                    $.post('backend.php',{r:'verifyEmail',email:$('#email').val()},function(res){
                        if( res.success ) {
                            $('#showError').html('');
                            $('#showSuccess').html( res.message );
                            setTimeout(function(){
                                $('#email').val('');
                                $('#showSuccess').html('');
                                }, 4000);
                        } else {
                            $('#showSuccess').html('');
                            $('#showError').html( res.message );
                        }
                        console.log(res);
                    });
                });
            });
        </script>
    </body>
</html>