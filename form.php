<?php
/**
 * © Copyright (C) 2013 - 2023 <Marius Schröder>. Born 1999
 * All rights reserved.
 *
 * @author      Marius Schröder
 * @copyright   Copyright (c) 2013 - 2023, Marius Schröder
 * @link        https://schroeder.systems/
 * @mail        copyright@schroeder.systems
 *
 * Please include the full copyright for the name, domain and
 * email address if you use any part of the code in your imprint
 * or publicly readable on your website
 */

?>

<head>
	<title>Bumms</title>
</head>
<div class="col-sm-6 col-sm-offset-3">
	<h1>Titel</h1>

	<form id="rteu-form">
		<?php

		$json = file_get_contents(__DIR__.'/test.json');

		try
		{
			$content = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
		}
		catch(JsonException $e)
		{
			echo $e->getMessage();
			exit;
		}

		foreach($content as $key => $value)
		{
			?>
			<p><strong><?php echo strtoupper($key) ?></strong></p>
			<table>
				<tr>
					<td width="50%"></td>
					<td width="50%"></td>
				</tr>
				<?php
				foreach($value as $k => $v)
				{
					?>
					<tr>
						<td>
							<label for="<?php echo $key.'-'.$k; ?>"><?php echo $k; ?></label>
						</td>
						<td>
							<input type="text" class="form-control" id="sdr-frequency-input"
								   name="<?php echo $key.'-'.$k; ?>"
								   value="<?php echo $v; ?>">
						</td>
					</tr>
				<?php } ?>
			</table>
		<?php } ?>
		<button type="submit" class="btn btn-success">Absenden</button>
	</form>
</div>
<script>
	window.addEventListener("load", function()
	{
		const form = document.getElementById("rteu-form");

		form.addEventListener("submit", function(event)
		{
			event.preventDefault();

			const FD = new FormData(event.target);

			let Formobject = {};
			FD.forEach(function(value, key)
			{
				Formobject[key] = value;
			});
			let json = JSON.stringify(Formobject);

			sendData(json);
		});
	});

	function sendData(data)
	{
		let postData = {
			hash: 'sicherheitgehtvor!',
			formdata: data
		};

		const XHR = new XMLHttpRequest();

		// Define what happens on successful data submission
		XHR.addEventListener('load', async function(event)
		{
			let result = JSON.parse(event.target.responseText);
			// if login was not sucessfull - else add session token as cookie
			if(result.return === false)
			{
				alert('failed');
				console.log(result);
			}
			else
			{
				alert('success');
				console.log(result)
			}
		});

		// Define what happens in case of error
		XHR.addEventListener('error', function(event)
		{
			console.log(event.target.responseText);
		});

		// Set up our request
		XHR.open('POST', "http://localhost/marvin/handler.php");

		// Add the required HTTP header for form data POST requests
		XHR.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

		// Finally, send our data.
		XHR.send((serializeObject(postData)));
	}

	function serializeObject(obj)
	{
		let str = [];
		for(let p in obj)
			if(obj.hasOwnProperty(p))
			{
				str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
			}
		return str.join("&");
	}

</script>
