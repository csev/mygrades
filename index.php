<?php
require_once "../config.php";

// The Tsugi PHP API Documentation is available at:
// http://do1.dr-chuck.com/tsugi/phpdoc

use \Tsugi\Core\Settings;
use \Tsugi\Core\LTIX;
use \Tsugi\Util\Net;

// No parameter means we require CONTEXT, USER, and LINK
$LAUNCH = LTIX::requireData();

// Model
$p = $CFG->dbprefix;

$mygrades_form='start';


$lstmt = $PDOX->queryDie(
	"SELECT DISTINCT L.id_sha256 AS id_sha256, L.definition AS definition
	FROM {$p}mygradesActivities AS L
	WHERE L.context_id = :CID",
	array(":CID" => $CONTEXT->id)
);
$links = $lstmt->fetchAll();

$activity_list=[];
if ( $links !== false && count($links) > 0 ) {
	foreach($links as $link) {
		$a_id=addSession('index.php?activity_id='.$link['id_sha256']);
		$a_title=json_decode($link['definition'])->title;
		$activity_list[]=['activityId' => $a_id, 'activityTitle' => $a_title];
	}
}

if ( isset($_GET['addActivity']) && $USER->instructor ) {
	$mygrades_form='addActivity';
	$_SESSION['success'] = __('Add new activity');
}

if ( isset($_POST['cancel']) ) {
	header( 'Location: '.addSession('index.php') ) ;
	return;
}
if ( isset($_POST['newActivityButton']) && $USER->instructor ) {
	$title=$_POST['newActivityTitle'];
	$id=$_POST['newActivityId'];
	if ($id == '') {
		$id=rand();
	}
	$id_sha256 = lti_sha256($id);
	$stmt=$PDOX->queryReturnError("INSERT INTO {$p}mygradesActivities
            (context_id, definition, id,id_sha256)
            VALUES ( :CI, :DE, :ID, :IS )",
            array(
                ':CI' => $CONTEXT->id,
                ':DE' => '{"title": "'.$title.'"}',
                ':ID' => $id,
				':IS' => $id_sha256
            )
        );
	if ($stmt->success) {
		$_SESSION['success'] = __('New activity added');
	} else {
		$_SESSION['error'] = __('URL already used - must be unique.');
	}
	header( 'Location: '.addSession('index.php') ) ;
//    return;
}
//+Experimentell
if ( isset($_GET['activity_id'])) {
	$mygrades_form='activity';
	$activity_id=$_GET['activity_id'];
	$lstmt = $PDOX->queryDie(
		"SELECT definition
		FROM {$p}mygradesActivities
		WHERE context_id = :CID AND id_sha256 = :SID",
		array(":CID" => $CONTEXT->id, ":SID" => $activity_id)
	);
	if ($lstmt->success) {
		$defarray = $lstmt->fetchAll();
		$def=$defarray[0];
		//$a_title=$def
		$a_title=json_decode($def['definition'])->title;
	} else {
		$_SESSION['error'] = __('Activity not found');
	}

}
//-Experimentell

// View
$OUTPUT->header();
$OUTPUT->bodyStart();
$OUTPUT->flashMessages();
$OUTPUT->welcomeUserCourse();

echo("<!-- Handlebars version of the tool -->\n");
echo('<div id="mygrades-div"><img src="'.$OUTPUT->getSpinnerUrl().'"></div>'."\n");

$OUTPUT->footerStart();
$OUTPUT->templateInclude(array('mygrades', 'newActivity', 'activity', 'error'));

if ($USER->instructor) {
	switch ($mygrades_form) {
		case 'start':
			?>
			<script>
			$(document).ready(function(){
					context = {
						'instructor' : true,
						'instructorStart': true,
						'activityList': <?php echo json_encode($activity_list); ?>
					};
					tsugiHandlebarsToDiv('mygrades-div', 'mygrades', context);
			});
			</script>
			<?php
			break;
		case 'addActivity':
			?>
			<script>
			$(document).ready(function(){
					tsugiHandlebarsToDiv('mygrades-div', 'newActivity', {});
			});
			</script>
			<?php
			break;
		case 'activity': 
			?>
			<script>
			$(document).ready(function(){
				context = {
					'aTitle': '<?php echo $a_title; ?>'
				};
				tsugiHandlebarsToDiv('mygrades-div', 'activity', context);
			});
			</script>
			<?php
			break;
		default:
			?>
			<script>
			$(document).ready(function(){
				context = {
						'mygradesForm': <?php echo $mygrades_form; ?>
					};
				tsugiHandlebarsToDiv('mygrades-div', 'error', context);
			});
			</script>
			<?php
	}
} else {
	switch ($mygrades_form) {
		case 'start':
			?>
			<script>
			$(document).ready(function(){
				context = {
						'activityList': <?php echo json_encode($activity_list); ?>
					};
				tsugiHandlebarsToDiv('mygrades-div', 'mygrades', context);
			});
			</script>
			<?php
			break;
		default:
			?>
			<script>
			$(document).ready(function(){
				context = {
						'mygradesForm': '<?php echo $mygrades_form; ?>'
					};
				tsugiHandlebarsToDiv('mygrades-div', 'error', context);
			});
			</script>
			<?php
	}
}

$OUTPUT->footerEnd();
