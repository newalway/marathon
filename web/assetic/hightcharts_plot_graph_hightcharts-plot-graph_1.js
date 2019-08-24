$(function() {
	Highcharts.chart('plot-graph', {

		chart: {
				polar: true,
				type: 'line'
		},

		xAxis: {
				categories: ['Sales', 'Marketing', 'Development', 'Customer Support',
								'Information Technology', 'Administration', 'SEO', 'Online'],
				tickmarkPlacement: 'on',
				lineWidth: 0
		},

		yAxis: {
				gridLineInterpolation: 'polygon',
				lineWidth: 0,
				min: 0
		},

		series: [{
				name: 'Budget',
				data: [57000, 57000, 57000, 57000, 57000, 57000, 57000, 57000],
				pointPlacement: 'on'
		}]

	});

});
