jQuery(document).ready(function($) {
    const canvas = document.getElementById('vir-canvas');
    const ctx = canvas.getContext('2d');
    const carImg = new Image();
    let markers = [];
    let selectedMarker = null;
    
    // Set canvas dimensions
    function resizeCanvas() {
        const container = $('.vir-canvas-container');
        const maxWidth = Math.min(container.width(), window.virPluginData.maxWidth);
        canvas.width = maxWidth;
        
        // Calculate height based on image aspect ratio
        if (carImg.complete) {
            const imgRatio = carImg.height / carImg.width;
            canvas.height = maxWidth * imgRatio;
        } else {
            canvas.height = maxWidth * 0.6; // Default ratio until image loads
        }
        
        drawCanvas();
    }
    
    // Load car image
    carImg.onload = function() {
        resizeCanvas();
        $(window).on('resize', resizeCanvas);
    };
    carImg.src = window.virPluginData.imageUrl;

    // Add marker button
    $('#vir-add-code').on('click', function() {
        const fullCode = $('#vir-code-dropdown').val();
        markers.push({
            code: fullCode,
            x: canvas.width / 2,
            y: canvas.height / 2,
            color: '#FF0000',
            id: Date.now() + Math.random().toString(36).substr(2, 9)
        });
        drawCanvas();
    });

    // Clear all markers
    $('#vir-clear-markers').on('click', function() {
        if (confirm('Are you sure you want to clear all markers?')) {
            markers = [];
            drawCanvas();
        }
    });

    // Handle canvas interaction
    canvas.addEventListener('mousedown', function(e) {
        const pos = getCanvasPosition(e);
        
        for (let i = markers.length - 1; i >= 0; i--) {
            const marker = markers[i];
            const distance = Math.sqrt(Math.pow(pos.x - marker.x, 2) + Math.pow(pos.y - marker.y, 2));
            
            if (distance <= 15) {
                if (e.ctrlKey || e.metaKey) {
                    markers.splice(i, 1);
                    drawCanvas();
                    return;
                } else {
                    selectedMarker = marker;
                    return;
                }
            }
        }
        selectedMarker = null;
    });

    canvas.addEventListener('mousemove', function(e) {
        if (selectedMarker) {
            const pos = getCanvasPosition(e);
            selectedMarker.x = pos.x;
            selectedMarker.y = pos.y;
            drawCanvas();
        }
    });

    canvas.addEventListener('mouseup', function() {
        selectedMarker = null;
    });

    function getCanvasPosition(e) {
        const rect = canvas.getBoundingClientRect();
        const scaleX = canvas.width / rect.width;
        const scaleY = canvas.height / rect.height;
        return {
            x: (e.clientX - rect.left) * scaleX,
            y: (e.clientY - rect.top) * scaleY
        };
    }

    function drawCanvas() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        
        // Draw image maintaining aspect ratio
        const imgRatio = carImg.height / carImg.width;
        const displayHeight = canvas.width * imgRatio;
        ctx.drawImage(carImg, 0, 0, canvas.width, displayHeight);
        
        // Draw markers
        markers.forEach(marker => {
            ctx.beginPath();
            ctx.arc(marker.x, marker.y, 15, 0, Math.PI * 2);
            ctx.fillStyle = marker.color;
            ctx.fill();
            ctx.fillStyle = '#FFFFFF';
            ctx.font = 'bold 12px Arial';
            ctx.textAlign = 'center';
            ctx.fillText(marker.code, marker.x, marker.y + 5);
        });
    }

    // Generate report
    $('#vir-generate-report').on('click', function() {
        if (!carImg.complete) {
            alert('Please wait for image to load');
            return;
        }
        
        const reportCanvas = document.createElement('canvas');
        reportCanvas.width = Math.min(canvas.width, 2000);
        
        // Calculate proper height including report data
        const imgRatio = carImg.height / carImg.width;
        const imgDisplayHeight = reportCanvas.width * imgRatio;
        reportCanvas.height = imgDisplayHeight + 400; // Space for report data
        
        const reportCtx = reportCanvas.getContext('2d');
        
        // White background
        reportCtx.fillStyle = '#FFFFFF';
        reportCtx.fillRect(0, 0, reportCanvas.width, reportCanvas.height);
        
        // Draw vehicle image (full height)
        reportCtx.drawImage(canvas, 0, 0, reportCanvas.width, imgDisplayHeight);
        
        // Draw report data
        const startY = imgDisplayHeight + 40;
        reportCtx.fillStyle = '#000000';
        reportCtx.font = 'bold 24px Arial';
        reportCtx.fillText('Vehicle Inspection Report', 40, startY);
        
        // Progress bars
        const components = [
            { label: 'Engine', value: $('#vir-engine').val() },
            { label: 'Transmission', value: $('#vir-transmission').val() },
            { label: 'Body', value: $('#vir-body').val() },
            { label: 'Tyres', value: $('#vir-tyres').val() },
            { label: 'Hybrid Battery', value: $('#vir-hybrid-battery').val() }
        ];

        let yPos = startY + 60;
        const barWidth = reportCanvas.width - 240;
        
        components.forEach(component => {
            const percent = parseInt(component.value);

            reportCtx.font = '18px Arial';
            reportCtx.fillText(component.label + ':', 40, yPos);
            
            const barHeight = 24;
            const barY = yPos - barHeight + 5;
            
            reportCtx.fillStyle = '#EEEEEE';
            reportCtx.fillRect(180, barY, barWidth, barHeight);
            
            const fillWidth = (percent / 100) * barWidth;
            reportCtx.fillStyle = `rgb(${255 * (100 - percent) / 100}, ${255 * percent / 100}, 0)`;
            reportCtx.fillRect(180, barY, fillWidth, barHeight);
            
            reportCtx.fillStyle = '#000000';
            reportCtx.font = 'bold 16px Arial';
            reportCtx.fillText(`${percent}%`, 185 + barWidth, yPos);
            
            yPos += 50;
        });
        
        // Download
        const link = document.createElement('a');
        link.download = 'vehicle-inspection-report.png';
        link.href = reportCanvas.toDataURL('image/png');
        link.click();
    });

    // Update progress bars
    $('.vir-progress-item input').on('input', function() {
        const value = $(this).val();
        const percent = Math.min(100, Math.max(0, value));
        const target = $(this).siblings('.vir-progress-bar');
        const valueDisplay = $(this).siblings('.vir-progress-value');
        
        target.css('width', percent + '%');
        target.css('background-color', 
            `rgb(${255 * (100 - percent) / 100}, ${255 * percent / 100}, 0)`);
        valueDisplay.text(percent + '%');
    }).trigger('input');
});