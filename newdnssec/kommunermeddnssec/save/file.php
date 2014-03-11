<?php

/*

	anrop:
		php file.php <nysida.txt >out.html

*/


	function getContent(){
		$data = "";
		while( !feof(STDIN) ){
			$line = trim(fgets(STDIN));

			$arrParts = explode(",", $line);


			if( sizeof($arrParts) > 3 ){
				$host = "";
				$yesOrEmpty = "";
				$email = "";
				$nsList = "";

				for ($i = 0 ; $i < sizeof($arrParts) ; $i++) {

					$arrParts[$i] = trim($arrParts[$i]);

					if( $i == 0 ){
						$host = $arrParts[$i];
					}
					else if( $i == 1 ){
						$yesOrEmpty = $arrParts[$i];
					}
					else if( $i == 2 ){
						$email =  formatEmail($arrParts[$i]);
					}
					else if( $i > 2 ){
						$nsList = $nsList . $arrParts[$i];
						if( $i < sizeof($arrParts) -1 ){
							$nsList = $nsList . "<br/>";
						}
					}
				}

				$data = $data . outputSingleDomain($host, $yesOrEmpty != "", $email, $nsList);

			}

		}

		return $data;
	}

	function formatEmail($email){

		if($email == "") return "";
		$email = str_replace("\\.", "�", $email);

		$firstDot = strpos($email, '.');


		$email = substr($email, 0, $firstDot) . " [at] " . substr($email, $firstDot + 1);

		return str_replace("�", ".", $email);
	}

	function outputSingleDomain($host, $isSecure, $email, $nsList){

		global $okDomCount;
		global $notOkDomCount;

		$tagStart = "<div class=\"".($isSecure? "okdom" : "dom")."\">";
		$tagEnd = "</div>";
		$okImg = "<img src=\"tick.png\" style=\"width:16px;height:16px;border:0px\"/>&nbsp;";
		$linkStart = "<a href=\"http://www.";
		$linkMid = "\" onmouseover=\"showHover(event, '" . $email . "<br/>" . $nsList . "')\" onmouseout=\"hideHover()\" title=\"";
		$linkMidEnd = "\">";
		$linkEnd = "</a>";

		if( $isSecure ){
			$okDomCount++;
		}
		else{
			$notOkDomCount++;
		}

		return $tagStart . $linkStart . $host . $linkMid . $linkMidEnd . ($isSecure? $okImg : "") . $host . $linkEnd . $tagEnd . "\n";

	}

	//dessa tv� s�tts av outputSingleDomain
	$okDomCount = 0;
	$notOkDomCount = 0;

	//skapar listan av alla divar.
	$domainList = getContent();


?>
<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
<html>
	<head>
		<style>
			body{
				font-family: Arial, tahoma, verdana, arial;
			}
			.mainContainer{
				background: url('bg.gif');
				font-size: 12px;
			}
			.dom{
				padding: 2px;
				width: 145px;
				margin: 1px;
				overflow: hidden;
				float: left;
				height: 20px;
			}
			.okdom{
				padding: 2px;
				width: 145px;
				margin: 1px;
				overflow: hidden;
				float: left;
				background-color: #A0FFA0;
				height: 20px;
			}
			h1, h2, h3, h4, h5, h6{
				border-bottom: 1px solid #b0b0b0;
			}
			.contact{
				text-style: uppercase;
			}
			.subContainer{
				margin-botton: 42px;
			}
		</style>
		<title>Kommuner med DNSSEC</title>
		<script type="text/javascript">
			function showHover(e, txt){
				if( e == null ) e = window.event;
				if(e.pageX == null ) e.pageX = e.clientX;
				if(e.pageY == null ) e.pageY = e.clientY + document.body.scrollTop;
				//alert(e.pageX);

				var o = document.getElementById("hoverDiv");
				o.innerHTML = txt;
				o.style.left = (e.pageX+20) + "px";
				o.style.top = (e.pageY+12) + "px";
				o.style.visibility = "visible";
			}
			function hideHover(){
				var o = document.getElementById("hoverDiv");
				o.style.visibility = "hidden";
			}
		</script>
		<script type="text/javascript">
		var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
		document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
		</script>
		<script type="text/javascript">
		try {
		var pageTracker = _gat._getTracker("UA-10643709-2");
		pageTracker._trackPageview();
		} catch(err) {}</script>
	</head>
	<body>
		<div class="mainContainer" style="padding:20px;border:1px solid #e0e0e0;position:absolute;left:50%;width:800px;margin-left:-400px;margin-top:50px">
			<h1>Kommuner med DNSSEC</h1>
			<div class="subContainer">
				<p><b>kommunermeddnssec.se</b> �r en frist�ende hemsida som listar alla kommundom�ner och visar om de �r signerade med <b><a href=http://en.wikipedia.org/wiki/DNSSEC>DNSSEC</a> </b>eller inte.<br>
				Sidan uppdatera automatiskt n�gra g�nger per dag.<br>
<?php echo `./dnscheck`; ?>
				<br><i>Kontakt: tobbe (a] <a href=http://www.interlan.se>interlan</a> punkt se</i><br>
				Se �ven <h href=http://www.iis.se><b>.SE</b></a>'s <a href=http://fou.iis.se/dnsseckommun/>geografiska spridning av DNSSEC</a> eller systersidan <a href=http://www.kommunermedipv6.se>www.kommunermedipv6.se</a>

			</div>

			<h2>Dom�ner</h2>
			<div class="subContainer">
				<i>Uppdaterad <?php echo `date`; ?></i><br/>
				<?php

					echo $okDomCount . " av " . ($okDomCount + $notOkDomCount) . " dom�ner s�krade<br/>";

					echo $domainList;

				?>
				<div style="clear:both">&nbsp;</div>
			</div>
			<div style="clear:both">&nbsp;</div>
		</div>

		<div id="hoverDiv" style="position:absolute;visibility:hidden;left:0px;top:0px;font:11px verdana;border:1px solid #AFAF20;padding:4px; width:220px;background-color:#FFFFE0;">&nbsp;</div>

	</body>
</html>

