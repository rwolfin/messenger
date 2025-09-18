<?php



session_start();

if (!isset($_SESSION['user_data'])) 
{
	header('location:index.php');
}


require('database/ChatUserModel.php');
require('database/GroupChatMessageModel.php');
?>

<!DOCTYPE html>
<html>
	<head>
		<title>Skillfactory</title>

		<!--Кодировка-->
		<meta charset="UTF-8">

		<!-- CSS -->
		<link href="vendor-front/bootstrap/bootstrap.min.css" rel="stylesheet">
		<link href="vendor-front/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
		<link rel="stylesheet" type="text/css" href="vendor-front/parsley/parsley.css"/>
		<!-- HTML Favicon -->
		<link rel="icon" type="image/x-icon" href="vendor-front/bubble-chat.png"> 

		<!-- JavaScript -->
		<script src="vendor-front/jquery/jquery.min.js"></script>
		<script src="vendor-front/bootstrap/js/bootstrap.bundle.min.js"></script>

		<!-- plugin -->
		<script src="vendor-front/jquery-easing/jquery.easing.min.js"></script>
		<script type="text/javascript" src="vendor-front/parsley/dist/parsley.min.js"></script>
		<script type="text/javascript" language="javascript" src="/extrapm.js"></script>
	    <link rel="stylesheet" type="text/css" href="vendor-front/parsley/group.css"/>
		<style type="text/css">
			html,
			body {
			height: 100%;
			width: 100%;
			margin: 0;
			}
			#wrapper
			{
				display: flex;
				flex-flow: column;
				height: 100%;
			}
			#remaining
			{
				flex-grow : 1;
			}
			#messages {
				height: 200px;
				background: whitesmoke;
				overflow: auto;
			}
			#chat-room-frm {
				margin-top: 10px;
			}
			#user_list
			{
				height:450px;
				overflow-y: auto;
			}

			#messages_area
			{
				height: 75vh;
				overflow-y: auto;
				/*background-color:#e6e6e6;*/
				/*background-color: #EDE6DE;*/
			}

		</style>
	</head>
	<body>
		<div class="container-fluid">
			
			<div class="row">

				<div class="col-lg-3 col-md-4 col-sm-5" style="background-color:  #f1f1f1; display:none; height: 100vh; border-right:1px solid #ccc;">
					<?php
						$login_user_id = '';
						$token = '';

						foreach ($_SESSION['user_data'] as $key => $value)
						{
							$login_user_id = $value['id'];
							$token = $value['token'];
					?>
							<input type="hidden" name="login_user_id" id="login_user_id" value="<?php echo $login_user_id; ?>" />
							<input type="hidden" name="is_active_chat" id="is_active_chat" value="No" /> 
					<?php
						}
					?>



					<?php
						$user_object = new ChatUserModel;
						$user_object->setUserId($login_user_id);
						$user_data = $user_object->get_user_all_data_with_status_count();
							$udata = array();
							$edata = array();
							foreach ($user_data as $key => $user)
							{
								$udata[$user["user_id"]] = $user["user_name"];
								$edata[$user["user_id"]] = array( "show" => $user["showemail"], "email" => $user["user_email"] );
							}
							echo '<script type="text/javascript">
									var uData = '.json_encode($udata).';
									var eData = '.json_encode($edata).';
								</script>';
						?>
				</div>
				
				<div class="col-lg-12 col-md-8 col-sm-7">
					<div id="chat_area"></div> 
				</div>
				
			</div>
		</div>
	</body>
	<script type="text/javascript">
		$(document).ready(function(){
		

			var receiver_userid = ''; 


	
			var conn = new WebSocket('ws://localhost:8080?token=<?php echo $token; ?>'); 

			conn.onopen = function(event)
			{
		
				console.log('Connection Established! (One-to-One/Private Chat)');
			};



			
			conn.onmessage = function(event)
			{
				var data = JSON.parse(event.data); 

				
				if (data.status_type == 'Online') 
				{
					$('#userstatus_' + data.user_id).html('<i class="fa fa-circle text-success"></i>'); 
				}
				else if (data.status_type == 'Offline') 
				{
					$('#userstatus_' + data.user_id).html('<i class="fa fa-circle text-danger"></i>'); 
				}
				else 
			
				{
					var row_class 		 = '';
					var background_class = '';

					if (data.from == 'Me')
					{
						row_class 		 = 'row justify-content-start';
						background_class = 'alert-primary';
						//data.from = data.myname;
					
						var myNotificationAudioPath = 'vendor-front/sounds/mixkit-clear-announce-tones-2861.wav';
					}
					else 
					{
						row_class		 = 'row justify-content-end';
						background_class = 'alert-success';

						
						var myNotificationAudioPath = 'vendor-front/sounds/mixkit-arabian-mystery-harp-notification-2489.wav';
					}

					if( eData[ data["userId"] ][ "show" ] ) data.myname = eData[ data["userId"] ][ "email" ];
					else if( !data.myname || data.myname === undefined ) data.myname = data.from;
					
					let myAudio = new Audio(myNotificationAudioPath);
					myAudio.play(); 


				
					if (receiver_userid == data.userId || data.from == 'Me') 
					{
						if ($('#is_active_chat').val() == 'Yes') 
						{
							var html_data = `
								<div class="${row_class}">
									<div class="col-sm-10">
										<div class="shadow-sm alert ${background_class}">
											<b>${data.myname} - </b>${data.msg}<br />
											<div class="text-right">
												<small>
													<i>${data.datetime}</i>
												</small>
											</div>
										</div>
									</div>
								</div>
							`;

							$('#messages_area').append(html_data); 
							$('#messages_area').scrollTop($('#messages_area')[0].scrollHeight); 
							$('#chat_message').val(""); 
						}
					}
					else 
					{
						var count_chat = $('#userid' + data.userId).text();

						if (count_chat == '')
						{
							count_chat = 0;
						}

						count_chat++; 


						$('#userid_' + data.userId).html('<span class="badge badge-danger badge-pill">' + count_chat + '</span>'); 
					}
				}
			};



			conn.onclose = function(event) 
			{
				console.log('Connection Closed!');
			};



			function make_chat_area(userid) 
			{
				let user_name = uData[userid];
				if( eData[userid]["show"] ) user_name = eData[userid]["email"];
				var html = `
					                    <div class="card">
						<div class="card-header">
							<div class="row">
								<div class="col col-sm-6">
									<b>Приватный чат с: <span class="text-danger" id="chat_user_name">${user_name}</span></b>
								</div>
							</div>
						</div>
						<div class="card-body" id="messages_area"> 
						</div>
					</div>

 
					<br />
					<form id="chat_form" method="POST" data-parsley-errors-container="#validation_error">
						<div class="input-group mb-3" style="height:7vh">
							<textarea class="form-control" id="chat_message" name="chat_message" placeholder="ваше сообщение" data-parsley-maxlength="1000" data-parsley-pattern="[a-zA-Zа-яА-ЯёЁ0-9 ?!,.():-]+" required></textarea>
							<div class="input-group-append">
								<button type="submit" name="send" id="send" class="btn btn-primary"><i class="fa fa-paper-plane"></i></button>
							</div>
						</div>

						<div id="validation_error"></div>
						<br />
					</form>
				
				`;


				$('#chat_area').html(html); 
				$('#chat_form').parsley(); 
			}

			function get(name){
				if(name=(new RegExp('[?&]'+encodeURIComponent(name)+'=([^&]*)')).exec(location.search))
					return decodeURIComponent(name[1]);
			}

			if( get("id") !== undefined ) {
				receiver_userid = get("id");
				var from_user_id = $('#login_user_id').val(); 
				var receiver_user_name = $('#list_user_name_' + receiver_userid).text(); 

				$('.select_user.active').removeClass('active'); // Remove the .active CSS class from any other previously selected user (to add it on the newly selected user)
				$(this).addClass('active'); // Add the .active CSS on the newly selected user

				make_chat_area(receiver_userid); // Show the chat area with the selected user
				$('#is_active_chat').val('Yes'); // Change the private chat area with a user from 'No' to 'Yes' to be used for different purposes


				// Fetch the private Chat History of the authenticated/logged-in user with the selected user from the `private_chat_messages` database table using AJAX
				$.ajax({
					url     :"action.php",
					method  :"POST",
					data    :{
						action      :'fetch_chat',
						to_user_id  : receiver_userid, // The selected user (by the authenticated/logged-user) i.e. the receiver
						from_user_id: from_user_id     // The authenticated/logged-in user i.e. the sender
					},
					dataType:"JSON",
					success:function(data) // 'data' is the response (Chat History between the two users) from the server/server side/backend (action.php)
					{
						if (data.length > 0) // If there's a chat history between the authenticated/logged-in user and the selected user
						{
							var html_data = '';

							for (var count = 0; count < data.length; count++)
							{
								var row_class        = ''; 
								var background_class = '';
								var user_name        = '';

								if (data[count].from_user_id == from_user_id) // If the Chat History message is sent by the authenticated/logged-in user to the selected user, show it on the left side of the chat area
								{
									row_class        = 'row justify-content-start';
									background_class = 'alert-light';
									//user_name        = 'Me';
								}
								else // If the Chat History essage is sent by the selected user to the authenticated/logged-in user, show it on the right side of the chat area
								{
									row_class = 'row justify-content-end';
									background_class = 'alert-warning';
								}

								if( eData[ data[count]["from_user_id"] ][ "show" ] ) user_name = eData[ data[count]["from_user_id"] ][ "email" ];
								else user_name = data[count].from_user_name;

								// Display/Show the Chat History message's sender name, message itself, its timestamp, and some CSS classes
								html_data += `
									<div class="${row_class}">`;
									if( data[count].from_user_id == from_user_id )
										html_data += `<div onmouseenter="Showediticons('${data[count].chat_message_id}', this.children[0]);" onmouseleave="Hideicons( '${data[count].chat_message_id}' );" class="col-sm-10">`;
									else
										html_data += `<div class="col-sm-10">`;
											html_data += `<div class="shadow alert ${background_class}">
												<b>${user_name} - </b>
												<span>${data[count].private_chat_message}</span><br />
												<div class="text-right">
													<small><i>${data[count].timestamp}</i></small>
												</div>
											</div>
										</div>
									</div>
								`;
							}

							$('#userid_' + receiver_userid).html(''); // Remove the 'Unread' messages count/number red-colored Push Notification (because the message has been opened (seen) then)
							$('#messages_area').html(html_data); // Display/Show the Chat History messages between the authenticated/logged-in user and the selected user
							$('#messages_area').scrollTop($('#messages_area')[0].scrollHeight); // Scroll to the bottom of the private chat area to show latest messages (after displaying/showing the History Chat message/s)
						}
					}
				});
			}



			// Close the private chat area with a user when clicking on the x button (on the far right side of the private chat area)
			$(document).on('click', '#close_chat_area', function(){
				$('#chat_area').html(''); // Remove the contents of the Private Chat area
				$('.select_user.active').removeClass('active'); // Remove the .active CSS class
				$('#is_active_chat').val('No'); // Convert the value from 'Yes' to 'No'

				receiver_userid = ''; // Empty the receiver user's variable
			});



			// Handling 'One-to-One/Private' Chat HTML Form Submission (sending messages to a one particular/specific user, NOT all users as with 'Group' Chat) (Handling sending chat messages to the onMessage() method of the custom WebSocket handler Chat.php class)
			$(document).on('submit', '#chat_form', function(event){
				event.preventDefault(); // Prevent actual HTML Form submission to avoid page refresh which can ruin user experience (i.e. Prevent form submission by HTML. JavaScript will handle form submission.)

				if ($('#chat_form').parsley().isValid()) // If the One-to-One/Private Chat HTML Form submitted data passes Parsley library validation
				{
					var user_id = parseInt($('#login_user_id').val()); // The authenticated/logged-in user's `user_id` in `chat_application_users` table
					var message = $('#chat_message').val(); // The submitted Private Chat message

					var data = { // This 'data' object will be sent to the onMessage() method of the custom WebSocket handler Chat.php Class)
						userId         : user_id,
						msg            : message,
						receiver_userid:receiver_userid,
						command        :'private' // We send this    command: 'private'    key-value pair to signal that this is a ONE-TO-ONE/PRIVATE Chat message, not a Group Chat message, to the onMessage() method in Chat.php Class
					};

					conn.send(JSON.stringify(data)); // Send the One-to-One/Private Chat message via WebSocket to the onMessage() method of our custom WebSocket handler Chat.php class in the backend    // Convert the JavaScript Object to a JSON string (to send it to the server (our custom WebSocket handler Chat.php class))
				}

			});



			// Logout (When the Logout button is clicked (the button is in this file)) (N.B. This updates the `user_login_status` column of the `chat_application_users` database table from 'Login' to 'Logout')
			$('#logout').click(function(){
				user_id = $('#login_user_id').val();

				$.ajax({
					url   :"action.php",
					method:"POST",
					data  : {
						user_id: user_id,
						action : 'leave'
					},
					success:function(data) // 'data' is the response from the server (server-side/backend). It contains the 'status' key. Check the first if condition in action.php
					{
						var response = JSON.parse(data);

						if (response.status == 1) // 'data' is the response from the server (server-side/backend). It contains the 'status' key. Check the first if condition in action.php
						{
							conn.close(); // Closes the WebSocket connection
							location = 'index.php'; // Redirect the user to the Login Page (index.php) after logging out
						}
					}
				})
			});
		})
	</script>

	
</html>