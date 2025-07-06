/**
 * Custom Post Shortcode Creator - Meta Boxes JavaScript
 *
 * @package custom-post-shortcode-creator
 */

(function($) {
    'use strict';

    /**
     * Meta Boxes functionality
     */
    var CpscMetaBoxes = {

        /**
         * Initialize meta boxes functionality
         */
        init: function() {
            this.bindEvents();
        },

        /**
         * Bind event handlers
         */
        bindEvents: function() {
            // Handle location type change with modern WordPress patterns.
            $(document).on('change', '.cpsc-location-type-select', this.handleLocationTypeChange);
        },

        /**
         * Handle location type dropdown change
         *
         * @param {Event} e The change event.
         */
        handleLocationTypeChange: function(e) {
            var locationType = $(this).val();
            var $locationField = $('#cpsc-location-address-row');

            // Simple show/hide based on selection - no AJAX needed.
            if (locationType === 'offline') {
                $locationField.slideDown(200);
            } else {
                $locationField.slideUp(200);
            }
        }
    };

    // Initialize when document is ready.
    $(document).ready(function() {
        CpscMetaBoxes.init();
    });

})(jQuery); 