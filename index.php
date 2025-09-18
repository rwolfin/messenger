<?php
// Login Page

session_start();

$error = '';

if (isset($_SESSION['user_data'])) 
{
    header('location:group_chat.php');
}

if (isset($_POST['login'])) 
{
    require_once('database/ChatUserModel.php');

    $user_object = new ChatUserModel;

    $user_object->setUserEmail($_POST['user_email']);
    $user_data = $user_object->get_user_data_by_email();

    if (is_array($user_data) && count($user_data) > 0)
    {
        if ($user_data['user_status'] == 'Enable') 
        {
            if ($user_data['user_password'] == $_POST['user_password']) 
            {
                $user_object->setUserId($user_data['user_id']);
                $user_object->setUserLoginStatus('Login'); 

                $user_token = md5(uniqid()); 
                $user_object->setUserToken($user_token);

                if ($user_object->update_user_login_data())
                {
                    
                    $_SESSION['user_data'][$user_data['user_id']] = [
                        'id'      =>  $user_data['user_id'],
                        'name'    =>  $user_data['user_name'],
                        'profile' =>  $user_data['user_profile'],
                        'token'   =>  $user_token
                    ];

                    header('location:group_chat.php');

                }
            }
            else
            {
                $error = 'Wrong Password!';
            }
        }
        else
        {
            $error = 'Please Verify Your Email Address (User is disabled)';
        }
    }
    else
    {
        $error = 'Wrong Email Address!';
    }
}

?>

<!DOCTYPE html>
<html lang="ru">

    <head>

        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="">

        <title>Real-time One-to-One & Group Chat Application using Websocket</title>

        <!-- Bootstrap core CSS -->
        <link href="vendor-front/bootstrap/bootstrap.min.css" rel="stylesheet">
        <link href="vendor-front/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
        <link rel="stylesheet" type="text/css" href="vendor-front/parsley/parsley.css"/>
        <link rel="icon" type="image/x-icon" href="vendor-front/bubble-chat.png"> <!-- HTML Favicon -->

        <!-- Bootstrap core JavaScript -->
        <script src="vendor-front/jquery/jquery.min.js"></script>
        <script src="vendor-front/bootstrap/js/bootstrap.bundle.min.js"></script>

        <!-- Core plugin JavaScript-->
        <script src="vendor-front/jquery-easing/jquery.easing.min.js"></script>
        <script type="text/javascript" src="vendor-front/parsley/dist/parsley.min.js"></script>
    </head>

    <body>
<style>
    html, body {
        height: 100%;
        margin: 0;
    }
    
    body {
        display: flex;
        flex-direction: column;
        min-height: 100vh;
    }
    
    .container {
        flex: 1;
    }
    
    footer {
        background-color: #f8f9fa;
        padding: 20px 0;
        margin-top: auto;
    }
</style>
        <div class="container">
            <br />
            <br />
           
            <div class="row justify-content-md-center mt-5">
                
                <div class="col-md-4">
                <?php
                if (isset($_SESSION['success_message'])) 
                {
                        echo '
                            <div class="alert alert-success">
                                ' . $_SESSION["success_message"] . '
                            </div>
                        ';

                   
                        unset($_SESSION['success_message']);
                }

                if ($error != '')
                {
                        echo '
                            <div class="alert alert-danger">
                                ' . $error . '
                            </div>
                        ';
                }
                ?>
                    <div class="card">
                        <div class="card-header text-center"><strong> Форма Входа</strong></div>
                        <div class="card-body">
                            <form method="post" id="login_form">
                                <div class="form-group">
                                    <label>Ведите свой адрес электронной почты</label>
                                    <input type="text" name="user_email" id="user_email"  class="form-control" data-parsley-type="email" required />
                                </div>
                                <div class="form-group">
                                    <label>Введите свой пароль</label>
                                    <input type="password" name="user_password" id="user_password" class="form-control" required />
                                </div>
                                <div class="form-group text-center">
                                    <input type="submit" name="login" id="login" class="btn btn-primary" value="Войти в мой мессенджер" />
                                </div>
                                <a class="btn btn-link" href="register.php">Зарегистрироваться</a>
                            </form>
                        </div>  
                    </div>
                </div>
            </div>
        </div>

        <footer>
            <div class="text-center"><small >Приложение для индивидуального и группового чата в режиме реального времени с использованием Ratchet WebSocket с PHP MySQL - Онлайн/Оффлайн статус</small></div>
        </footer>

    </body>

</html>

<script>

    $(document).ready(function(){

        $('#login_form').parsley();

    });

</script>