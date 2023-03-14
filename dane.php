<!DOCTYPE HTML>

<html lang="pl">

<head>
	<meta charset="utf-8" />
	<title>Dane kandydatów</title>
	<link rel="stylesheet" href="main.css">
</head>

<body>

<?php
	//odebranie danych z formularza

	$imie=$_POST["imie"];
	$nazwisko=$_POST["nazwisko"];
	$email=$_POST["email"];
	$data=$_POST["data"];
	$wyksztalcenie=$_POST["wyksztalcenie"];
	$nazwa_firmy = $_POST['nazwa_firmy'];
	$data_od = $_POST['data_od'];
	$data_do = $_POST['data_do'];

	//próba nawiązania połączenia z bazą danych
	$baza = new mysqli('localhost', 'root', '', 'dane');	
	if($baza->connect_error)
	{
		die('Connection failed: '.$baza->connect_error);
	}
	else
	{
		$sql1="select Email from formularz where email='$email'";
		if($result1=$baza->query($sql1))
		{	
			$ile_userow=$result1->num_rows;
			
			//obsługa błędów
			if (!preg_match('/^[a-zA-ZąćęłńóśźżĄĆĘŁŃÓŚŹŻ]+$/', $imie))
			{
				echo '<div id="powrot">Źle wpisane pole: Imię</div>';
			}
			elseif (!preg_match('/^[a-zA-ZąćęłńóśźżĄĆĘŁŃÓŚŹŻ]+$/', $nazwisko))
			{
				echo '<div id="powrot">Źle wpisane pole: Nazwisko</div>';
			}
			elseif(!filter_var($email, FILTER_VALIDATE_EMAIL))
			{
				echo '<div id="powrot">Źle wpisane pole: Email</div>';
			}
			elseif (!preg_match('/^[a-zA-Zśż]+$/', $wyksztalcenie))
			{
				echo '<div id="powrot">Źle wpisane pole: Wykształcenie</div>';
			}
			elseif($ile_userow>0)
			{
				echo '<div id="powrot">Twoje dane istnieją już w naszej bazie danych</div>';
			}
			elseif (strtotime($data_od) > strtotime($data_do))
			{
				echo '<div id="powrot">Data rozpoczęcia stażu nie może być późniejsza niż data zakończenia stażu</div>';
			}
			elseif (strtotime($data) > strtotime($data_od))
			{
				echo '<div id="powrot">Data urodzenia nie może być późniejsza niż data rozpoczęcia stażu</div>';
			}
			else
			{
				//instrukcja TRY spróboje wykonac to co jest wewnątrz, a w razie niepowodzenia zamiast krytycznego błędu zostanie wyświetlony komunikat
				try
				{			
					//obsługa załączników
					$dopuszczone_rozszerzenia=array('pdf', 'doc', 'png');
		
					$sql2="select Email from zalaczniki where email='$email'";
					if($result1=$baza->query($sql2))
					{
						//sprawdzenie czy załączniki zostały przesłane i czy nie napotkano żadnego błędu
						if(isset($_FILES['lm']) && isset($_FILES['cv']) && $_FILES['lm']['error']  == UPLOAD_ERR_OK 
								&& $_FILES['cv']['error']  == UPLOAD_ERR_OK)
						{
							$LM_rozszerzenie = pathinfo($_FILES['lm']['name']);
							$LM_rozszerzenie1 = strtolower($LM_rozszerzenie['extension']);
								
							$CV_rozszerzenie = pathinfo($_FILES['cv']['name']);
							$CV_rozszerzenie1 = strtolower($CV_rozszerzenie['extension']);
								
							if(in_array($LM_rozszerzenie1, $dopuszczone_rozszerzenia) && in_array($CV_rozszerzenie1, $dopuszczone_rozszerzenia))
							{
								//sprawdzenie czy dodatkowy załącznik został przesłany
								if (isset($_POST['dodatkowy_zalacznik_checkbox']) && isset($_FILES['dodatkowy_zalacznik'])
									&& $_FILES['dodatkowy_zalacznik']['error']  == UPLOAD_ERR_OK ) 
								{
									$D_rozszerzenie = pathinfo($_FILES['dodatkowy_zalacznik']['name']);
									$D_rozszerzenie1 = strtolower($D_rozszerzenie['extension']);
										
									if(in_array($D_rozszerzenie1, $dopuszczone_rozszerzenia))
									{
										$lm = file_get_contents($_FILES['lm']['tmp_name']);
										$cv = file_get_contents($_FILES['cv']['tmp_name']);
										$dodatkowy_zalacznik = file_get_contents($_FILES['dodatkowy_zalacznik']['tmp_name']);
										
										$pdo = new PDO('mysql:host=localhost;dbname=dane', 'root', '');
										
										$sql2 = "INSERT INTO zalaczniki (Email, LMnazwa, LMplik, CVnazwa, CVplik, Dnazwa, Dplik) VALUES (?, ?, ?, ?, ?, ?, ?)";
										$stmt= $pdo->prepare($sql2);
										
										$LMname = $_FILES['lm']['name'];
										$CVname = $_FILES['cv']['name'];
										$Dname = $_FILES['dodatkowy_zalacznik']['name'];
									
										$LMdata = $lm;
										$CVdata = $cv;
										$Ddata = $dodatkowy_zalacznik;
									
										$stmt->execute([$email, $LMname, $LMdata, $CVname, $CVdata, $Dname, $Ddata]);
									}
									else
									{
										//echo "Błędne rozszerzenie pliku: Dodatkowy załącznik";
										$message = "Błędne rozszerzenie pliku: Dodatkowy załącznik";
										throw new Exception($message);
										
										//gdy intrukcja TRY napotka błąd to: throw new Exception($message); prześle '$message' do: catch (Exception) czyli do bloku który wykona się gdy TRY napotka błąd
										//wystarczyło by samo ECHO ale chciałem przedstawić taką możliwość (dlatego został zakomentowany)
									}
								}
								else
								{
									$lm = file_get_contents($_FILES['lm']['tmp_name']);
									$cv = file_get_contents($_FILES['cv']['tmp_name']);
								
									$pdo = new PDO('mysql:host=localhost;dbname=dane', 'root', '');
								
									$sql2 = "INSERT INTO zalaczniki (Email, LMnazwa, LMplik, CVnazwa, CVplik) VALUES (?, ?, ?, ?, ?)";
									$stmt= $pdo->prepare($sql2);
								
									$LMname = $_FILES['lm']['name'];
									$CVname = $_FILES['cv']['name'];
								
									$LMdata = $lm;
									$CVdata = $cv;
								
									$stmt->execute([$email, $LMname, $LMdata, $CVname, $CVdata]);
								}
							}
							else
							{
								//echo "Błędne rozszerzenie pliku LM lub CV";
								$message1 ="Błędne rozszerzenie pliku LM lub CV";
								throw new Exception($message1);
							}
						}
						else
						{
							//echo "Wystąpił błąd podczas ładowania pliku.";
							$message2 ="Wystąpił błąd podczas ładowania pliku.";
							throw new Exception($message2);
						}
					}	

					
					$sql3 = "INSERT INTO staz (Email, Firma, Od, Do) VALUES ('$email','$nazwa_firmy', '$data_od', '$data_do')";
					mysqli_query($baza, $sql3);

					//dodawanie do bazy danych każdego dodatkowego stażu który przesłał użytkownik
					for ($i = 1; $i <= 10; $i++) 
					{
						if (isset($_POST['nazwa_firmy'.$i]))
						{
							$nazwa_firmy = $_POST['nazwa_firmy'.$i];
							$data_od = $_POST['data_od'.$i];
							$data_do = $_POST['data_do'.$i];
							
							$sql3 = "INSERT INTO staz (Email,Firma, Od, Do) VALUES ('$email','$nazwa_firmy', '$data_od', '$data_do')";
							mysqli_query($baza, $sql3);
						} 
						else 
						{
							break;
						}
					}
					
					//dodawanie DANE PODSTAWOWE z formularza do bazy danych
					$sql ="insert into formularz(Imie, Nazwisko, Email, DataUro, Wyksztalcenie) values ('$imie', '$nazwisko', '$email', '$data', '$wyksztalcenie')";
					$result=$baza->query($sql);
					
					$baza->close();

					echo '<h1 id="podziekowanie">Dziękujemy za przesłanie formularza</h1>';
					echo '<h4 id="podziekowanie">Twoje dane zostały przesłane do naszej bazy danych</h4>';
				}
				catch (Exception)    //intrukcje które wyonają się gdy TRY napotka błąd
				{
					if (isset($message)) 
					{
						echo  $message;
					}
					elseif (isset($message1)) 
					{
						echo  $message1;
					}
					elseif (isset($message2)) 
					{
						echo  $message2;
					}
					else
					{
						echo '<h4 id="podziekowanie">Przepraszamy, wprowadzone dane są niepoprawne lub rozmiar któregoś z załączników jest zbyt duży. Spóbuj ponownie.</h4>';
					}	
				}
			}
		}
	}
		
?>

<form action="index.php">

	<div id="przycisk"><input type="submit" value="Powrót"/></div>
	</br></br>

</form>

</body>
</html>  