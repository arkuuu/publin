<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>BibTeX Import/Export Test</title>
</head>
<body>

	<?php

	if ($_SERVER['REQUEST_METHOD'] == 'POST') {

		$bibtex = trim($_POST['bibtex']);

	} else {
		$bibtex = '';
	}

	?>

	<h1>Input BibTeX data:</h1>

	<form action="index.php" method="post">
	<textarea name="bibtex" cols="80" rows="10"><?php echo stripslashes(htmlspecialchars($bibtex, ENT_QUOTES)); ?></textarea><br/>
	<input type="submit" value="Import"/>
	</form>

	
	<?php

	require_once 'Bibtex.php';
	// while testing
	// error_reporting(E_ALL & ~E_NOTICE);

	if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($bibtex)) {

		$result = Bibtex::import($bibtex);

		echo "<h1>Imported meta data:</h1>";
		echo '<ul>';

		foreach ($result as $key => $value) {
			if ($key == 'author') {
				echo '<li><i>authors:</i><ul>';
				foreach ($value as $author) {
					echo '<li><i>given:</i> '.$author['given'].'<br/>';
					echo '<i>family:</i> '.$author['family'].'</li>';
				}
				echo '</ul></li>';
			}
			else if ($key == 'keywords') {
				echo '<li><i>keywords:</i><ul>';
				foreach ($value as $keyword) {
					echo '<li>'.$keyword.'</li>';
				}
				echo '</ul></li>';
			}
			else {
				echo '<li><i>'.$key.':</i> '.$value.'</li>';
			}
		}
		
		echo '</ul><br/><br/>';	

		echo "<h1>Export of the imported meta data:</h1>";
		echo '<textarea rows="10" cols="80" readonly>';
		echo $result = Bibtex::export($result);
		echo '</textarea>';
	}

	?>
</body>
</html>
