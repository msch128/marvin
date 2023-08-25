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

/**
 * @return string
 */
function getMyUrl()
{
	$protocol = (!empty($_SERVER['HTTPS']) && (strtolower($_SERVER['HTTPS']) == 'on' || $_SERVER['HTTPS'] == '1')) ? 'https://' : 'http://';
	$server   = $_SERVER['SERVER_NAME'];
	$request  = $_SERVER['REQUEST_URI'] ?? '';
	$port     = $_SERVER['SERVER_PORT'] ? ':'.$_SERVER['SERVER_PORT'] : '';

	return $protocol.$server.$port.$request;
}

?>

<head>
	<title>Configuration editor</title>
</head>
<div class="col-sm-6 col-sm-offset-3">
	<h1></h1>

	<form id="rteu-form">
		<?php

		// get local json file
		$json = file_get_contents(__DIR__.'/rteu.json');

		try
		{
			// decode json to array
			$content = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
		}
		catch(JsonException $e)
		{
			echo $e->getMessage();
			exit;
		}

		// loop through array to set first row
		// i.e. RX3
		foreach($content as $key => $value)
		{
			?><p><strong><?php echo strtoupper($key) ?></strong></p>
			<table>
				<tr>
					<td width="50%"></td>
					<td width="50%"></td>
				</tr>
				<?php
				// loop through child element to get key and value
				// i.e. name => Hello World
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
		<button type="submit" class="btn btn-success">submit</button>
	</form>
</div>
<script>
	/**
	 * ajax function for sending  data to handler.php
	 */
	window.addEventListener("load", function()
	{
		const form = document.getElementById("rteu-form");

		form.addEventListener("submit", function(event)
		{
			event.preventDefault();

			// get all Formdata for Ajax
			const FD = new FormData(event.target);

			let Formobject = {};
			FD.forEach(function(value, key)
			{
				Formobject[key] = value;
			});

			sendData(JSON.stringify(Formobject));
		});
	});

	/**
	 * Sends data to handler.php based on Form input
	 * @param data
	 */
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
			// if login was not sucessfull - else successfully submitted data and rteu.json overwritten
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

		// if response was an  error log responsetext to console
		XHR.addEventListener('error', function(event)
		{
			console.log(event.target.responseText);
		});

		// sets ajax type and url
		<?php echo '//'.$_SERVER['REQUEST_URI'].PHP_EOL; ?>
		XHR.open('POST', "<?php echo getMyUrl(); ?>/handler.php");

		// Add the required HTTP header for form data POST requests
		XHR.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

		// send data to defined url
		XHR.send((serializeObject(postData)));
	}

	/**
	 * serializes objects into a string for transfering its content to php
	 *
	 * @param obj
	 * @returns {string}
	 */
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
