// Dipakai hanya di area admin (dashboard). Tiap <canvas data-chart> membawa datanya sendiri:
// data-tipe="doughnut|bar", data-label='["..."]', data-nilai='[1,2]', data-warna='["#hex"]' (berulang bila lebih pendek).
import Chart from 'chart.js/auto';

// Token DESIGN.md: ink-muted & krem-garis.
Chart.defaults.font.family = 'Nunito, sans-serif';
Chart.defaults.color = '#64766E';

document.querySelectorAll('canvas[data-chart]').forEach((canvas) => {
    const tipe = canvas.dataset.tipe;

    new Chart(canvas, {
        type: tipe,
        data: {
            labels: JSON.parse(canvas.dataset.label),
            datasets: [{
                label: canvas.dataset.satuan ?? 'Jumlah',
                data: JSON.parse(canvas.dataset.nilai),
                backgroundColor: JSON.parse(canvas.dataset.warna),
                borderColor: '#fff',
                borderWidth: tipe === 'doughnut' ? 4 : 0,
                borderRadius: tipe === 'bar' ? 8 : 0,
            }],
        },
        options: {
            maintainAspectRatio: false,
            cutout: tipe === 'doughnut' ? '65%' : undefined,
            // Doughnut memakai legenda HTML di Blade (dengan angka).
            plugins: { legend: { display: false } },
            scales: tipe === 'bar' ? {
                x: { grid: { display: false } },
                y: { beginAtZero: true, grid: { color: '#EDE5D8' }, border: { display: false } },
            } : {},
        },
    });
});
