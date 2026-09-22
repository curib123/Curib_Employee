/**
 * Dashboard Chart.js rendering. Data is supplied by escaped data attributes.
 */
(function () {
    'use strict';

    var dataElement = document.getElementById('dashboardChartData');

    if (!dataElement || typeof Chart === 'undefined') {
        return;
    }

    var ageData = JSON.parse(dataElement.dataset.age || '{}');
    var monthlyData = JSON.parse(dataElement.dataset.monthly || '[]');
    var addressData = JSON.parse(dataElement.dataset.address || '[]');

    new Chart(document.getElementById('ageBreakdownChart'), {
        type: 'pie',
        data: {
            labels: ageData.labels,
            datasets: [{
                data: ageData.values,
                backgroundColor: ['#0d6efd', '#20c997', '#ffc107', '#fd7e14', '#dc3545'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: 'bottom' } }
        }
    });

    new Chart(document.getElementById('monthlyRegistrationsChart'), {
        type: 'line',
        data: {
            labels: monthlyData.map(function (item) { return item.registration_month; }),
            datasets: [{
                label: 'Registrations',
                data: monthlyData.map(function (item) { return Number(item.total); }),
                borderColor: '#0d6efd',
                backgroundColor: 'rgba(13, 110, 253, 0.15)',
                fill: true,
                tension: 0.3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
        }
    });

    new Chart(document.getElementById('addressStatisticsChart'), {
        type: 'bar',
        data: {
            labels: addressData.map(function (item) { return item.address; }),
            datasets: [{
                label: 'Users',
                data: addressData.map(function (item) { return Number(item.total); }),
                backgroundColor: '#20c997',
                borderRadius: 6
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            scales: { x: { beginAtZero: true, ticks: { precision: 0 } } },
            plugins: { legend: { display: false } }
        }
    });
}());
