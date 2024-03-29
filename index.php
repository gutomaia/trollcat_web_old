<?php
/**
 * Trollcat 0.1
 * 
 * Meow. x42.
 */

/**
 * Requiring abraham's TwitterOAuth library
 * @link https://github.com/abraham/twitteroauth
 */
require('lib/twitteroauth/twitteroauth.php');
require('lib/recaptcha/recaptchalib.php');
require('config.php');

/** 
 * Trolling function
 */
function troll($messages, $cats) {
	array_reverse($messages);
	
	foreach($messages as $key => $message) {
		if($key == 0) {
			$twitter = new TwitterOAuth(CKEY, CSECRET, $cats['head']['key'], $cats['head']['secret']);
		} elseif($key == sizeof($messages) - 1) {
			$twitter = new TwitterOAuth(CKEY, CSECRET, $cats['tail']['key'], $cats['tail']['secret']);
		} else {
			$twitter = new TwitterOAuth(CKEY, CSECRET, $cats['body']['key'], $cats['body']['secret']);
		}
		
		$tweet = $twitter->post('statuses/update', array('status' => $message));
		
		if($tweet->error) {
			throw new Exception("HTTP Status: {$twitter->http_code} - API Error: {$tweet->error}");
		}
	}
}

/**
 * Form POST processing
 */
if($_POST) {
	$resp = recaptcha_check_answer(RECAPTCHA_PRIVATE, $_SERVER["REMOTE_ADDR"], $_POST["recaptcha_challenge_field"], $_POST["recaptcha_response_field"]);
	if ($resp->is_valid){
		$messages = filter_var_array($_POST['messages'], FILTER_SANITIZE_STRING);
		if(sizeof($messages) == 5) {
			try {
				troll($messages, $cats);
			} catch (Exception $e) {
				echo $e->getMessage();
			}
		}
	}
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Trollcat</title>
	<link type="text/css" rel="stylesheet" href="style.css" media="screen" />
	<script>
	function addMessage() {
		var fields = document.getElementById("fields");
		if (fields.childElementCount <= 10){ 
			var template = document.createElement('p');
			template.innerHTML = "<label>Message:</label> <input type=\"text\" name=\"messages[]\" />";
			fields.appendChild(template);
		}
	}
	</script>
</head>
<body>

	<div class="row">
		<img src="images/cat.png" alt="" />
	</div>
	
	<div class="row main">
		<div class="column grid_8 content">
			<h1>Start trolling</h1>
			<form action="" method="POST">
				<div id="fields">
					<p><label>Message:</label> <input type="text" name="messages[]" /></p>
					<p><label>Message:</label> <input type="text" name="messages[]" /></p>
				</div>
				<p><input type="button" value="Add" onclick="javascript:addMessage()" /></p>
				<?php echo recaptcha_get_html(RECAPTCHA_PUBLIC); ?>
				<p><input type="submit" value="Meow!"></p>
			</form>
		</div>
		<div class="column grid_4 sidebar">
			<h1>Trollcat</h1>
			<p>Trollcat is a small tool for trolling Twitter hashtags with predetermined messages in a stylish feline manner with the aid of our dear friend longcat. Meow here, meow there, meow everywhere.</p>
		</div>
	</div>
	
	<div class="row">
		<div class="column grid_12 footer">
			<p>&copy;2011 All rights trolled. Trollcat is loonger and annoying.</p>
		</div>
	</div>
</body>
</html>
