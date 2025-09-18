<?php 


session_start();

if (!isset($_SESSION['user_data'])) 
{
    header('location:index.php'); 
}

require('database/ChatUserModel.php');


$user_object = new ChatUserModel;
$user_id = '';

// Get the user ID from the Session
foreach ($_SESSION['user_data'] as $key => $value)
{
    $user_id = $value['id'];
}

$user_object->setUserId($user_id);
$user_data = $user_object->get_user_data_by_id();

$message = '';

if (isset($_POST['edit'])) 
{
    $user_profile = $_POST['hidden_user_profile'];

    if ($_FILES['user_profile']['name'] != '')
    {
        $user_profile = $user_object->upload_image($_FILES['user_profile']);
        $_SESSION['user_data'][$user_id]['profile'] = $user_profile;
    }

    if( isset($_POST["showemail"]) ) $user_object->setShowemail( 1 );
    else $user_object->setShowemail( 0 );
    $user_object->setUserName($_POST['user_name']);
    $user_object->setUserEmail($_POST['user_email']);
    $user_object->setUserPassword($_POST['user_password']);
    $user_object->setUserProfile($user_profile);
    $user_object->setUserId($user_id);

    if ($user_object->update_data()) 
    {
        $message = '<div class="alert alert-success">Изменения обновлены!</div>';
    }
}


?>

<!DOCTYPE html>
<html>
<head>
	<title>Редактировать профиль</title>
	<!-- Bootstrap core CSS -->
    <link href="vendor-front/bootstrap/bootstrap.min.css" rel="stylesheet">
    <link href="vendor-front/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" type="text/css" href="vendor-front/parsley/parsley.css"/>
    
    <!-- HTML Favicon -->
    <link rel="icon" type="image/x-icon" href="vendor-front/bubble-chat.png"> 

    <!-- Bootstrap core JavaScript -->
    <script src="vendor-front/jquery/jquery.min.js"></script>
    <script src="vendor-front/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="vendor-front/jquery-easing/jquery.easing.min.js"></script>
    <script type="text/javascript" src="vendor-front/parsley/dist/parsley.min.js"></script>
</head>
<body>
	<div class="container">
        <br> <br>
	    <?php echo $message; ?>
		<div class="card">
			<div class="card-header">
                <div class="row">
                    <div class="col-md-6"><strong>Мой профиль</strong></div>
                    <div class="col-md-6 text-right"><a href="group_chat.php" class="btn btn-warning btn-sm">перейти в <b>Групповой чат</b></a></div>
                </div>
            </div>
            <div class="card-body">
                <form method="post" id="profile_form" enctype="multipart/form-data"> 
                    <div class="form-group">
                        <label>Имя</label>
                        <input type="text" name="user_name" id="user_name" class="form-control" data-parsley-pattern="/^[a-zA-Zа-яА-ЯёЁ0-9\s\?]+$/" required value="<?php echo $user_data['user_name']; ?>" />
                    </div>
                    <div class="form-group">
                        <label>Почта e-mail</label>
                        <input type="email" name="user_email" id="user_email" class="form-control" data-parsley-type="email" required readonly value="<?php echo $user_data['user_email']; ?>" />
                        <input type="checkbox" id="showemail" name="showemail"<?php echo (isset($_POST["showemail"]) || (isset($user_data["showemail"]) && $user_data["showemail"])) ? "checked" : ""; ?> />
                        <label for="showemail">Показывать E-Mail вместо ника</label>
                    </div>
                    <div class="form-group">
                        <label>Пароль</label>
                        <input type="password" name="user_password" id="user_password" class="form-control" data-parsley-minlength="6" data-parsley-maxlength="12" data-parsley-pattern="^[a-zA-Z0-9]+$" required value="<?php echo $user_data['user_password']; ?>" />
                    </div>
                    <div class="form-group">
                        <label>Фотография Профиля</label><br />
                        <input type="file" name="user_profile" id="user_profile" />
                        <br />
                        <img src="<?php echo $user_data['user_profile']; ?>" class="img-fluid img-thumbnail mt-3" width="100" />
                        <input type="hidden" name="hidden_user_profile" value="<?php echo $user_data['user_profile']; ?>" />
                    </div>
                    <div class="form-group text-center">
                        <input type="submit" name="edit" class="btn btn-success" value="Сохранить изменения" />
                    </div>
                </form>
            </div>
		</div>
	</div>
</body>
</html>

<script>

$(document).ready(function(){

    $('#profile_form').parsley(); 

    $('#user_profile').change(function(){
        var extension = $('#user_profile').val().split('.').pop().toLowerCase();
        if (extension != '')
        {
            if (jQuery.inArray(extension, ['gif','png','jpg','jpeg']) == -1)
            {
                alert("Invalid Image File");
                $('#user_profile').val('');
                return false;
            }
        }
    });

});

</script>