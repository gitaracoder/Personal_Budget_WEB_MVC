window.onload = function() {

var options = {

	backgroundColor: "",  title: {
	fontStyle: "Arial",
		text: ""
	},
	data: [{
	fontStyle: "Arial",
			type: "pie",
			startAngle: 45,
			showInLegend: "true",
			legendText: "{label}",
			indexLabel: "{label} ({y})",
			yValueFormatString:"#,##0.#"%"",
			dataPoints: [
				{ label: "Mieszkanie", y: 2000 },
				{ label: "Jedzenie", y: 1000 },
				{ label: "Transport", y: 500 },
				{ label: "Ubranie", y: 200 },
				{ label: "Oszczędności", y: 1000 },
				{ label: "Wycieczka", y: 1500 },
				{ label: "Książki", y: 100 }
			]
	}]
};
$("#chartContainer").CanvasJSChart(options);

}