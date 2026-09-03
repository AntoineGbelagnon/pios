import './bootstrap';
import 'bootstrap';
import { Chart, registerables } from 'chart.js';

Chart.register(...registerables);

const activityChart = document.getElementById('activityChart');
if (activityChart) {
    new Chart(activityChart, {
        type: 'line',
        data: {
            labels: JSON.parse(activityChart.dataset.labels),
            datasets: [{
                label: 'Ventes',
                data: JSON.parse(activityChart.dataset.values),
                borderColor: '#1538a6',
                backgroundColor: 'rgba(21, 56, 166, .08)',
                borderWidth: 4,
                pointRadius: 0,
                pointHoverRadius: 6,
                pointBackgroundColor: '#ffffff',
                pointBorderColor: '#1538a6',
                pointBorderWidth: 3,
                fill: true,
                tension: .42,
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { intersect: false, mode: 'index' },
            plugins: { legend: { display: false } },
            scales: {
                x: { grid: { display: false }, border: { display: false }, ticks: { color: '#9096a6' } },
                y: { beginAtZero: true, border: { display: false }, grid: { color: '#edf0f4' }, ticks: { precision: 0, color: '#9096a6' } },
            },
        },
    });
}

const resourcesChart = document.getElementById('resourcesChart');
if (resourcesChart) {
    new Chart(resourcesChart, {
        type: 'doughnut',
        data: {
            labels: ['Utilisateurs', 'Boutiques', 'Entrepots'],
            datasets: [{ data: JSON.parse(resourcesChart.dataset.values), backgroundColor: ['#1538a6', '#ffad1f', '#7837ee'], borderWidth: 0 }],
        },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } },
    });
}

const sidebar = document.getElementById('piosSidebar');
const sidebarBackdrop = document.querySelector('.pios-sidebar-backdrop');

document.querySelectorAll('[data-sidebar-toggle]').forEach((button) => {
    button.addEventListener('click', () => {
        sidebar?.classList.toggle('is-open');
        sidebarBackdrop?.classList.toggle('is-visible');
    });
});
