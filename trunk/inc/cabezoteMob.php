<div id="cabezote">
	<h1>not_a_profile</h1>
	<ul id="nav">
<?php
if(NotAProfile::estaLogeado()){
?>
		<li class="wide"><a href="/m?logout">logout</a></li>
<?php
}
?>
		<li class="wide"><a href="/m/create">create</a></li>
		<li><a href="/m/view">view</a></li>
	</ul>
</div>
