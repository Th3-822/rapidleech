var idleTime = 0;
$(document).ready(function(){
  var idleInterval = setInterval("idleTime++;", 1000);
  $(this).mousemove(function(e){
    var tmp = idleTime;
    idleTime = 0;
    if (tmp >= 120) { stats_timer = setTimeout("refreshStats()",1000); }
  });
});

var stats_timed = 10;
function refreshStats() {
	$.ajax({
		type: "GET",
		url: 'ajax.php?ajax=server_stats',
		dataType: 'json',
		success: function (data) {
			$('#cpuload').html(data.CPULoad);
			$('#inuse').html(data.InUse);
			$('#inusepercent').html(data.InUsePercent);
			$('#freespace').html(data.FreeSpace);
			$('#diskspace').html(data.DiskSpace);
			diskPercent = new Image();
			diskPercent.src = "classes/bar.php?rating=" + data.DiskPercent;
			$('#diskpercent').attr('src',diskPercent.src);
			cpuPercent = new Image();
			cpuPercent.src = "classes/bar.php?rating=" + data.CPUPercent;
			$('#cpupercent').attr('src',cpuPercent.src);
			if (stats_timed < 60) { stats_timed = stats_timed + 10; }
			if (idleTime < 120) {
				clearTimeout(stats_timer);
				stats_timer = setTimeout("refreshStats()",stats_timed * 1000);
			}
		}
	});
}