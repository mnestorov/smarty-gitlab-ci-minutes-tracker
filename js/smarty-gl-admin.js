
jQuery(document).ready(function($) {
    // Add source functionality
    $(document).on("click", ".smarty-gl-add-source", function(e) {
        e.preventDefault();
        var $container = $(this).siblings(".smarty-gl-sources-container");
        var index = $container.find(".smarty-gl-source-row").length;
        var $newRow = $(`
            <div class="smarty-gl-source-row">
                <div class="smarty-gl-source-fields">
                    <div class="smarty-gl-source-field">
                        <label>Display Name</label>
                        <input type="text" name="smarty_gl_settings[sources][${index}][name]" placeholder="My Project" required />
                    </div>
                    <div class="smarty-gl-source-field">
                        <label>Type</label>
                        <select name="smarty_gl_settings[sources][${index}][type]" required>
                            <option value="group">Group</option>
                            <option value="namespace">Namespace</option>
                        </select>
                    </div>
                    <div class="smarty-gl-source-field">
                        <label>GitLab ID/Path</label>
                        <input type="text" name="smarty_gl_settings[sources][${index}][identifier]" placeholder="group-name or 12345" required />
                    </div>
                    <div class="smarty-gl-source-field">
                        <button type="button" class="smarty-gl-btn smarty-gl-btn-danger smarty-gl-remove-source">Remove</button>
                    </div>
                </div>
            </div>
        `);
        $container.append($newRow);
        
        // Add animation
        $newRow.hide().fadeIn(300);
    });
    
    // Remove source functionality
    $(document).on("click", ".smarty-gl-remove-source", function(e) {
        e.preventDefault();
        var $row = $(this).closest(".smarty-gl-source-row");
        $row.fadeOut(300, function() {
            $(this).remove();
        });
    });
    
    // Form validation
    $("form").on("submit", function() {
        var isValid = true;
        $(this).find("input[required], select[required]").each(function() {
            if (!$(this).val().trim()) {
                $(this).addClass("error");
                if (!$(this).siblings(".error-message").length) {
                    $(this).after('<div class="error-message">This field is required</div>');
                }
                isValid = false;
            } else {
                $(this).removeClass("error");
                $(this).siblings(".error-message").remove();
            }
        });
        return isValid;
    });
    
    // Clear errors on input
    $(document).on("input change", "input, select", function() {
        $(this).removeClass("error");
        $(this).siblings(".error-message").remove();
    });
    
    // Enhanced placeholder hints
    $(document).on("focus", "input[placeholder]", function() {
        $(this).data("placeholder", $(this).attr("placeholder"));
        $(this).attr("placeholder", "");
    });
    
    $(document).on("blur", "input[placeholder]", function() {
        if ($(this).data("placeholder")) {
            $(this).attr("placeholder", $(this).data("placeholder"));
        }
    });
    
    // Dashboard widget functionality - Clean external JS approach
    function initDashboardWidget() {
        var $triggers = $('.smarty-gl-trigger');
        
        if ($triggers.length === 0) {
            return; // No dashboard widget on this page
        }
        
        $triggers.each(function() {
            var $trigger = $(this);
            var action = $trigger.data('action');
            var nonce = $trigger.data('nonce');
            var ajaxUrl = (typeof smartyGLAdmin !== 'undefined' && smartyGLAdmin.ajaxurl) ? smartyGLAdmin.ajaxurl : ajaxurl;
            
            console.log('GitLab CI: Initializing', action, 'with nonce:', nonce);
            console.log('GitLab CI: Using AJAX URL:', ajaxUrl);
            
            if (action === 'background-update') {
                // Background update for cached data
                setTimeout(function() {
                    performAjaxRequest(ajaxUrl, nonce, true);
                }, 1000);
            } else if (action === 'initial-load') {
                // Initial load for no cached data
                performAjaxRequest(ajaxUrl, nonce, false);
            }
        });
    }
    
    function performAjaxRequest(ajaxUrl, nonce, isBackgroundUpdate) {
        console.log('GitLab CI: Performing AJAX request - Background:', isBackgroundUpdate);
        
        $.ajax({
            url: ajaxUrl,
            type: "POST",
            data: {
                action: "smarty_gl_dashboard_data",
                nonce: nonce
            },
            timeout: 15000,
            success: function(response) {
                console.log('GitLab CI: AJAX Success:', response);
                
                if (response.success) {
                    updateDashboardData(response.data, isBackgroundUpdate);
                } else {
                    showError(response.data || 'Failed to load GitLab data');
                }
            },
            error: function(xhr, status, error) {
                console.log('GitLab CI: AJAX Error:', status, error);
                handleAjaxError(status, error, isBackgroundUpdate);
            }
        });
    }
    
    function updateDashboardData(data, isBackgroundUpdate) {
        var $dataRows = $("#smarty-gl-data-rows");
        var $lastUpdated = $("#smarty-gl-last-updated");
        var $loading = $("#smarty-gl-loading");
        var $content = $("#smarty-gl-content");
        
        console.log('GitLab CI: Updating DOM - Elements found:', {
            dataRows: $dataRows.length,
            lastUpdated: $lastUpdated.length,
            loading: $loading.length,
            content: $content.length
        });
        
        if ($dataRows.length > 0) {
            $dataRows.html(data.html);
        }
        
        if ($lastUpdated.length > 0) {
            $lastUpdated.html('Last updated: ' + data.timestamp);
        }
        
        if (!isBackgroundUpdate) {
            // For initial load, show content and hide loading
            if ($loading.length > 0) {
                $loading.hide();
            }
            if ($content.length > 0) {
                $content.show();
            }
        }
        
        console.log('GitLab CI: DOM update complete');
    }
    
    function showError(message) {
        var $loading = $("#smarty-gl-loading");
        if ($loading.length > 0) {
            $loading.html(
                '<div class="smarty-gl-alert smarty-gl-alert-error">' +
                '<strong>Error:</strong> ' + message +
                '</div>'
            );
        }
    }
    
    function handleAjaxError(status, error, isBackgroundUpdate) {
        if (isBackgroundUpdate) {
            console.log('GitLab CI: Background update failed, ignoring silently');
            return;
        }
        
        var errorMsg = "Connection timeout or error";
        if (status === "timeout") {
            errorMsg = "Request timed out - GitLab API may be slow";
        }
        
        var $loading = $("#smarty-gl-loading");
        if ($loading.length > 0) {
            $loading.html(
                '<div class="smarty-gl-alert smarty-gl-alert-warning">' +
                '<strong>Notice:</strong> ' + errorMsg +
                ' <button onclick="location.reload()" class="smarty-gl-btn smarty-gl-btn-secondary" style="margin-left: 10px;">Retry</button>' +
                '</div>'
            );
        }
    }
    
    // Initialize dashboard widget when DOM is ready
    initDashboardWidget();
});
