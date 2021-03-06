<?php
//require_once('../../sites/climates/constants.php');
require_once(PATH_CORE.'/classes/dbConsoleModel.class.php');
//ini_set('display_errors', 'off');

set_magic_quotes_runtime(0);

session_start();

//if (!$_SESSION['authed'] && (isset($_GET['e']) && isset($_GET['a']))) {
if (defined('NO_SECURITY') AND NO_SECURITY) {
	$_SESSION['authed']	= true;
	$_SESSION['email']	= ADMIN_EMAIL;
	$_SESSION['userid']	= 1;
	$_SESSION['name'] 	= 'Installer';
	$_SESSION['role'] = 'admin';	
} else if (!$_SESSION['authed']) {
	$errors = array();
	if (isset($_GET['e']) && $_GET['e'] != '')
		$email = $_GET['e'];
	else
		$errors[] = 'Invalid email';
	if (isset($_GET['a']) && $_GET['a'] != '') {
		$act = $_GET['a'];
		// placed to fix facebook link bug introduced on Apr 22, 09 which added a slash
		$act=trim($act,'/');
		
	}
	else
		$errors[] = 'Invalid activation code.';
	if (count($errors)) {
		echo '<html><head><title>Invalid authentication</title><meta http-equiv="refresh" content="10;url='.URL_CANVAS.'"></head><body>';

		foreach ($errors as $error)
			echo "<h1>ERROR: $error</h1>";
		echo '<h1 style="color: red;">Authorization has failed. Redirecting you to the '.SITE_TITLE.' app in 10 seconds, or <a href="'.URL_CANVAS.'">click here</a> to return immediately.</h1>';
		echo "</body></html>";
		exit;
	}
	$db = new dbConsoleModel('all');
	$result = $db->query("SELECT ncUid, userid, name, email, isAdmin, isResearcher, isSponsor, isModerator from User WHERE email = '".mysql_real_escape_string($email)."'");
	if ($row = mysql_fetch_assoc($result)) {
		$ncUid = $row['ncUid'];
		$e = $row['email'];
		$u = $row['userid'];
		$n = $row['name'];
		$isAdmin = $row['isAdmin'];
		$isResearcher = $row['isResearcher'];
		$isSponsor = $row['isSponsor'];
		$isModerator = $row['isModerator'];
	}
	$activation_code = crypt($ncUid, $email);
	$activation_code .= 'c';
	$activation_code = str_replace('/', '', $activation_code);

	if ($activation_code == $act) {
		$_SESSION['authed']	= true;
		$_SESSION['email']	= $e;
		$_SESSION['userid']	= $u;
		$_SESSION['name'] 	= $n;
		if ($isAdmin == 1) {
			$_SESSION['role'] = 'admin';
			if ($n == 'Jeff Reifman' || $n == 'Russell Branca')
				$_SESSION['curr_site_id'] = 0;
		} else if ($isResearcher == 1) {
			$_SESSION['role'] = 'researcher';
			$_SESSION['curr_site_id'] = 0;
		} else if ($isSponsor == 1) {
			$_SESSION['role'] = 'sponsor';
		} else if ($isModerator == 1) {
			$_SESSION['role'] = 'moderator';
		} else {
			$_SESSION['role'] = 'default';
		}
	} else {
		$_SESSION['authed'] = false;
		// TODO: BLOW UP!!!!
		echo '<h1 style="color: red;">Authorization has failed. Redirecting you to the '.SITE_TITLE.' app in 10 seconds, or <a href="'.URL_CANVAS.'">click here</a> to return immediately.</h1>';
		session_destroy();
		exit;
	}
}

if (get_magic_quotes_gpc()) {
       function stripslashes_deep($value) {
               $value = is_array($value) ? array_map('stripslashes_deep', $value) :
					stripslashes($value);
               return $value;
       }

       $_POST = array_map('stripslashes_deep', $_POST);
       $_GET = array_map('stripslashes_deep', $_GET);
       $_COOKIE = array_map('stripslashes_deep', $_COOKIE);
}

init_session();

