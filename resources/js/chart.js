// Dipakai hanya di area admin (dashboard). Tiap <canvas data-chart> membawa datanya sendiri:
// data-tipe="doughnut|bar", data-label='["..."]', data-nilai='[1,2]'.
import Chart from 'chart.js/auto';

const warna = ['#059669', '#0284c7', '#d97706', '#7c3aed', '#db2777', '#0891b2', '#65a30d'];

document.querySelectorAll('canvas[data-chart]').forEach((canvas) => {
    const tipe = canvas.dataset.tipe;
    const nilai = JSON.parse(canvas.dataset.nilai);

    new Chart(canvas, {
        type: tipe,
        data: {
            labels: JSON.parse(canvas.dataset.label),
            datasets: [{
                label: canvas.dataset.satuan ?? 'Jumlah',
                data: nilai,
                backgroundColor: tipe === 'bar' ? warna[0] : warna,
            }],
        },
        options: {
            maintainAspectRatio: false,
            plugins: { legend: { display: tipe === 'doughnut', position: 'bottom' } },
            scales: tipe === 'bar' ? { y: { beginAtZero: true } } : {},
        },
    });
});
