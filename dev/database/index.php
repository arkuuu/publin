<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>Database testing</title>
</head>
<body>

<?php

require 'class/Database.php';
require 'class/Publication.php';
require 'class/Author.php';

$db = new Database('localhost', 'root', 'root', 'dev');


if (isset($_GET['action'])) {
	$action = $_GET['action'];
}
else {
	$action = 'default';
} 

if (isset($_GET['id'])) {
	$id = $_GET['id'];
}
else {
	$id = 0;
}

if (isset($_GET['name'])) {
	$name = $_GET['name'];
}
else {
	$name = 0;
}

if (isset($_GET['year'])) {
	$year = $_GET['year'];
}
else {
	$year = 0;
}

if (isset($_GET['month'])) {
	$month = $_GET['month'];
}
else {
	$month = 0;
}


switch ($action) {

	case 'browseAuthor':

		echo '<p><a href="index.php">back</a></p>
			<h2>Browse by authors</h2>
				<br />';

		$data = $db -> getAuthors();
		echo '<ul>';

		foreach ($data as $key => $value) {
			$author = new Author($value, $db);

			echo '<li><a href="author.php?id='.$author -> getId().'">'.$author -> getName().'</a></li>';
		}
		echo '</ul>';
		
		break;


	case 'browseKeyTerm':

		echo '<p><a href="index.php">back</a></p>
			<h2>Browse by key terms</h2>
				<br />';

		if ($id == 0) {

			$data = $db -> getKeyTerms();

			foreach ($data as $key => $value) {
				echo '<p><a href="index.php?action='.$action.'&id='.$value['id']
						.'&name='.$value['name'].'">'.$value['name'].'</a></p>';
			}
		}
		else {

			$data = $db -> getPublications(array('key_term_id' => $id));

			echo '	<br /><p>Found '.$db -> getNumData().' entries for '.$name.':</p>
						<table>
							<tr>
								<th>Title</th>
								<th>Author(s)</th>
								<th>Year</th>
								<th>Month</th>
							</tr>';

			foreach ($data as $key => $value) {
					$publ = new Publication($value, $db);
					echo '	<tr>
								<td><a href="publication.php?id='.$publ -> getId().'">'.$publ -> getTitle().'</a></td>
								<td>'.$publ -> getAuthorsString().'</td>
								<td>'.$publ -> getYear().'</td>
								<td>'.$publ -> getMonth().'</td>
							</tr>';
			}

			echo '	</table>';
		}
		break;


	case 'browseFieldOfStudy':

		echo '<p><a href="index.php">back</a></p>
			<h2>Browse by fields of study</h2>
				<br />';

		if ($id == 0) {

			$data = $db -> getStudyFields();

			foreach ($data as $key => $value) {
				echo '<p><a href="index.php?action='.$action.'&id='.$value['id']
					.'&name='.$value['name'].'">'.$value['name'].'</a></p>';
			}
		}
		else {

			$data = $db -> getPublications(array('study_field_id' => $id));

			echo '	<br /><p>Found '.$db -> getNumData().' entries for '.$name.':</p>
						<table>
							<tr>
								<th>Title</th>
								<th>Author(s)</th>
								<th>Year</th>
								<th>Month</th>
							</tr>';

			foreach ($data as $key => $value) {
					$publ = new Publication($value, $db);
					echo '	<tr>
								<td><a href="publication.php?id='.$publ -> getId().'">'.$publ -> getTitle().'</a></td>
								<td>'.$publ -> getAuthorsString().'</td>
								<td>'.$publ -> getYear().'</td>
								<td>'.$publ -> getMonth().'</td>
							</tr>';
			}

			echo '	</table>';
		}
		break;


	case 'browseYear':

		echo '<p><a href="index.php">back</a></p>
			<h2>Browse by year</h2>
				<br />';

		if ($year == 0) {
			
			$data = $db -> getYears();

			foreach ($data as $key => $value) {
				echo '<p><a href="index.php?action='.$action.'&year='.$value['year'].'">'
						.$value['year'].'</a></p>';
			}
		}
		else {
			if ($month == 0) {
				$data = $db -> getMonths($year);


				echo '<p><b>'.$year.'</b></p>';

				foreach ($data as $key => $value) {
					echo '<p><a href="index.php?action='.$action.'&year='.$year.'&month='
							.$value['month'].'">'.$value['month'].'</a></p>';
				}

				$data = $db -> getPublications(array('year' => $year));

			}
			else {
				echo '	<p><b>'.$year.'</b></p>
						<p><b>'.$month.'</b></p>';	

				$data = $db -> getPublications(array('year' => $year, 'month' => $month));
			}

			echo '	<br /><p>Found entries:</p>
						<table>
							<tr>
								<th>Title</th>
								<th>Author</th>
							</tr>';

				foreach ($data as $key => $value) {
					$publ = new Publication($value, $db);
					echo '	<tr>
								<td><a href="publication.php?id='.$publ -> getId().'">'.$publ -> getTitle().'</a></td>
								<td>'.$publ -> getAuthorsString().'</td>

						</tr>';
				}
				echo '	</table>';
		}
		break;


	case 'browseType':

		echo '<p><a href="index.php">back</a></p>
			<h2>Browse by types</h2>
				<br />';

		if ($id == 0) {

			$data = $db -> getTypes();

			foreach ($data as $key => $value) {
				echo '<p><a href="index.php?action='.$action.'&id='.$value['id']
						.'&name='.$value['name'].'">'.$value['name'].'</a></p>';
			}
		}
		else {

			$data = $db -> getPublications(array('type_id' => $id));

			echo '	<br /><p>Found '.$db -> getNumData().' entries for '.$name.':</p>
						<table>
							<tr>
								<th>Title</th>
								<th>Author</th>
								<th>Year</th>
								<th>Month</th>
							</tr>';

			foreach ($data as $key => $value) {
					$publ = new Publication($value, $db);
					echo '	<tr>
								<td><a href="publication.php?id='.$publ -> getId().'">'.$publ -> getTitle().'</a></td>
								<td>'.$publ -> getAuthorsString().'</td>
								<td>'.$publ -> getYear().'</td>
								<td>'.$publ -> getMonth().'</td>
							</tr>';
			}

			echo '	</table>';
		}
		break;


	default:
		echo '<h1>Database testing site</h2>
				<p>Please choose an option to show a complete page:<p>
				<ul>
					<li><a href="publication.php">Example publication page</a></li>
					<li><a href="author.php">Example author page</a></li>
				</ul>
				<br />

				<p>Or choose an option below to browse the entries:<p>
				<ul>
					<li><a href="index.php?action=browseAuthor">Browse by author</a></li>
					<li><a href="index.php?action=browseKeyTerm">Browse by key terms</a></li>
					<li><a href="index.php?action=browseFieldOfStudy">Browse by field of study</a></li>
					<li><a href="index.php?action=browseYear">Browse by year</a></li>
					<li><a href="index.php?action=browseType">Browse by type</a></li>
				</ul>';
		break;
}

// $mysqli -> close(); // TODO: Replace with new Database class
?>
</body>
</html>
