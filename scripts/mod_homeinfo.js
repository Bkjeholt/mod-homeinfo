	function mod_homeinfo_showDiffTime2(sampleTime) {
//		document.getElementById("mod_homeinfo_sensor_data_time").innerHTML = "Testing";

		var aaa = document.getElementById("mod_homeinfo_sensor_data_time");
		var d = new Date();
		var timeString = new String();
		var functionName = new String("mod_homeinfo_showDiffTime2");
	
		if (aaa != null) {
			var currTime = new Number(d.getTime()/1000);
				
			var diffTime = new Number();
			diffTime = currTime.toFixed(0) - sampleTime;
			aaa.innerHTML = diffTime;
		
			var resultString = new String("");
			if (diffTime < 300) {
				aaa.innerHTML = resultString.concat("(",diffTime, " sekunder gammal)");
			} else {
				var diffTimeMinutes = new Number();
				diffTimeMinutes = diffTime/60;
				aaa.innerHTML = resultString.concat("(",diffTimeMinutes.toFixed(0), " minuter gammal)");
			}
		
			timeString = functionName.concat("(", sampleTime, ")");

			setTimeout(timeString,1000);
		} 
	}
