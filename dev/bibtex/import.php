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
				<textarea name="bibtex" cols="50" rows="10"><?php echo stripslashes(htmlspecialchars($bibtex, ENT_QUOTES)); ?></textarea>
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
		$bibtex = stripslashes($bibtex);
		$bibtex = str_replace('{\"u}', 'ü', $bibtex);
		$bibtex = str_replace('{\"a}', 'ä', $bibtex);
		$bibtex = str_replace('{\"o}', 'ö', $bibtex);

		// get the type and citeKey
		preg_match('/\@(article|book|incollection|inproceedings|masterthesis|misc|phdthesis|techreport|unpublished)\s?(\"|\{)([^\"\},]*),/i', $bibtex, $typeReg);
		$type = strtolower($typeReg[1]);
		$citeKey = $typeReg[3];

		// get the author
		preg_match('/author\s+=\s+(.*)/i', $bibtex, $authorReg);
		$author = stripfield($authorReg[1]);

		// get the title
		preg_match('/\btitle\b\s+=\s+(.*)/i', $bibtex, $titleReg);
		$title = stripfield($titleReg[1]);
		print_r($titleReg[1]);

		// get the journal
		preg_match('/journal\s+=\s+(.*)/i', $bibtex, $journalReg);
		$journal = stripfield($journalReg[1]);

		// get the booktitle
		preg_match('/booktitle\s+=\s+(.*)/i', $bibtex, $booktitleReg);
		$booktitle = stripfield($booktitleReg[1]);

		// get the publisher
		preg_match('/publisher\s+=\s+(.*)/i', $bibtex, $publisherReg);
		$publisher = stripfield($publisherReg[1]);

		// get the edition
		preg_match('/edition\s+=\s+(.*)/i', $bibtex, $editionReg);
		$edition = stripfield($editionReg[1]);

		// get the institution
		preg_match('/institution\s+=\s+(.*)/i', $bibtex, $institutionReg);
		$institution = stripfield($institutionReg[1]);

		// get the howpublished
		preg_match('/howpublished\s+=\s+(.*)/i', $bibtex, $howpublishedReg);
		$howpublished = stripfield($howpublishedReg[1]);

		// get the year
		preg_match('/year\s+=\s+(.*)/i', $bibtex, $yearReg);
		$year = stripfield($yearReg[1]);

		// get the month
		preg_match('/month\s+=\s+(.*)/i', $bibtex, $monthReg);
		$month = stripfield($monthReg[1]);

		// get the volume
		preg_match('/volume\s+=\s+(.*)/i', $bibtex, $volumeReg);
		$volume = stripfield($volumeReg[1]);

		// get the pages
		preg_match('/pages\s+=\s+(.*)/i', $bibtex, $pagesReg);
		$pages = stripfield($pagesReg[1]);

		// get the number
		preg_match('/number\s+=\s+(.*)/i', $bibtex, $numberReg);
		$number = stripfield($numberReg[1]);

		// get the series
		preg_match('/series\s+=\s+(.*)/i', $bibtex, $seriesReg);
		$series = stripfield($seriesReg[1]);

		// get the abstract
		preg_match('/abstract\s+=\s+(.*)/i', $bibtex, $abstractReg);
		$abstract = stripfield($abstractReg[1]);


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
				<td>booktitle:</td>
				<td>'.$booktitle.'</td>
			</tr>
			<tr>
				<td>year:</td>
				<td>'.$year.'</td>
			</tr>
			<tr>
				<td>month:</td>
				<td>'.$month.'</td>
			</tr>			
			<tr>
				<td>pages:</td>
				<td>'.$pages.'</td>
			</tr>
			<tr>
				<td>number:</td>
				<td>'.$number.'</td>
			</tr>	
			<tr>
				<td>series:</td>
				<td>'.$series.'</td>
			</tr>
			<tr>
				<td>volume:</td>
				<td>'.$volume.'</td>
			</tr>
			<tr>
				<td>publisher:</td>
				<td>'.$publisher.'</td>
			</tr>
			<tr>
				<td>howpublished:</td>
				<td>'.$howpublished.'</td>
			</tr>	
			<tr>
				<td>institution:</td>
				<td>'.$institution.'</td>
			</tr>	
			<tr>
				<td>edition:</td>
				<td>'.$edition.'</td>
			</tr>	
			<tr>
				<td>abstract:</td>
				<td>'.$abstract.'</td>
			</tr>

		</table>';

	}

	function stripField($string) {
		if (empty($string)) {
			return false;
		}
		$string = str_replace('{\"u}', 'ü', $string);
		$string = str_replace('{\"a}', 'ä', $string);
		$string = str_replace('{\"o}', 'ö', $string);

		$first_brace_pos = stripos($string, '{');
		$first_other_pos = stripos($string, '"');
		$last_brace_pos = strrpos($string, '}');
		$last_other_pos = strrpos($string, '"');

		if ($first_brace_pos !== false && $first_other_pos !== false) {
			if ($first_brace_pos < $first_other_pos + 1) {
				$first_pos = $first_brace_pos;
				$last_pos = $last_brace_pos;
			}
			else {
				$first_pos = $first_other_pos;
				$last_pos = $last_other_pos;
			}
		}
		else if ($first_brace_pos !== false) {
			$first_pos = $first_brace_pos;
			$last_pos = $last_brace_pos;
		}
		else if ($first_other_pos !== false) {
			$first_pos = $first_other_pos;
			$last_pos = $last_other_pos;
		}
		else {
			// break;
		}


		$string = substr($string, $first_pos + 1, ($last_pos - $first_pos - 1));
		$string = str_replace(array('{', '}'), '', $string);

		return $string;
	}

	?>
</body>
</html>
