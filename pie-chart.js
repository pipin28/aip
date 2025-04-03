const statusData = <?php echo json_encode($status_counts); ?>;

// Extract labels and values
const statusLabels = Object.keys(statusData);
const statusValues = Object.values(statusData);

// Define colors for each status
const statusColors = {
    'Pending': 'rgba(255, 223, 0, 0.8)', // Yellow
    'Evaluated': 'rgba(0, 123, 255, 0.8)', // Blue
    'Re-submission': 'rgba(255, 165, 0, 0.8)', // Orange
    'Approved': 'rgba(40, 167, 69, 0.8)' // Green
};

const chartColors = statusLabels.map(label => statusColors[label] || 'rgba(200, 200, 200, 0.8)');

// Create the pie chart
const pieCtx = document.getElementById('statusPieChart').getContext('2d');
new Chart(pieCtx, {
    type: 'pie',
    data: {
        labels: statusLabels,
        datasets: [{
            data: statusValues,
            backgroundColor: chartColors,
            borderColor: chartColors.map(color => color.replace('0.8', '1')),
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'top'
            }
        }
    }
});