import './bootstrap';
import 'bootstrap';
import { Chart, registerables } from 'chart.js';

Chart.register(...registerables);

const activityChart = document.getElementById('activityChart');
if (activityChart) {
	new Chart(activityChart, {
		type: 'bar',
		data: {
			labels: JSON.parse(activityChart.dataset.labels),
			datasets: [{ label: 'Activités', data: JSON.parse(activityChart.dataset.values), backgroundColor: '#0f766e', borderRadius: 6 }],
		},
		options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { precision: 0 } } } },
	});
}

const resourcesChart = document.getElementById('resourcesChart');
if (resourcesChart) {
	new Chart(resourcesChart, {
		type: 'doughnut',
		data: { labels: ['Utilisateurs', 'Boutiques', 'Entrepôts'], datasets: [{ data: JSON.parse(resourcesChart.dataset.values), backgroundColor: ['#0f766e', '#f59e0b', '#2563eb'], borderWidth: 0 }] },
		options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } },
	});
}
