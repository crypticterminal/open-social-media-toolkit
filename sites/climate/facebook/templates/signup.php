<?php
	/* FACEBOOK Module Templates for SignUp Page */
	$this->addTemplate('heading','<h1>Join the '.SITE_TITLE.' community on Facebook!</h1><h5>Raise awareness for climate change and support new media research!</h5>');
	$this->addTemplate('whyJoin', 
	'<div id="teamPanel"><div id="teamIcon">'
		.
	'<h1>We can do more, and have more fun doing it! <br /><a href="?p=team" class="more_link">&hellip;&nbsp;Learn more</a></h1>'
      .'<ul class="bullet_list">'
	.'<li>Compete for earth-happy rewards, including a trip to the Arctic</li>'
	.'<li>Earn points for online and offline actions</li>'
	.'<li>Contribute to a research study</li>'
	.'</ul>'
	.'<p>We\'ll even start you off with a 200 point bonus just for joining!</p>'   
	.	'</div><!--end "teamIcon"--></div><!--end "teamPanel"-->');
	
	$this->addTemplate('intro','<div id="introPanel"><p>'.SITE_TITLE.' is part of a <a href="http://www.newscloud.com/research" class="more_link" onclick="quickLog(\'extLink\',\'signup\',0,\'http://www.newscloud.com/research\');" target="_blank">not for profit research study sponsored by the Knight Foundation</a> to find new ways of engaging young people in news readership and community engagement. All ages are welcome but eco-rewards are only available to members aged 16- to 25-years old who are residents of the United States. Participation is purely voluntary. Minors will be required to present consent from parents or guardians to participate and to redeem eco-rewards. Action team not valid where prohibited by law.</p><!-- end of introPanel --></div>');	
?>