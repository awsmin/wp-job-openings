/* global awsmJobsAdminOverview, postboxes, Chart */

'use strict';

jQuery(document).ready(function($) {
	var awsmJobsOverview = window.awsmJobsOverview = window.awsmJobsOverview || {};

	/*================ Meta-boxes ================*/

	// Activate toggle state.
	postboxes.add_postbox_toggles(awsmJobsAdminOverview.screen_id);

	/*================ Charts ================*/

	var chartAreaPlugin = {
		id: 'chartAreaCustomizer',
		beforeDraw: function(chart, args, options) {
			if (options.chartArea && options.chartArea.primaryBgColor) {
				var primaryColor = options.chartArea.primaryBgColor;
				var secondaryColor = options.chartArea.secondaryBgColor || primaryColor;
				var ctx = chart.ctx;
				var chartArea = chart.chartArea;
				var height = chartArea.bottom - chartArea.top;
				var divisions = chart.scales.y.ticks.length - 1;
				var factor = height / divisions;

				ctx.save();
				for (var ticks = 1; ticks <= divisions; ticks++) {
					if (ticks % 2 === 0) {
						ctx.fillStyle = secondaryColor;
					} else {
						ctx.fillStyle = primaryColor;
					}
					var rectY = chartArea.top;
					if (ticks > 1) {
						rectY += (ticks - 1) * factor;
					}
					ctx.fillRect(chartArea.left, rectY, chartArea.right - chartArea.left, factor);
				}
				ctx.restore();
			}
		}
	};

	// Applications analytics chart
	var ctx = $('#awsm-jobs-overview-applications-analytics-chart');
	ctx[0].height = 250;
	var data = {
		labels: awsmJobsAdminOverview.analytics_data.labels,
		datasets: [ {
			label: awsmJobsAdminOverview.i18n.chart_label,
			data: awsmJobsAdminOverview.analytics_data.data,
			fill: true,
			borderColor: '#6CFAE4',
			backgroundColor: 'rgba(108, 250, 228, 0.15)', 
			pointBackgroundColor: '#6CFAE4',
			pointHoverBackgroundColor: '#6CFAE4',
			pointHoverBorderColor: '#6CFAE4',
			borderWidth: 4,
			pointBorderWidth: 2,
			pointRadius: 1,
			pointHoverRadius: 5,
			tension: 0.4,
			pointHitRadius: 10,
            
		} ]
	};
	var options = {
		scales: {
			x: {
				grid: {
					borderWidth: 1.5,
					drawOnChartArea: false,
					tickWidth: 1.5,
					display: false, // Remove vertical grid lines
					drawBorder: false
				},
				ticks: {
					font: {
						weight: 'normal'
					}
				}
			},
			y: {
				grid: {
					drawBorder: false,
					tickLength: 10,
					tickWidth: 0,
					color: '#F2F2F2', // Set grid line color
                    borderDash: [5, 5],
					drawBorder: false // Set grid lines as dotted
				},
				ticks: {
					font: {
						weight: 'normal'
					},
					precision: 0,
					stepSize: 5,
					
				}
			}
		},
		elements: {
            line: {
                borderColor: '#6CFAE4',
                borderWidth: 4
            },
            point: {
                radius: 1 // Hide the points
            }
        },
		layout: {
            padding: {
                right: 20, // Padding to the right for y-axis
                bottom: 20 // Padding to the bottom for x-axis
            }
        },
		plugins: {
			legend: {
				display: false
			}
		}
	};

	awsmJobsOverview.analyticsChart = {
		data: data,
		option: options,
		plugins: [ chartAreaPlugin ]
	};

	var analyticsChart = null;
	awsmJobsOverview.renderAnalyticsChart = function(reRender, chartData) {
		reRender = typeof reRender !== 'undefined' ? reRender : false;
		chartData = typeof chartData !== 'undefined' ? chartData : false;
		if (reRender && analyticsChart) {
			analyticsChart.destroy();
			analyticsChart = null;
		}
		if (! analyticsChart) {
			analyticsChart = new Chart(ctx, {
				type: 'line',
				data: data,
				options: options,
				plugins: [ chartAreaPlugin ]
			});
		}
		if (chartData && 'labels' in chartData && 'data' in chartData) {
			analyticsChart.data.labels = chartData.labels;
			analyticsChart.data.datasets[0].data = chartData.data;
			if ('datasets' in chartData) {
				analyticsChart.data.datasets = chartData.datasets;
			}
			analyticsChart.reset();
			analyticsChart.update();
		}
	};

	if (awsmJobsAdminOverview.analytics_data && 'data' in awsmJobsAdminOverview.analytics_data && awsmJobsAdminOverview.analytics_data.data.length > 0) {
		awsmJobsOverview.renderAnalyticsChart();

		$('.awsm-jobs-overview-mb-wrapper .meta-box-sortables').on('sortstop', function(e, ui) {
			if (ui.item.attr('id') === 'awsm-jobs-overview-applications-analytics') {
				awsmJobsOverview.renderAnalyticsChart(true);
			}
		});
		$('#awsm-jobs-overview-applications-analytics .handle-order-higher, #awsm-jobs-overview-applications-analytics .handle-order-lower' ).on('click.postboxes', function() {
			awsmJobsOverview.renderAnalyticsChart(true);
		});
	}
});
