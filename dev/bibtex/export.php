<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>BibTeX export</title>
</head>
<body>

	<?php

	// No input validation necessary, as data comes from server later!
	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		$type = htmlspecialchars($_POST['type'], ENT_QUOTES);
		$citeKey = htmlspecialchars($_POST['citeKey'], ENT_QUOTES);
		$author = htmlspecialchars($_POST['author'], ENT_QUOTES);
		$title = htmlspecialchars($_POST['title'], ENT_QUOTES);
		$year = htmlspecialchars($_POST['year'], ENT_QUOTES);
		$journal = htmlspecialchars($_POST['journal'], ENT_QUOTES);
		$volume = htmlspecialchars($_POST['volume'], ENT_QUOTES);
	} else {
		$type = '&nbsp;';
		$citeKey = '&nbsp;';
		$author = '&nbsp;';
		$title = '&nbsp;';
		$year = '&nbsp;';
		$journal = '&nbsp;';
		$volume = '&nbsp;';
	}
	?>

	<h1>Input meta data:</h1>

	<form action="export.php" method="post">
	<table border="0" cellpadding="0" cellspacing="4" >
		<tr>
			<td>type:</td>
			<td>
				<select name="type" size="1">
				 	<option>article</option>
				 	<option>book</option>
				 	<option>masterthesis</option>
				 	<option>phdthesis</option>
				 	<option>unpublished</option>
				</select>
			</td>
		</tr>
		<tr>
			<td>citeKey:</td>
			<td><input name="citeKey" type="text" value="<?php echo $citeKey; ?>"/></td>
		</tr>
		<tr>
			<td>author:</td>
			<td><input name="author" type="text" value="<?php echo $author; ?>"/></td>
		</tr>
		<tr>
			<td>title:</td>
			<td><input name="title" type="text" value="<?php echo $title; ?>"/></td>
		</tr>
		<tr>
			<td>journal:</td>
			<td><input name="journal" type="text" value="<?php echo $journal; ?>"/></td>
		</tr>
		<tr>
			<td>year:</td>
			<td><input name="year" type="text" value="<?php echo $year; ?>"/></td>
		</tr>
		<tr>
			<td>volume:</td>
			<td><input name="volume" type="text" value="<?php echo $volume; ?>"/></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td><input type="submit" value="Export"/></td>
		</tr>	
	</table>
	</form>

	
	<?php

	// show bibtex data if form was send
	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		
		echo "<h1>Exported BibTeX:</h1>";


		$bibtex = 	
	"<pre>@".$type."{".$citeKey.",
		author = {".$author."},
		title = {".$title."},
		journal = {".$journal."},
		volume = {".$volume."},
		year = {".$year."},
	}</pre>";

		echo $bibtex;
	}

	?>
</body>
</html>