<?php 

session_start();


if (!isset($_SESSION['user_data'])) 
{
	header('location:index.php');
}

require('database/ChatUserModel.php'); 
require('database/GroupChatMessageModel.php'); 

//$chat_object = new GroupChatMessageModel;
//$chat_data = $chat_object->get_all_chat_data();
$chat_object = NULL;

$user_object = new ChatUserModel;
$user_data = $user_object->get_user_all_data();
$user_datasc = $user_object->get_user_all_data_with_status_count();

$groups = NULL;

								$login_user_id = '';
								$profile = "";

								foreach ($_SESSION['user_data'] as $key => $value) 
								{
									$name = $value['name'];
									$login_user_id = $value['id'];
									foreach( $user_data as $key2 => $value2 ) {
										if( $value2["user_id"] == $login_user_id ) {
											if( $value2['showemail'] ) $name = $value2['user_email'];
										}
									}
								}
								
								if( count($user_data) > 0 ) {
									foreach( $user_data as $key => $value ) {
										if( $value["user_id"] == $login_user_id ) {
											if( isset($value["memberof"]) && !empty($value["memberof"]) ) $groups = explode( ",", $value["memberof"] );
											break;
										}
									}
								}

								if( $groups !== NULL ) $groups = $user_object->getMyGroups( $groups );

?>

