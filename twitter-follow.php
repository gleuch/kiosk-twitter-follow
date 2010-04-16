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
 <title>Follow <?php echo TWITTER_USER ?></title>
</head>
<body>
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
    <h1>Congrats <?php echo $_POST['user']['username'] ?>! You are now following <?php echo TWITTER_USER ?>!</h1>
  <?php else: ?>
    <script type="text/javascript">setTimeout(function() {location.href='<?php echo $_SERVER['PHP_SELF'] ?>';}, 5000);</script>
    <?php if (strtolower(TWITTER_USER) == strtolower($_POST['user']['username']) ): ?>
      <h1>You cannot follow yourself! Please try again.</h1>
    <?php elseif ($info['http_code'] == '401'): ?>
      <h1>Sorry <?php echo $_POST['user']['username'] ?>, your user credentials are invalid. Please try again to follow <?php echo TWITTER_USER ?>.</h1>
    <?php else: ?>
      <h1>Sorry <?php echo $_POST['user']['username'] ?>, an error occurred when trying to follow <?php echo TWITTER_USER ?> You might already be following them.</h1>
    <?php endif; ?>
    <p><em>(You will be redirected to the form momentarily.)</em></p>
  <?php endif; ?>


<?php 
// The form...
else: ?>
  <?php if (is_array($_POST['user'])):
    if (empty($_POST['user']['username'])): ?>
      <p>You must enter your username!</p>
    <?php elseif (empty($_POST['user']['password'])): ?>
      <p>You must enter your password!</p>
    <?php endif; endif; ?>
<form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
  <p><label for="username">Username</label> <input type="text" id="username" name="user[username]" value="<?php echo $_POST['user']['username'] ?>" /></p>
  <p><label for="password">Password</label> <input type="password" id="password" name="user[password]" value="" /></p>
  <p><input type="submit" value="Follow <?php echo TWITTER_USER ?>!" /></p>
</form>

<?php endif; ?>
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