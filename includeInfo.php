<?php
if(isset($_GET) && !empty($_GET)) {
	if(isset($_GET['tutorial']) && $_GET['tutorial'] != '') {
		$tutorial = $_GET['tutorial'];
	}
}
if($tutorial==="library"){ ?>
<div class="slider">
	<div><img src="./img/tutorial/library1.jpg" /></div>
	<div><img src="./img/tutorial/library2.jpg" /></div>
	<div><img src="./img/tutorial/library3.jpg" /></div>
	<div><img src="./img/tutorial/library4.jpg" /></div>
	<div>
		<br><br><br>
		<h2 style="text-align:center;color:white;">
			<a href="#" class="btn btn-success" onclick="parent.Modal.close();return false;"><span style="font-weight:bold;font-size:15pt;">Start Uploading Media Now</span></a> or watch our getting started video below:
		</h2>
		<br /><br />
		<div id="videocontainer">
			<center>
				<iframe width='640' height='360' src='https://acorn.webapps.csus.edu/EmbedMedia.php?media=77b493731fac39d18717a850413e64296973ceb2' frameborder='0' allowfullscreen=''></iframe>
			</center>
		</div>	
	</div>
</div>

<?php } ?>
<style>
.slider img {
	height:90%;
	margin:2.5% auto;
	max-width:80%;
}
</style>
<link rel="stylesheet" type="text/css" href="./css/style.css">
<script type="text/javascript" src="./js/jquery-1.11.3.min.js"></script>
<script type="text/javascript" src="./js/slick.min.js"></script>
<script type="text/javascript">
	$(document).ready(function(){
		$('.slider').slick({
		  infinite: false,
		  slidesToShow: 1,
		  dots: true
		});
	});
</script>