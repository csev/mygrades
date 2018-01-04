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

$instructor_form='start';


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
	$instructor_form='addActivity';
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

/*
if ( isset($ GET['activity_id']) {

}
*/

// View
$OUTPUT->header();
$OUTPUT->bodyStart();
$OUTPUT->flashMessages();
$OUTPUT->welcomeUserCourse();

echo("<!-- Handlebars version of the tool -->\n");
echo('<div id="mygrades-div"><img src="'.$OUTPUT->getSpinnerUrl().'"></div>'."\n");

$OUTPUT->footerStart();
$OUTPUT->templateInclude(array('mygrades', 'newActivity'));

if ( $USER->instructor && $instructor_form == 'start') {
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


<?php } elseif ( $USER->instructor && $instructor_form == 'addActivity' ) {
?>
<script>
$(document).ready(function(){
        tsugiHandlebarsToDiv('mygrades-div', 'newActivity', {]);
});
</script>
<?php
} else { ?>
<script>
$(document).ready(function(){
	context = {
			'activityList': <?php echo json_encode($activity_list); ?>
		};
    tsugiHandlebarsToDiv('mygrades-div', 'mygrades', context);
});
</script>
<?php
}
$OUTPUT->footerEnd();
