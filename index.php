<!DOCTYPE HTML>

<html lang="pl">
<head>
	<meta charset="utf-8" />
	<title>Formularz zgłoszeń kandydatów</title>
	<link rel="stylesheet" href="main.css">
</head>

<body>

	<header>
		<b>Formularz zgłoszeniowy</b>
	</header>
	</br>
	<main>
		<form action="dane.php"  method="post" enctype="multipart/form-data">	
			<fieldset>
				<legend><b>Dane Podstawowe</b></legend>
				<div id="zawartosc">
			
					Imię:</br>
					<div><input type="text" name="imie" pattern="[a-zA-ZąćęłńóśźżĄĆĘŁŃÓŚŹŻ]+" required /></div>   <!-- pattern [] - obsługa polskiego alfabetu w inputach -->
					Nazwisko:</br>
					<div><input type="text" name="nazwisko" pattern="[a-zA-ZąćęłńóśźżĄĆĘŁŃÓŚŹŻ]+" required/></div>
					Adres e-mail:</br>
					<div><input type="email" name="email" required/></div></br>
					Data Urodzenia:</br>
					<div><input type="date" name="data" required/></div>
					Wykształcenie:</br>
					<select id="wyksztalcenie" name="wyksztalcenie" required>
						<option value="" selected>--Wybierz--</option>
						<option value="podstawowe">podstawowe</option>
						<option value="średnie">średnie</option>
						<option value="wyższe">wyższe</option>
					</select>
					
				</div>
			</fieldset></br>
			
			<fieldset>
				<legend><b>Załączniki </b></legend>
	
				<div id="zawartosc">
					<label>List motywacyjny <input type="file" accept=".jpg, .pdf, .doc" name="lm" required></label></br>
					<label>CV <input type="file" accept=".jpg, .pdf, .doc" name="cv" required></label></br>
					
					<div id="dodatek">
						<input type="checkbox" id="dodatkowy_zalacznik_checkbox" name="dodatkowy_zalacznik_checkbox" value="tak">
						<label for="dodatkowy_zalacznik_checkbox">Dodatkowy załącznik (opcjonalnie):</label>
						<input type="file" id="dodatkowy_zalacznik" name="dodatkowy_zalacznik" accept=".jpg, .pdf, .doc" disabled><br>
					</div>
					<inf>Dozwolone formaty: JPG, PDF, DOC</inf>
				</div>
				
			</fieldset></br>
			
			<!-- kod JS umożliwia dołączenie załącznika jeśli checkbox jest zaznaczony -->
			
			<script>
				var checkbox = document.getElementById('dodatkowy_zalacznik_checkbox');
				var dodatkowyZalacznik = document.getElementById('dodatkowy_zalacznik');
				checkbox.onclick = function() 
					{
						if (checkbox.checked)
						{
							dodatkowyZalacznik.disabled = false;
						} 
						else 
						{
							dodatkowyZalacznik.disabled = true;
						}
					}
			</script>
			
			<fieldset>
				<legend><b>Staż</b></legend>
				<div id="staze">
					<input type="button" value="Dodaj kolejny staż" onClick="addInput()"><br><br>
					<div id="staz-1">
						<label>Staż 1:</label>
						<input type="text" name="nazwa_firmy" placeholder="Nazwa firmy">
						<input type="date" name="data_od" placeholder="Data od">
						<input type="date" name="data_do" placeholder="Data do">
					</div>
					<div id="inputs"></div>
				</div>

				<!-- kod JS odpowiada ze dodawanie nowych pól na stronie w celu dodania kolejnych staży -->

				<script>
					var counter = 1;
					var limit = 10; 

					function addInput() 
					{
						if (counter == limit) 
						{
							alert("Osiągnięto maksymalną liczbę staży.");
						}
						else
						{
							var fieldset = document.getElementById('staze'); 
							var div = document.createElement('div'); 
							div.id = 'staz-' + (counter + 1); 
							var newLabel = document.createElement('label');
							newLabel.innerHTML = 'Staż ' + (counter + 1) + ':';

							var newInput1 = document.createElement('input');
							newInput1.type = 'text';
							newInput1.name = 'nazwa_firmy' + counter;
							newInput1.placeholder = 'Nazwa firmy';

							var newInput2 = document.createElement('input');
							newInput2.type = 'date';
							newInput2.name = 'data_od' + counter;
							newInput2.placeholder = 'Data od';

							var newInput3 = document.createElement('input');
							newInput3.type = 'date';
							newInput3.name = 'data_do' + counter;
							newInput3.placeholder = 'Data do';

							div.appendChild(newLabel); 
							div.appendChild(newInput1); 
							div.appendChild(newInput2); 
							div.appendChild(newInput3); 

							fieldset.appendChild(div); 

							counter++; 
						}
					}
				</script>
			</fieldset>
					
			<div id='reset'><input type="reset" value="Wyczyść formularz"></div></br></br>
			<div id="przycisk"><input type="submit" value="Wyślij zgłoszenie"/></div>

		</form>
	</main>

</body>
</html>