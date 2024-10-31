<?php
die();
require_once('aweber_api/aweber_api.php');
// Replace with the keys of your application
// NEVER SHARE OR DISTRIBUTE YOUR APPLICATIONS'S KEYS!
$consumerKey    = 'Ak5W1oh3Qf2XbXZKGQ26X6dn';
$consumerSecret = 'B5FtveDSrTKJiZiSxzn7C18tLxZ4QCCSULP3eH2M';
$aweber = new AWeberAPI($consumerKey, $consumerSecret);

if (empty($_COOKIE['accessToken'])) {
    if (empty($_GET['oauth_token'])) {
        $callbackUrl = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
        list($requestToken, $requestTokenSecret) = $aweber->getRequestToken($callbackUrl);
        setcookie('requestTokenSecret', $requestTokenSecret);
        setcookie('callbackUrl', $callbackUrl);
        header("Location: {$aweber->getAuthorizeUrl()}");
        exit();
    }

    $aweber->user->tokenSecret = $_COOKIE['requestTokenSecret'];
    $aweber->user->requestToken = $_GET['oauth_token'];
    $aweber->user->verifier = $_GET['oauth_verifier'];
    list($accessToken, $accessTokenSecret) = $aweber->getAccessToken();
    setcookie('accessToken', $accessToken);
    setcookie('accessTokenSecret', $accessTokenSecret);
    header('Location: '.$_COOKIE['callbackUrl']);
    exit();
}

//$aweber->adapter->debug = true;
$account = $aweber->getAccount($_COOKIE['accessToken'], $_COOKIE['accessTokenSecret']);

function get_lists( $account ) {
	return $account->lists;
}

function fetch_list( $lists, $fetch_by, $value ){
	foreach ( $lists as $list ){
		if( $list->$fetch_by == $value )
			return $list;
	}
	return FALSE;
}

function get_subscriber_collection( $list ){
	return $list->subscribers;
}

function filter_subscribers( $subscriber ){
		if ( isset( $subscriber['status'] ) && $subscriber['status'] == 'subscribed' ){
				return TRUE;
		}
		return FALSE;
}


?><!DOCTYPE html>
<html lang="en">
<head>
  <title>AWeber Test Application</title>
  <link type="text/css" rel="stylesheet" href="styles.css" />
<body>
<?php
/*
$list = fetch_list( $account->lists,  'name', 'lifegoggles' );
$subscribers = $list->subscribers->data['entries'];
$result = $list->subscribers->total_size;
$result = count( array_filter( $subscribers, 'filter_subscribers' ) );
print_r( $result );
*/

foreach( $account->lists as $list ){
	$total_size = $list->subscribers->total_size;
	if( $list->name == 'lifegoggles' ){
		echo '<h3>'.$list->name . '</h3>';
		echo '<p>Total Size: ' . $list->subscribers->total_size . '</p>';
		$subscribers = $list->subscribers->data['entries'];
		$status = array();
		foreach( $subscribers as $subscriber ){
			print_r( $subscriber );
			$status[] = $subscriber['status'];
		}
		//print_r( $status );
		print_r( array_count_values($status) );
		//echo '<p>Actual Size: ' . count( $subscribers ) . '</p>';
		break;
	}
	
}
?>
</body>
</html>