<!DOCTYPE html>
<html>
	<head>
		<title>Месенджер Skillfactory</title>
		
		<!-- CSS -->
		<link href="vendor-front/bootstrap/bootstrap.min.css" rel="stylesheet">
		<link href="vendor-front/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
		<link rel="stylesheet" type="text/css" href="vendor-front/parsley/parsley.css"/>
		<link rel="stylesheet" type="text/css" href="vendor-front/parsley/group.css"/>

		
		<!-- Фавиконка -->
		<link rel="icon" type="image/x-icon" href="vendor-front/bubble-chat.png"> 


		<!-- JavaScript -->
		<script src="vendor-front/jquery/jquery.min.js"></script>
		<script src="vendor-front/bootstrap/js/bootstrap.bundle.min.js"></script>

		<!-- plugin -->
		<script src="vendor-front/jquery-easing/jquery.easing.min.js"></script>

		<script type="text/javascript" language="javascript" src="/extra.js"></script>
		
		<script type="text/javascript" src="vendor-front/parsley/dist/parsley.min.js"></script>
		<style type="text/css">
			

	
		</style>
	</head>
	<body>
		<div class="header">
			<div class="container">
				<!--Логотип-->
				<div class="flex">
				<svg width="200" height="17" viewBox="0 0 938 79" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M30.786 29.391l18.405 2.008c19.632 2.12 29.783 9.928 29.783 23.09 0 15.282-14.055 23.87-39.71 23.87C12.94 78.36 0 69.772 0 52.37h17.959c0 7.586 7.808 11.824 21.751 11.824 14.055 0 20.301-2.566 20.301-9.146 0-4.908-4.462-7.028-12.493-7.92L29.894 45.23C11.489 43.223 2.231 35.749 2.231 22.92 2.231 9.202 17.067.279 38.149.279c23.313 0 37.144 8.924 37.144 23.647H57.78c-.78-6.47-7.138-9.815-19.297-9.815-12.158 0-17.735 2.9-17.735 8.254 0 4.684 3.569 6.358 10.038 7.027zm520.806 33.575c-15.058 0-23.871-8.7-23.871-23.647s8.813-23.648 23.871-23.648c11.712 0 18.628 4.573 21.082 13.609h19.186C589.963 11.99 573.566.278 551.592.278c-26.214 0-43.279 15.393-43.279 39.04 0 23.76 16.954 39.042 43.279 39.042 22.866 0 38.818-12.159 40.491-30.787h-19.186c-1.115 9.705-8.923 15.393-21.305 15.393zm-104.956-16.62l13.712-35.917h.558l13.651 35.917h-27.921zm-.566-44.172l-28.333 74.29h17.402l6.174-16.175h38.543l6.147 16.174H505.3L476.856 2.174H446.07zm-278.083 0h-19.632l-28.89 29.337h-8.7V2.174H93.14v74.29h17.625V45.565h8.7l28.555 30.897h22.868L134.031 36.68l33.956-34.506zm49.749 0h17.625v60.012h40.156v14.277h-57.781V2.174zm87.229 0h-17.624v74.29h57.78V62.185h-40.156V2.174zm69.604 14.278h44.395V2.174h-62.019v74.29h17.624v-27.33h36.81V34.858h-36.81V16.452zM198.774 76.463H181.15V2.174h17.624v74.29zM699.836 2.174h44.968v17.067h-44.968V2.174zm0 17.067v40.156h-18.629V19.24h18.629zm44.968 0h18.628v40.156h-18.628V19.24zm76.52 18.962h-23.983V16.452h23.983c8.254 0 13.385 4.239 13.385 10.931 0 6.693-5.131 10.82-13.385 10.82zm31.679-10.82c0-15.504-12.048-25.209-31.679-25.209h-41.718v74.29h17.735V52.48h17.397l19.971 23.982h21.528l-22.749-27.32c12.323-2.963 19.515-10.729 19.515-21.76zm66.146-25.209l-21.082 33.017-21.082-33.017h-20.524l31.902 48.857v25.432h17.624V51.031L938 2.174h-18.851zM625.881 16.452h-27.886V2.174h73.508v14.278h-27.998v60.011h-17.624V16.452zm118.923 60.011h-44.968V59.397h44.968v17.066z" fill="#fff"></path></svg>
				 <div class="info"><span class="green">Ⓒ Роман Волков</span>  
				 <a class="git" href="https://github.com/rwolfin"><svg height="32" aria-hidden="true" viewBox="0 0 24 24" version="1.1" width="27" data-view-component="true" class="octicon octicon-mark-github v-align-middle">
    <path d="M12 1C5.923 1 1 5.923 1 12c0 4.867 3.149 8.979 7.521 10.436.55.096.756-.233.756-.522 0-.262-.013-1.128-.013-2.049-2.764.509-3.479-.674-3.699-1.292-.124-.317-.66-1.293-1.127-1.554-.385-.207-.936-.715-.014-.729.866-.014 1.485.797 1.691 1.128.99 1.663 2.571 1.196 3.204.907.096-.715.385-1.196.701-1.471-2.448-.275-5.005-1.224-5.005-5.432 0-1.196.426-2.186 1.128-2.956-.111-.275-.496-1.402.11-2.915 0 0 .921-.288 3.024 1.128a10.193 10.193 0 0 1 2.75-.371c.936 0 1.871.123 2.75.371 2.104-1.43 3.025-1.128 3.025-1.128.605 1.513.221 2.64.111 2.915.701.77 1.127 1.747 1.127 2.956 0 4.222-2.571 5.157-5.019 5.432.399.344.743 1.004.743 2.035 0 1.471-.014 2.654-.014 3.025 0 .289.206.632.756.522C19.851 20.979 23 16.854 23 12c0-6.077-4.922-11-11-11Z"></path>
