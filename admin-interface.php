<?php
/*
Plugin Name: Vehicle Inspection Report
Description: Complete vehicle inspection reporting tool
Version: 2.15d
Author: iUmer Farooq
*/

defined('ABSPATH') or die('Direct access not allowed');

add_action('admin_menu', function() {
    add_menu_page(
        'Vehicle Inspection',
        'Inspection Report',
        'manage_options',
        'vehicle-inspection-report',
        'vir_render_admin_page',
        'dashicons-car',
        30
    );
});

function vir_render_admin_page() {
    ?>
    <div class="vir-wrap">
        <h1>Vehicle Inspection Report</h1>
        
        <div class="vir-controls">
            <select id="vir-code-dropdown">
                <?php
                $codes = [
                    'G' => 'Genuine',
                    'Gs' => 'Minor Scratches',
                    'GS' => 'Major Scratches',
                    'Gd' => 'Minor Dent',
                    'GD' => 'Major Dent',
                    'P' => 'Painted',
                    'Pm' => 'Minor Paintwork',
                    'D' => 'Damaged',
                    'R' => 'Replaced',
                    'Cr' => 'Cracked',
                    'Rf' => 'Refinished',
                    'Ox' => 'Oxidized'
                ];
                foreach ($codes as $code => $label) :
                ?>
                <option value="<?php echo esc_attr($code); ?>"><?php echo esc_html($code); ?> - <?php echo esc_html($label); ?></option>
                <?php endforeach; ?>
            </select>
            <button id="vir-add-code" class="button">Add Marker</button>
            <button id="vir-clear-markers" class="button">Clear All Markers</button>
        </div>
        
        <div class="vir-canvas-container">
            <canvas id="vir-canvas" width="800" height="600"></canvas>
            <p class="vir-marker-instructions">Click to place markers. Ctrl+Click to delete.</p>
        </div>
        
        <div class="vir-progress-section">
            <h3>Component Condition</h3>
            <div class="vir-progress-item">
                <label>Engine:</label>
                <input type="number" id="vir-engine" min="0" max="100" value="100">
                <div class="vir-progress-bar" data-target="vir-engine"></div>
                <span class="vir-progress-value">100%</span>
            </div>
            <div class="vir-progress-item">
                <label>Transmission:</label>
                <input type="number" id="vir-transmission" min="0" max="100" value="100">
                <div class="vir-progress-bar" data-target="vir-transmission"></div>
                <span class="vir-progress-value">100%</span>
            </div>
            <div class="vir-progress-item">
                <label>Body:</label>
                <input type="number" id="vir-body" min="0" max="100" value="100">
                <div class="vir-progress-bar" data-target="vir-body"></div>
                <span class="vir-progress-value">100%</span>
            </div>
            <div class="vir-progress-item">
                <label>Tyres:</label>
                <input type="number" id="vir-tyres" min="0" max="100" value="100">
                <div class="vir-progress-bar" data-target="vir-tyres"></div>
                <span class="vir-progress-value">100%</span>
            </div>
            <div class="vir-progress-item">
                <label>Hybrid Battery:</label>
                <input type="number" id="vir-hybrid-battery" min="0" max="100" value="100">
                <div class="vir-progress-bar" data-target="vir-hybrid-battery"></div>
                <span class="vir-progress-value">100%</span>
            </div>
        </div>
        
        <button id="vir-generate-report" class="button button-primary">Download Report</button>
    </div>
    
    <script>
    window.virPluginData = {
        imageUrl: '<?php echo esc_url(plugins_url('assets/car-top-view.png', __FILE__)); ?>',
        maxWidth: 2000
    };
    </script>
    <?php
}

add_action('admin_enqueue_scripts', function($hook) {
    if ('toplevel_page_vehicle-inspection-report' !== $hook) return;
    
    wp_enqueue_style('vir-style', plugins_url('style.css', __FILE__));
    wp_enqueue_script('vir-script', plugins_url('script.js', __FILE__), ['jquery', 'jquery-ui-draggable'], '1.7', true);
});