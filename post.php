<?php

//ini_set('display_errors', 1); 
//error_reporting(E_ALL);


try {
    $dbuser = 'pippinle_rhok';
    $dbpass = 'rhokto2011';
    $dbh = new PDO('mysql:host=localhost;dbname=pippinle_bikegeist', $dbuser, $dbpass);
} catch (PDOException $e) {
    print "Error!: " . $e->getMessage() . "<br/>";
    die();
}
/*
foreach($dbh->query('SELECT * from FOO') as $row) {
    print_r($row);
}

$preparedStatement = $db->prepare('SELECT * FROM employees WHERE name = :name');
$preparedStatement->execute(array(':name' => $name));
$rows = $preparedStatement->fetchAll();

$preparedStatement = $db->prepare('INSERT INTO table (column) VALUES (:column)');
$preparedStatement->execute(array(':column' => $unsafeValue));

// close connection
$dbh = null;
*/
/*
$q = <<<EOD
CREATE TABLE IF NOT EXISTS posts_test (
  id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR (64),
  timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  plate VARCHAR(16),
  image_url VARCHAR(128),
  mbl_url VARCHAR(128),
  mbl_id INT,
  lat DECIMAL(10,6),
  lon DECIMAL(10,6),
  location VARCHAR(128),
  details TEXT
);
EOD;

echo $q."\n<br />\n";

try {
	$dbh->exec($q);
} catch (PDOException $e) {
    print "Error!: " . $e->getMessage() . "<br/>";
    die();
}

echo 'created.. i think';
*/

//$res = $dbh->query('SELECT count(*) FROM posts_test');
//echo 'count: '.$res->fetchColumn(); 

header('Content-type: text/html; charset=utf-8');



if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	require('class.upload.php');

	$title = $_POST['title'];
	$date = $_POST['date'];
	$location = $_POST['location'];
	$details = $_POST['details'];

	$pid = 1;

	// http://www.verot.net/php_class_upload_docs.htm?PHPSESSID=f3399fae461044faa9da4e7a8a14677c
	$foo = new Upload($_FILES['image']);
	if ($foo->uploaded) {
	  $foo->file_overwrite = true;
	  // save uploaded image with a new name
	  $foo->file_new_name_body = "post$pid-orig";
	  $foo->image_convert = 'jpeg';
	  $foo->Process('img/');
	  if ($foo->processed) {
		//$foo->Clean();
	  } else {
	    echo 'error : ' . $foo->error;
	  }
	  // save uploaded image with a new name,
	  // resized to 100px wide
	  $foo->file_overwrite = true;
	  $foo->file_new_name_body = "post$pid";
	  $foo->image_resize = true;
	  $foo->image_convert = 'jpeg';
	  $foo->image_x = 345;
	  $foo->image_y = 230;
	  //$foo->image_ratio_y = true;
	  $foo->Process('img/');
	  if ($foo->processed) {
	    $foo->Clean();
	  } else {
	    echo 'error : ' . $foo->error;
	  }
	}
	
	?>
<!DOCTYPE html>
<html>
<head></head><body>
<?

echo "title: $title<br />";
echo "date: $date<br />";
echo "location: $location<br />";
echo "details: $details<br />";

?>
<img src="img/post<?= $pid ?>.jpeg" />
</body></html>
	<? 
	exit();
	
} // end post handling



