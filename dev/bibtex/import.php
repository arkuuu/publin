<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>BibTeX import</title>
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

	<form action="import.php" method="post">
	<table border="0" cellpadding="0" cellspacing="5">
		<tr>
			<td valign="top">BibTeX data:</td>
			<td>
				<textarea name="bibtex" cols="50" rows="10"><?php echo htmlspecialchars($bibtex, ENT_QUOTES); ?></textarea>
			</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td><input type="submit" value="Import"/></td>
		</tr>	
	</table>
	</form>

	
	<?php

	if ($_SERVER['REQUEST_METHOD'] == 'POST') {

		// get rid off too many whitespaces and newlines
		$bibtex = preg_replace('/\s+/', ' ', $bibtex);

		// get the type and citeKey
		preg_match('/\@(article|book|masterthesis|phdthesis|unpublished)\s?(\"|\{)([^\"\},]*),/i', $bibtex, $typeReg);
		$type = htmlspecialchars($typeReg[1], ENT_QUOTES);
		$citeKey = htmlspecialchars($typeReg[3], ENT_QUOTES);

		// get the author
		preg_match('/author\s?=\s?(\"|\{)([^\"\}]*)(\"|\})/i', $bibtex, $authorReg);
		$author = htmlspecialchars($authorReg[2], ENT_QUOTES);

		// get the title
		preg_match('/\btitle\s?=\s?(\"|\{)([^\"\}]*)(\"|\})/i', $bibtex, $titleReg);
		$title = htmlspecialchars($titleReg[2], ENT_QUOTES);

		// get the journal
		preg_match('/journal\s?=\s?(\"|\{)([^\"\}]*)(\"|\})/i', $bibtex, $journalReg);
		$journal = htmlspecialchars($journalReg[2], ENT_QUOTES);

		// get the year
		preg_match('/year\s?=\s?(\"|\{)([^\"\}]*)(\"|\})/i', $bibtex, $yearReg);
		$year = htmlspecialchars($yearReg[2], ENT_QUOTES);

		// get the volume
		preg_match('/volume\s?=\s?(\"|\{)([^\"\}]*)(\"|\})/i', $bibtex, $volumeReg);
		$volume = htmlspecialchars($volumeReg[2], ENT_QUOTES);



		echo "<h1>Imported meta data:</h1>";

		echo '
		<table border="0" cellpadding="0" cellspacing="4" >
			<tr>
				<td>type:</td>
				<td>'.$type.'</td>
			</tr>
			<tr>
				<td>citeKey:</td>
				<td>'.$citeKey.'</td>
			</tr>
			<tr>
				<td>author:</td>
				<td>'.$author.'</td>
			</tr>
			<tr>
				<td>title:</td>
				<td>'.$title.'</td>
			</tr>
			<tr>
				<td>journal:</td>
				<td>'.$journal.'</td>
			</tr>
			<tr>
				<td>year:</td>
				<td>'.$year.'</td>
			</tr>
			<tr>
				<td>volume:</td>
				<td>'.$volume.'</td>
			</tr>	
		</table>';

	}

	?>
</body>
</html>