if (isset($_GET['logout']) && $_GET['logout']) {
	session_destroy();
}
/*
if (isset($_GET['logout']) && $_GET['logout']) {
	session_destroy();
} else if($_SESSION['authed'] != true) {
  if($_POST['username'] == 'admin' and $_POST['password'] == 'asdffdsa') $_SESSION['authed'] = true;
  else header('Location: index.php?auth_failed=true');
}
*/

function get_flash() {
	$tmp_flash = array('notice' => $_SESSION['flash_notice'], 'error' => $_SESSION['flash_error']);
	reset_flash();
	return $tmp_flash;
}

function reset_flash() {
	$_SESSION['flash_notice'] = '';
	$_SESSION['flash_error'] = '';
}

function set_flash($flash_array) {
	if (isset($flash_array['notice']) && $flash_array['notice'] != '')
		$_SESSION['flash_notice'] = $flash_array['notice'];
	if (isset($flash_array['error']) && $flash_array['error'] != '')
		$_SESSION['flash_error'] = $flash_array['error'];
}

function init_session() {
	if (!isset($_SESSION['flash_notice'])) $_SESSION['flash_notice'] = '';
	if (!isset($_SESSION['flash_error'])) $_SESSION['flash_error'] = '';
}

function init_db($ctrl = 'main', $action = 'index') {
	$db = false;
	switch ($ctrl) {
		case 'stories':
			if (preg_match('/widget/i', $action)) {
					$db = new dbConsoleModel('Widgets', array(), 'id');
			} else if (preg_match('/story/i', $action)) {
					$db = new dbConsoleModel('Content', array(), 'siteContentId');
			} else if (preg_match('/comment/i', $action)) {
					$db = new dbConsoleModel('Comments', array(), 'siteCommentId');
			} else if (preg_match('/video/i', $action)) {
					$db = new dbConsoleModel('Videos', array(), 'id');
			} else if (preg_match('/feed/i', $action)) {
					$db = new dbConsoleModel('Feeds', array(), 'id');
			} else {
				$db = new dbConsoleModel('all');
			}
		break;
		case 'research':
			$db = new dbConsoleModel('all');
		break;
		case 'admin':
			if (preg_match('/cronjobs/i', $action)) {
					$db = new dbConsoleModel('cronJobs');
			} else {
				$db = new dbConsoleModel('all');
			}
		break;
		case 'street_team':
			if (preg_match('/prize/i', $action)) {
				$db = new dbConsoleModel('Prizes');
			} else if (preg_match('/completed_challenge/i', $action)) {
				$db = new dbConsoleModel('ChallengesCompleted');
			} else if (preg_match('/challenge/i', $action)) {
				$db = new dbConsoleModel('Challenges');
			} else if (preg_match('/order/i', $action)) {
				$db = new dbConsoleModel('Orders');
			} else {
				$db = new dbConsoleModel('all');
			}
		break;
		case 'main':
			if (preg_match('/index/i', $action)) {
				$db = new dbConsoleModel('all');
			} else {
				$db = new dbConsoleModel('all');
			}
		break;
		case 'members':
			if (preg_match('/member_email/i', $action)) {
				$db = new dbConsoleModel('ContactEmails');
			} else if (preg_match('/member/i', $action)) {
				$db = new dbConsoleModel('User', array(), 'userid');
			} else if (preg_match('/outboundmessage/i', $action)) {
				$db = new dbConsoleModel('OutboundMessages');
			} else if (preg_match('/forumtopic/i', $action)) {
				$db = new dbConsoleModel('ForumTopics');
			} else if (preg_match('/folderlink/i', $action)) {
				$db = new dbConsoleModel('FolderLinks');
			} else if (preg_match('/folder/i', $action)) {
				$db = new dbConsoleModel('Folders');
			} else if (preg_match('/card/i', $action)) {
				$db = new dbConsoleModel('Cards');
			} else {
				$db = new dbConsoleModel('all');
			}
		break;
		default:
			$db = new dbConsoleModel('all');
		break;
	}

	return $db;
}

?>
