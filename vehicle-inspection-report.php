<?php
/*
Plugin Name: Vehicle Inspection Report
Description: Complete vehicle inspection reporting tool
Version: 2.1
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
            <?php
            $components = [
                'Engine' => 100,
                'Transmission' => 100,
                'Body' => 100,
                'Tyres' => 100,
                'Hybrid Battery' => 100
            ];
            foreach ($components as $name => $value) :
                $id = strtolower(str_replace(' ', '-', $name));
            ?>
            <div class="vir-progress-item">
                <label><?php echo esc_html($name); ?>:</label>
                <input type="number" id="vir-<?php echo esc_attr($id); ?>" 
                       min="0" max="100" value="<?php echo esc_attr($value); ?>">
                <div class="vir-progress-bar" data-target="vir-<?php echo esc_attr($id); ?>"></div>
                <span class="vir-progress-value"><?php echo esc_html($value); ?>%</span>
            </div>
            <?php endforeach; ?>
        </div>
        
        <button id="vir-generate-report" class="button button-primary">Download Report</button>
    </div>
    
    <script>
    window.virPluginData = {
        imageUrl: '<?php echo esc_url(plugins_url('assets/car-top-view.png', __FILE__)); ?>',
        codes: <?php echo json_encode($codes); ?>,
        maxWidth: 2000 // Limits image size for better compatibility
    };
    </script>
    <?php
}

add_action('admin_enqueue_scripts', function($hook) {
    if ('toplevel_page_vehicle-inspection-report' !== $hook) return;
    
    wp_enqueue_style('vir-style', plugins_url('style.css', __FILE__));
    wp_enqueue_script('vir-script', plugins_url('script.js', __FILE__), ['jquery', 'jquery-ui-draggable'], '1.4', true);
});