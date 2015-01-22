<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>BibTeX Import/Export Test</title>
</head>
<body>

	<?php

	if ($_SERVER['REQUEST_METHOD'] == 'POST') {

		$input = trim($_POST['input']);

	} else {
		$input = '';
	}

	?>

	<h1>Input BibTeX data:</h1>

	<form action="index.php" method="post">
	<textarea name="input" cols="100" rows="15"><?php echo stripslashes(htmlspecialchars($input, ENT_QUOTES)); ?></textarea><br/>
	<input type="submit" value="Import"/>
	</form>

	
	<?php

	require_once 'Bibtex.php';
	// while testing
	// error_reporting(E_ALL & ~E_NOTICE);

	if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($input)) {

		$bibtex = new Bibtex();

		$result = $bibtex -> import($input);

		echo "<h1>Imported meta data:</h1>";
		echo '<pre>';
		print_r($result);	
		echo '</pre><br/>';	

		echo "<h1>Export of the imported meta data:</h1>";
		echo '<textarea rows="15" cols="100" readonly>';
		echo $result = $bibtex -> export($result);
		echo '</textarea>';
	}

	?>
</body>
</html>
