// JavaScript Document
        function runMiniClock()
        {
            var time = new Date();
            var hours = time.getHours();
            var minutes = time.getMinutes();
			var seconds = time.getSeconds();
            minutes=((minutes < 10) ? "0" : "") + minutes;
			seconds=((seconds < 10) ? "0" : "") + seconds;
            hours=(hours == 0) ? 12 : hours;
			hours=((hours < 10) ? "0" : "") + hours;
			month = time.getMonth()+1;
			date = time.getDate()+"/"+month+"/"+time.getFullYear();
            var clock = hours + ":" + minutes + ":" + seconds;
			clock += "<br />";
			clock += date;
            if(clock != document.getElementById('miniclock').innerHTML) document.getElementById('miniclock').innerHTML = clock;
            timer = setTimeout("runMiniClock()",1000);
        }
        runMiniClock();