</svg> Мой github</a>  </div>
				</div>
			</div>
		</div>
		<div class="container-fluid">
		<div class="text-right"><h5 class="text-center">Итоговый проект Месенджер (Онлайн чат)</h5></div>
		<br>
			<div class="row justify-beetwen">
				<div id="groupchatlist" class="card mt-3">
					<div class="card mt-3">
						<div class="card-header"><b>Список пользователей</b></div>
						<div class="card-body" id="user_list">
							<div class="list-group list-group-flush">
								<?php
									if (count($user_data) > 0) 
									{
										foreach ($user_data as $key => $user)
										{
											
											$icon = '<i class="fa fa-circle text-danger"></i>'; 

										
											if ($user['user_login_status'] == 'Login')
											{
												$icon = '<i class="fa fa-circle text-success"></i>'; 
											}

										
											if ($user['user_id'] != $login_user_id)
											{
												echo '
													<a onclick="openPM('.$user["user_id"].');" class="list-group-item list-group-item-action">
														<img src="' . $user["user_profile"] . '" class="img-fluid img-thumbnail" width="50" />
														<span class="ml-1"><strong>'. ($user["showemail"] ? $user["user_email"] : $user["user_name"]) . '</strong></span>
														<span class="mt-2 float-right">' . $icon . '</span>
													</a>
												';
											}
										}
									}
								?>
							</div>
						</div>
						</div>
						<div class="card-header"><b>Список групповых чатов</b></div>
						<div class="card-body" id="user_list">
							<div class="list-group list-group-flush">
								<ul class="chatlist list-group">
								<?php
								if( $groups !== NULL ) {
									for( $i = 0; $i < count($groups); $i++ ) {
										echo "<li class=\"list-group-item\" onclick=\"openChat('".$groups[$i][0]["id"]."', '".$login_user_id."');\">".$groups[$i][0]["chatname"]."</li>";
									}
								}
								else {
									echo "<p>Список групповых чатов пуст.</p>";
								}
								?>
								</ul>
							</div>
							<input type="button" onclick="Shownewgrpnl();" class="btn btn-danger mt-2 mb-2" value="Создать гр. чат" />
							<div id="newchat" style="display:none;" class="">
								<input type="text" id="newgroupname" placeholder="Название гр. чата" />
								<input type="button" onclick="Newchat( <?php echo $login_user_id; ?> );" class="btn btn-danger mt-2 mb-2" value="Создать" />
							</div>
						</div>
						</div>
						<div class="card-body" style="display:none;" id="user_list2">
							<div class="list-group list-group-flush">
								<?php
									if (count($user_data) > 0) 
									{
										foreach ($user_data as $key => $user)
										{
											
											$icon = '<i class="fa fa-circle text-danger"></i>'; 

										
											if ($user['user_login_status'] == 'Login')
											{
												$icon = '<i class="fa fa-circle text-success"></i>'; 
											}

										
											if ($user['user_id'] != $login_user_id)
											{
												echo '
													<a class="list-group-item list-group-item-action" onclick="Addusertogc('.$user["user_id"].');">
														<img src="' . $user["user_profile"] . '" class="img-fluid img-thumbnail" width="50" />
														<span class="ml-1"><strong>'. ($user["showemail"] ? $user["user_email"] : $user["user_name"]) . '</strong></span>
														<span class="mt-2 float-right">' . $icon . '</span>
													</a>
												';
											}
											else $profile = $user["user_profile"];
										}
									}
								?>
							</div>
							<!-- <a onclick="Resetview();" href="#">Назад</a> !-->
						</div>
				<iframe id="pmchats" width="60%" src="" style="display:none;"></iframe>
				<div id="chatarea" class="col-lg-8" style="display:none;">
							<div class="card">
								<div class="card-header">
									<div class="row">
										<div id="chatname" class="col col-sm-6">
											<h3>Групповой Чат</h3>
										</div>
										<div class="col col-sm-6 text-right">
											<a href="#" onclick="Showadduserpnl();" class="btn btn-warning btn-sm">Добавить нового пользователя</a>
										</div>
										<!-- <div class="col col-sm-6 text-right">
											<a href="#" onclick="Resetview();" class="btn btn-warning btn-sm">Назад</b></a>
										</div> -->
									</div>
								</div>

							
								<div class="card-body" id="messages_area"> 
								</div>
								
							</div>

							<!-- Форма -->
							<form method="post" id="chat_form" data-parsley-errors-container="#validation_error">
								<div class="input-group mb-3">
									<textarea class="form-control" id="chat_message" name="chat_message" placeholder="Введите ваше сообщение здесь" data-parsley-maxlength="1000"  data-parsley-pattern="/^[a-zA-Zа-яА-ЯёЁ0-9\s\.,!?]+$/" required></textarea>
									<div class="input-group-append">
										<button type="submit" name="send" id="send" class="btn btn-success"><i class="fa fa-paper-plane"></i></button>
									</div>
								</div>

							
								<div id="validation_error"></div>
							</form>
						</div>
					<div class="col-lg-2">
					<div class="profil-bar">						
						<div class="profil-bar">
							<input type="hidden" name="login_user_id" id="login_user_id" value="<?php echo $login_user_id; ?>" />
							<div class="mt-3 mb-3 text-center">
								<img src="<?php echo $profile; ?>" width="250" class="img-fluid img-thumbnail" />
								<h3 class="mt-2"><?php echo $name;?></h3>
								<a href="profile.php" class="btn btn-success mt-2 mb-2">Редактировать мой профиль</a>
								<input type="button" class="btn btn-danger mt-2 mb-2" name="logout" id="logout" value="Выход" /> 
							</div>
						</div>				
				</div>
								</div>
			</div>
		</div>
	</body>
	<script type="text/javascript">
		$(document).ready(function(){
		

		
			var conn = new WebSocket('ws://localhost:8080'); 




		
			conn.onopen = function(e) {
			
				console.log("Connection Established! (Group Chat)");
			};



			conn.onmessage = function(e) {
				var data = JSON.parse(e.data);

				var row_class 		 = '';
				var background_class = '';

			
				if (data.from == 'Me')
				{
					row_class        = 'row justify-content-start';
					background_class = 'text-dark alert-light';

				
					var myNotificationAudioPath = 'vendor-front/sounds/joyous-chime-notification.mp3';
				}
				else
				{
					row_class 	     = 'row justify-content-end';
					background_class = 'alert-success';

				
					var myNotificationAudioPath = 'vendor-front/sounds/light-hearted-message-tone.mp3';
				}

				let myAudio = new Audio(myNotificationAudioPath);
				myAudio.play(); 
				var html_data = 
					`
						<div class='${row_class}'>`;
						if( data.from == 'Me' ) {
							data.from = data.myname;
							html_data += `<div onmouseenter='Showediticons(${data.msgid}, this.children[0]);' onmouseleave='Hideicons( ${data.msgid} );' class='col-sm-10'>`;
						}
						else
							html_data += `<div class='col-sm-10'>`;
								html_data += `<div class='shadow-sm alert ${background_class}'>
									<b>${data.from} - </b><span>${data.msg}</span>
									<br>
									<div class='text-right'>
										<small>
											<i>${data.dt}</i>
										</small>
									</div>
								</div>
							</div>
						</div>
					`
				;

				$('#messages_area').append(html_data); 
				$("#chat_message").val(""); 
			};
 			
			$('#chat_form').parsley();
			$('#messages_area').scrollTop($('#messages_area')[0].scrollHeight); 
			$('#chat_form').on('submit', function(event) { 
				event.preventDefault(); 

				if ($('#chat_form').parsley().isValid()) 
				{
					var user_id = $('#login_user_id').val(); 
					var message = $('#chat_message').val(); 
					var data    = { 
						userId: user_id,
						groupId: grp_id,
						msg   : message  
						
					};
					
					conn.send(JSON.stringify(data)); 
					$('#messages_area').scrollTop($('#messages_area')[0].scrollHeight); 
				}
			});



			
			$('#logout').click(function(){
				user_id = $('#login_user_id').val();

				$.ajax({
					url   : "action.php",
					method: "POST",
					data  :{
						user_id: user_id,
						action : 'leave'
					},
					success:function(data) 
					{
						var response = JSON.parse(data);

						if (response.status == 1) 
						{
							conn.close(); 
							location = 'index.php'; 
						}
					}
				})
			});
		});
	</script>
</html>