//echo 'json decoded:'.json_decode('{'a':1,'b':2}")."\n";
//echo 'json encoded:'.json_encode(array(1,2,3))."\n";

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Submit Report - Bikegeist</title>
    <link rel="stylesheet" href="bootstrap.css">
	<style media="screen" type="text/css">
    	.defaultText { width: 300px; }
    	.defaultTextActive { color: #a1a1a1; }
		input, textarea, select { color: #000; }
	</style>
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
  </head>
  <body>

	<ul class="breadcrumb" style="font-size:1.1em;margin-bottom:0;">
		<!--<li><a href="/">Home</a> <span class="divider">/</span></li> -->
		<li style="font-weight:bold;color:#333;">Submit a Report</li>
	</ul>
	
  <form enctype="multipart/form-data" method="post" class="form-stacked">
    <fieldset>
      <div class="clearfix">
        <label for="date-val">Title</label>
        <div class="input">
          <input id="title" class="xlarge" name="title" size="30" maxlength="64" type="text">
        </div>
      </div><!-- /clearfix -->
		
	  <div class="clearfix" id="date">
        <label for="date-val">Date</label>
        <div class="input">
          <button type="button" class="btn">Today</button>, <button type="button" class="btn">Yesterday</button> or 
          <input id="date-val" class="small defaultText" name="date" size="30" maxlength="10" type="text" title="YYYY-MM-DD">
        </div>
      </div><!-- /clearfix -->
	  
      <div class="clearfix">
        <label for="location">Address or Intersection</label>
        <div class="input" style="margin-bottom:5px;">
          <input id="location" class="xlarge" name="location" size="30" type="text">
        </div>
		  <button id="detect-location" type="button" class="btn" style="display:none;">Detect Current Location</button>
      </div><!-- /clearfix -->
	  
	  <div class="clearfix">
	  	<label for="image">Photo</label>
		<div class="input" style="margin-bottom:5px;">
		  <input id="image" class="xlarge" type="file" size="23" name="image" value="">
        </div>
	  </div><!-- /clearfix -->
	  
	  <div class="clearfix">
        <label for="details">Details</label>

        <div class="input">
          <textarea class="xlarge" id="details" name="details" rows="3"></textarea>
          <!--<span class="help-block">
            Block of help text to describe the field above if need be.
          </span>-->
        </div>
      </div><!-- /clearfix -->
	  
      
    </fieldset>
    <div class="actions">
      <button type="submit" class="btn primary"> &nbsp; Submit &nbsp; </button>&nbsp;<button id="cancel" type="reset" class="btn">Cancel</button>
    </div>
  </form>
          

<script type="text/javascript">

$('#cancel').click(function(){
	history.go(-1);
	return false;
});

$('#date .btn').click(function(){
	$('#date .btn').removeClass('success');
	$(this).addClass('success');
	//$(this).attr('class', 'btn success');
	var d = new Date();
	function d2(a) {
		return ((''+a).length == 1) ? '0'+a : a;
	}
	if ($(this).html() == 'Today') {
		$('#date-val').val(d.getFullYear() + '-' + (d.getMonth()+1) + '-' + d2(d.getDate()));
	} else {
		d.setDate(d.getDate() - 1)
		$('#date-val').val(d.getFullYear() + '-' + (d.getMonth()+1) + '-' + d2(d.getDate()));
	}
	$("#date .defaultText").removeClass("defaultTextActive");
});
	
$('#detect-location').click(function(){
	navigator.geolocation.getCurrentPosition(function(position) {
	  if (position) $('#location').val(position.coords.latitude +','+ position.coords.longitude);
	  // TODO: reverse geocode to an address or something
	});
});
if (navigator.geolocation) {
	$('#detect-location').show();
}
	
$('#date-val').focus(function(){
	$('#date .btn').removeClass('success');
});
	
$(".defaultText").focus(function(srcc){
    if ($(this).val() == $(this)[0].title){
        $(this).removeClass("defaultTextActive");
        $(this).val("");
    }
});
$(".defaultText").blur(function(){
    if ($(this).val() == ""){
        $(this).addClass("defaultTextActive");
        $(this).val($(this)[0].title);
    }
});
$(".defaultText").blur();
	
/*
 * Auto-growing textareas; technique ripped from Facebook
 * from: https://github.com/jaz303/jquery-grab-bag/blob/master/javascripts/jquery.autogrow-textarea.js
 */
$.fn.autogrow = function(options) {
    this.filter('textarea').each(function() {
        var $this       = $(this),
            minHeight   = $this.height(),
            lineHeight  = $this.css('lineHeight');
        var shadow = $('<div></div>').css({
            position:   'absolute',
            top:        -10000,
            left:       -10000,
            width:      $(this).width() - parseInt($this.css('paddingLeft')) - parseInt($this.css('paddingRight')),
            fontSize:   $this.css('fontSize'),
            fontFamily: $this.css('fontFamily'),
            lineHeight: $this.css('lineHeight'),
            resize:     'none'
        }).appendTo(document.body);
        var update = function() {
            var times = function(string, number) {
                for (var i = 0, r = ''; i < number; i ++) r += string;
                return r;
            };
            var val = this.value.replace(/</g, '&lt;')
                                .replace(/>/g, '&gt;')
                                .replace(/&/g, '&amp;')
                                .replace(/\n$/, '<br/>&nbsp;')
                                .replace(/\n/g, '<br/>')
                                .replace(/ {2,}/g, function(space) { return times('&nbsp;', space.length -1) + ' ' });
            
            shadow.html(val);
            $(this).css('height', Math.max(shadow.height() + 20, minHeight));
        }
        $(this).change(update).keyup(update).keydown(update);
        update.apply(this);
    });
    return this;
}
$('#details').autogrow();

</script>

  </body>
</html>