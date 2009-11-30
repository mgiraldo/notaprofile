	<div id="cabezote">
		<h1>not_a_profile</h1>
		<ul id="nav">
			<li><a href="/view">keyring</a></li>
			<li><a href="/connections">connections</a></li>
		</ul>
		<div id="userinfo">
		<?php $email = $_SESSION['username'];
		List ($user, $emailCompany) = split("@", $email);
		?>
			user: <?php echo($user);?> | <a href="/logout">log_out</a>
		</div>
	</div>
