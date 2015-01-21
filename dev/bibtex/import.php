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
	<textarea name="bibtex" cols="50" rows="10"><?php echo stripslashes(htmlspecialchars($bibtex, ENT_QUOTES)); ?></textarea><br/>
	<input type="submit" value="Import"/>
	</form>

	
	<?php
	// while testing
	// error_reporting(E_ALL & ~E_NOTICE);

	if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($bibtex)) {

		$result = array();

		$bibtex = stripslashes($bibtex);

		// get rid off special symbols
		$bibtex = str_replace(array('\"u', '\"{u}', '{\"u}'), 'ü', $bibtex);
		$bibtex = str_replace(array('\"a', '\"{a}', '{\"a}'), 'ä', $bibtex);
		$bibtex = str_replace(array('\"o', '\"{o}', '{\"o}'), 'ö', $bibtex);
		$bibtex = str_replace(array('\"U', '\"{U}', '{\"U}'), 'Ü', $bibtex);
		$bibtex = str_replace(array('\"A', '\"{A}', '{\"A}'), 'Ä', $bibtex);
		$bibtex = str_replace(array('\"O', '\"{O}', '{\"O}'), 'Ö', $bibtex);

		$bibtex = str_replace('\c{c}', 'ç', $bibtex);
		$bibtex = str_replace('\c{C}', 'Ç', $bibtex);

		// get the type and citeKey
		preg_match('/\@(article|book|incollection|inproceedings|masterthesis|misc|phdthesis|techreport|unpublished|inbook)\s?(\"|\{)([^\"\},]*),/i', $bibtex, $typeReg);
		$result['type'] = strtolower($typeReg[1]);
		$result['citeKey'] = $typeReg[3];

		// get the other fields
		$fields = array('author', 'title', 'journal', 'booktitle', 'publisher', 'edition', 'institution', 'howpublished', 'year', 'month', 'volume', 'pages', 'number', 'series', 'abstract', 'bibsource', 'copyright', 'url', 'doi', 'address', 'date-added', 'date-modified', 'keywords');

		foreach ($fields as $field) {
			$regex = '/\b'.$field.'\b\s*=\s*(.*)/i';
			preg_match($regex, $bibtex, $reg_result);

			if (!empty($reg_result[1])) {
				$value = stripField($reg_result[1]);

				if (!empty($value)) {
					$result[$field] = $value;
				}
			}
		}

		echo "<h1>Imported meta data:</h1>";
		echo '<table border="0" cellpadding="0" cellspacing="4" >';

		foreach ($result as $key => $value) {
			echo '<tr><td>'.$key.':</td>';
			echo '<td>'.$value.'</td><tr>';
		}
		
		echo '</table>';	
	}


	function stripField($string) {
		if (empty($string)) {
			return false;
		}

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
		$string = trim($string);

		return $string;
	}

	?>
</body>
</html>
