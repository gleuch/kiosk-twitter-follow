<?php
/* 
  Kiosk Twitter Follow
  ------------------------------------------------------
  Simple interface to allow people to quickly follow you on Twitter. Ideal for a kiosk/small laptop, user enters their credentials and it has them auto-follow you. Can optionally have you follow them.

  Written by Greg Leuch <http://gleuch.com>
*/

// Have your account follow them back? (true or false)
define('TWITTER_RETURN_FOLLOWING', true);

// Username (who to follow)
define('TWITTER_USER', 'gleuch');

// User password
define('TWITTER_PASSWORD', '');

/* ------------------------------------------------------ */

?>
<html>
<head>
 <title>Follow @<?php echo TWITTER_USER ?></title>
 <style type="text/css">
  html, body, * {font-family: Helvetica, Arial, Verdana; line-height: 1.0em; font-size: 1.0em; vertical-align: top; letter-spacing: -.02em;}
  body {font-size: 62.5%; background: #c0deed;}
  #content {margin: 40px auto; padding: 0 0 8px 0; width: 475px; text-align: center; background: #fff; border: 1px solid #c0deed; border-radius: 12px; -moz-border-radius: 12px; -webkit-border-radius: 12px; text-align: left;}
  #content .ta_c {text-align: center;}
  #content #titlebar {font-size: 2.6em; margin: 0 0 12px 0; padding: 14px 13px 12px 13px; background: #ddeef6; color: #333; text-align: left; border: 1px solid #ddeef6; border-bottom: none; border-radius-topleft: 12px; -moz-border-radius-topleft: 12px; -webkit-border-radius-topleft: 12px; border-radius-topright: 12px; -moz-border-radius-topright: 12px; -webkit-border-radius-topright: 12px;}
  #content #titlebar h1 {margin: 0; padding: 0;}
  #content h2 {padding: 0 14px; font-size: 2.0em; color: #333;}
  #content p {font-size: 1.6em;}
  #content p.welcome {font-size: 1.8em; color: #333333; padding: 0 14px; line-height: 1.2em;}
  #content p.welcome.error {font-weight: bold;}
  #content p.note {font-size: 1.1em; font-style: italic; color: #666666; padding: 0 14px; text-align: center;}
  #content form {display: block; padding: 0 14px;}
  #content fieldset {border: none; display: block; padding: 4px 0; margin: 0;}
  #content fieldset label {font-size: 1.6em; color: #333;}
  #content fieldset input[type=text], #content fieldset input[type=password] {display: inline-block; font-size: 1.6em; border: 1px solid #CCCCCC; margin: 5px 0 4px 0; padding: 6px 4px; width: 450px; border-radius: 5px; -moz-border-radius: 5px; -webkit-border-radius: 5px; color: #444;}
  #content fieldset.submit {text-align: right; padding: 0 0 4px 0;}
  #content fieldset input[type=submit] {display: inline-block; font-size: 1.6em; border: 1px solid #4a87ad; background: #7bb1cd; margin: 4px 0; padding: 4px 12px 3px 12px; border-radius: 15px; -moz-border-radius: 15px; -webkit-border-radius: 15px; font-weight: bold; color: #fff;}
  #content fieldset input[type=submit]:hover {border-color: #3c6386; cursor: pointer;}
 </style>
</head>
<body>
<div id="content">
  <div id="titlebar">
   <h1>Follow me @<?php echo TWITTER_USER ?></h1>
  </div>
<?php

define('TWITTER_FOLLOW_URL', 'http://api.twitter.com/1/friendships/create.xml'); // Twitter API follow URL


if (is_array($_POST['user']) && !empty($_POST['user']['username']) && !empty($_POST['user']['password']) ):
  $info = twitter_follow(TWITTER_USER, $_POST['user']['username'], $_POST['user']['password']);

  // Return follow if following worked and return follow is active.
  if (($info['http_code'] == '200' || $info['http_code'] == '403') && TWITTER_RETURN_FOLLOW && TWITTER_PASSWORD && TWITTER_PASSWORD != ''): // 403 == error, usually means user already is following TWITTER_USER
    $info_return = twitter_follow($_POST['user']['username'], TWITTER_USER, TWITTER_PASSWORD);
  endif;

  // Display success/error messages
  if ($info['http_code'] == '200'):
?>
    <script type="text/javascript">setTimeout(function() {location.href='<?php echo $_SERVER['PHP_SELF'] ?>';}, 10000);</script>
    <h2 class="ta_c">Congrats @<?php echo $_POST['user']['username'] ?>! You are now following @<?php echo TWITTER_USER ?>!</h2>
  <?php else: ?>
    <script type="text/javascript">setTimeout(function() {location.href='<?php echo $_SERVER['PHP_SELF'] ?>';}, 5000);</script>
    <?php if (strtolower(TWITTER_USER) == strtolower($_POST['user']['username']) ): ?>
      <h2 class="ta_c">You cannot follow yourself! Please try again.</h2>
    <?php elseif ($info['http_code'] == '401'): ?>
      <h2 class="ta_c">Sorry @<?php echo $_POST['user']['username'] ?>, your user credentials are invalid. Please try again to follow @<?php echo TWITTER_USER ?>.</h2>
    <?php else: ?>
      <h2 class="ta_c">Sorry @<?php echo $_POST['user']['username'] ?>, an error occurred when trying to follow @<?php echo TWITTER_USER ?> You might already be following them.</h2>
    <?php endif; ?>
    <p class="note">(You will be redirected to the form momentarily.)</p>
  <?php endif; ?>


<?php 
// The form...
else: ?>
  <?php if (is_array($_POST['user']) && empty($_POST['user']['username'])): ?>
    <p class="welcome error">You must enter your username!</p>
  <?php elseif (is_array($_POST['user']) && empty($_POST['user']['password'])): ?>
    <p class="welcome error">You must enter your password!</p>
  <?php else: ?>
    <p class="welcome">Hello new friend. Enter your Twitter user information to automatically follow me!</p>
  <?php endif; ?>
<form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
  <fieldset><label for="username">Your Twitter Username</label><br /><input type="text" id="username" name="user[username]" value="<?php echo $_POST['user']['username'] ?>" /></fieldset>
  <fieldset><label for="password">Your Twitter Password</label><br /><input type="password" id="password" name="user[password]" value="" /></fieldset>
  <fieldset class="submit"><input type="submit" value="Follow <?php echo TWITTER_USER ?>!" /></fieldset>
</form>
<p class="note">We don't store your user info. We just want to shake hands with you on Twitter!</p>
<?php endif; ?>
</div>
</body>
</html>
<?php


// The main attraction
function twitter_follow($follow, $user, $pass) {
  $data = array(
    'screen_name' => $follow,
    'source' => 'twitterart'
  );

  $groups = array();
  foreach ($data as $k => $v) $groups[] = $k.'='.$v;
  $url = TWITTER_FOLLOW_URL .'?'. implode('&', $groups);

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERPWD, $user .':'. $pass);
  curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
  curl_setopt($ch, CURLOPT_POST, 1);
  // curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
  $result = curl_exec($ch);
  $info = curl_getinfo($ch);
  curl_close($ch);
  return $info;
}


?>