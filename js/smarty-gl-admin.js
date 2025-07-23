
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
});
