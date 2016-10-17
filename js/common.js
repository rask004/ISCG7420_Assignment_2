"use strict";       // more secure.

// Generates auto-updating current time.
function update_time() {
    var currentTime = new Date();
    var hours = currentTime.getHours();
    var minutes = currentTime.getMinutes();
    var seconds = currentTime.getSeconds();
    if (hours < 10) { hours = "0" + hours; }
    if (minutes < 10) { minutes = "0" + minutes; }
    if (seconds < 10) { seconds = "0" + seconds; }
    document.getElementById("footerCurrentTime").innerHTML = hours + ":" + minutes + ":" + seconds;
    setTimeout(update_time, 1000);
}
